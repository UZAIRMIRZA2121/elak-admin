<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CyberSourceController extends Controller
{
    // =========================
    // 1. SHOW PAYMENT FORM
    // =========================
    public function form()
    {
        return view('cybersource.form');
    }

    // =========================
    // 2. PROCESS PAYMENT
    // =========================
    public function testPayment(Request $request)
    {
        try {

            // =========================
            // CONFIG
            // =========================
            $baseUrl = env('CYBERSOURCE_BASE_URL', 'https://apitest.cybersource.com');
            $merchantId = env('CYBERSOURCE_MERCHANT_ID');
            $keyId = env('CYBERSOURCE_KEY_ID');
            $secretKey = base64_decode(env('CYBERSOURCE_SECRET_KEY'));

            $endpoint = "/pts/v2/payments";
            $host = parse_url($baseUrl, PHP_URL_HOST);

            // Toggle Demo Mode
            $demoMode = true;

            // =========================
            // PAYLOAD
            // =========================
            $payload = [
                "clientReferenceInformation" => [
                    "code" => "order_" . time()
                ],
                "processingInformation" => [
                    "commerceIndicator" => "internet",
                    "capture" => true
                ],
                "orderInformation" => [
                    "amountDetails" => [
                        "totalAmount" => (string) ($request->amount ?? "10.00"),
                        "currency" => "JOD"
                    ],
                    "billTo" => [
                        "firstName" => $request->first_name ?? "John",
                        "lastName" => $request->last_name ?? "Doe",
                        "address1" => "1 Market St",
                        "locality" => "San Francisco",
                        "administrativeArea" => "CA",
                        "postalCode" => "94105",
                        "country" => "US",
                        "email" => $request->email ?? "test@example.com",
                        "phoneNumber" => "4158880000"
                    ]
                ],
                "paymentInformation" => [
                    "card" => [
                        "number" => $demoMode ? "4111111111111111" : $request->card_number,
                        "expirationMonth" => $demoMode ? "12" : $request->exp_month,
                        "expirationYear" => $demoMode ? "2030" : $request->exp_year,
                        "securityCode" => $demoMode ? "123" : $request->cvv
                    ]
                ]
            ];

            $payloadJson = json_encode($payload);

            // =========================
            // DIGEST
            // =========================
            $digest = base64_encode(hash('sha256', $payloadJson, true));
            $digestHeader = "SHA-256=" . $digest;

            $date = gmdate("D, d M Y H:i:s T");

            // =========================
            // SIGNATURE STRING
            // =========================
            $signatureString =
                "host: {$host}\n" .
                "date: {$date}\n" .
                "(request-target): post {$endpoint}\n" .
                "digest: {$digestHeader}\n" .
                "v-c-merchant-id: {$merchantId}";

            $signature = base64_encode(
                hash_hmac('sha256', $signatureString, $secretKey, true)
            );

            $signatureHeader =
                'keyid="' . $keyId . '",' .
                'algorithm="HmacSHA256",' .
                'headers="host date (request-target) digest v-c-merchant-id",' .
                'signature="' . $signature . '"';

            // =========================
            // HEADERS
            // =========================
            $headers = [
                "Content-Type" => "application/json",
                "v-c-merchant-id" => $merchantId,
                "Date" => $date,
                "Host" => $host,
                "Digest" => $digestHeader,
                "Signature" => $signatureHeader,
                "User-Agent" => "Laravel-App"
            ];

            // =========================
            // API REQUEST
            // =========================
            $response = Http::withHeaders($headers)
                ->withBody($payloadJson, 'application/json')
                ->post($baseUrl . $endpoint);

            $status = $response->status();
            $body = json_decode($response->body(), true);

            // =========================
            // LOGGING
            // =========================
            Log::info('CyberSource Transaction', [
                'url' => $baseUrl . $endpoint,
                'headers' => $headers,
                'request' => $payload,
                'status' => $status,
                'response' => $body
            ]);

            // =========================
            // RESPONSE HANDLING
            // =========================
            if (in_array($status, [200, 201])) {

                return redirect()->back()->with([
                    'success' => 'Payment Successful',
                    'transaction_id' => $body['id'] ?? null
                ]);
            }

            return redirect()->back()->with(
                'error',
                'Payment Failed: ' . ($body['message'] ?? 'Unknown error')
            );

        } catch (\Exception $e) {

            Log::error('CyberSource Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with(
                'error',
                'Exception: ' . $e->getMessage()
            );
        }
    }
    // =========================
    // 3. LOG VIEWER (OPTIONAL)
    // =========================
    public function logs()
    {
        $logs = file(storage_path('logs/laravel.log'));

        return view('cybersource.logs', compact('logs'));
    }




}