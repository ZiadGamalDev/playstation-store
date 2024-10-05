<?php

namespace App\Http\Controllers;

use App\Http\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(protected WhatsAppService $whatsAppService) {}

    public function sendMessage(): JsonResponse
    {
        $to = '201149579978';
        $templateName = 'price_alert';
        $templateVariables = [];

        $response = $this->whatsAppService->sendMessage($to, $templateName, $templateVariables);

        return response()->json($response);
    }

    public function sendMessageCurl(): JsonResponse
    {
        $to = '201026264486';
        $postData = json_encode([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => 'price_alert',
                'language' => [
                    'code' => 'en',
                ],
            ],
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://graph.facebook.com/v20.0/' . env('WHATSAPP_PHONE_NUMBER_ID') . '/messages',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . env('WHATSAPP_ACCESS_TOKEN'),
                'Content-Type: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = 'Curl error: ' . curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error], 500);
        }

        curl_close($curl);
        return response()->json(json_decode($response, true));
    }
}
