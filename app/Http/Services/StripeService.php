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

    public function createCheckoutSession($amount, $currency, $orderId)
    {
        try {
            $session = $this->stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Order #' . $orderId,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['order' => $orderId]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
            ]);

            return $session;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Stripe Checkout session: ' . $e->getMessage());
        }
    }

    public function retrieveCheckoutSession($sessionId)
    {
        try {
            return $this->stripe->checkout->sessions->retrieve($sessionId);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve Stripe Checkout session: ' . $e->getMessage());
        }
    }

}
