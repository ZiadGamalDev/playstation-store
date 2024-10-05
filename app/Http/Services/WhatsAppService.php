<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

class WhatsAppService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('WHATSAPP_API_URL'),
            'headers' => [
                'Authorization' => 'Bearer ' . env('WHATSAPP_ACCESS_TOKEN'),
                'Content-Type' => 'application/json',
            ],
            'verify' => false, // Disable SSL verification for local development
        ]);
    }

    /**
     * Send a WhatsApp template message.
     *
     * @param string $to Recipient phone number
     * @param string $templateName Template name
     * @param array $templateVariables Optional template variables
     * @return array Response from the API
     */
    public function sendMessage(string $to, string $templateName, array $templateVariables = []): array
    {
        $body = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => 'en', // Use 'en' for general English
                ],
            ],
        ];

        // Add components if variables are provided
        if (!empty($templateVariables)) {
            $body['template']['components'] = [
                [
                    'type' => 'body',
                    'parameters' => $templateVariables,
                ],
            ];
        }

        $response = $this->client->post('/' . env('WHATSAPP_PHONE_NUMBER_ID') . '/messages', [
            'json' => $body,
        ]);

        return json_decode($response->getBody(), true);
    }
}
