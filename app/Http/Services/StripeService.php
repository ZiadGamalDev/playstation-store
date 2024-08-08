<?php

namespace App\Http\Services;

use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function createToken(array $card)
    {
        try {
            $token = $this->stripe->tokens->create([
                'card' => [
                    'name' => $card['name'],
                    'number' => $card['number'],
                    'exp_month' => $card['exp_month'],
                    'exp_year' => $card['exp_year'],
                    'cvc' => $card['cvc'],
                ],
            ]);

            return $token->id;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Stripe token: ' . $e->getMessage());
        }
    }

    public function createCharge($amount, $currency, $source)
    {
        try {
            $charge = $this->stripe->charges->create([
                "amount" => $amount,
                "currency" => $currency,
                "source" => $source,
            ]);

            return $charge;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Stripe charge: ' . $e->getMessage());
        }
    }
}
