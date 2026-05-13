<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CybersourcePaymentController extends Controller
{
    private function getConfig()
    {

        $payment = Setting::where('key_name', 'cybersource')->first();

        if (!$payment) {
            throw new \Exception('Cybersource configuration not found');
        }
        // If live_values/test_values are already cast to array in the model,
// no need to call json_decode() again.

        $configData = $payment->mode === 'live'
            ? $payment->live_values
            : $payment->test_values;

        // If the values are strings, decode them.
// If they are already arrays, use them directly.
        if (is_string($configData)) {
            $configData = json_decode($configData, true);
        }

        // Optional: ensure it's always an array
        $configData = is_array($configData) ? $configData : [];



        if (!$configData || ($configData['status'] ?? 0) != 1) {
            throw new \Exception('Cybersource gateway is disabled');
        }

        return [
            'merchant_id' => $configData['merchant_id'] ?? null,
            'api_key' => $configData['api_key'] ?? null,
            'secret_key' => $configData['secret_key'] ?? null,
            'base_url' => $configData['base_url'] ?? null,
            'mode' => $configData['mode'] ?? 'test',
            'status' => $configData['status'] ?? 0,
        ];
    }

    private function getHeaders()
    {
        $config = $this->getConfig();

        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'v-c-merchant-id' => $config['merchant_id'],
        ];
    }

    public function index(Request $request)
    {
        $payment_req = PaymentRequest::where('id', $request->payment_id)->firstOrFail();

        $additionalData = json_decode($payment_req->additional_data, true);
        $payerInformation = json_decode($payment_req->payer_information, true);
        $receiverInformation = json_decode($payment_req->receiver_information, true);

        $data = [
            'payment_request_id' => $payment_req->id,
            'payer_id' => $payment_req->payer_id,
            'receiver_id' => $payment_req->receiver_id,
            'amount' => $payment_req->payment_amount,
            'currency' => $payment_req->currency_code,
            'payment_method' => $payment_req->payment_method,
            'success_hook' => $payment_req->success_hook,
            'failure_hook' => $payment_req->failure_hook,
            'transaction_id' => $payment_req->transaction_id,
            'attribute_id' => $payment_req->attribute_id,
            'attribute' => $payment_req->attribute,
            'payment_platform' => $payment_req->payment_platform,

            // Business Info
            'business_name' => $additionalData['business_name'] ?? 'Shop',
            'business_logo' => $additionalData['business_logo'] ?? asset('assets/admin/img/160x160/img2.jpg'),

            // Customer Info
            'customer_name' => $payerInformation['name'] ?? 'Guest',
            'customer_email' => $payerInformation['email'] ?? 'guest@example.com',
            'customer_phone' => $payerInformation['phone'] ?? '',
            'customer_address' => $payerInformation['address'] ?? '',

            // Receiver Info
            'receiver_name' => $receiverInformation['name'] ?? '',
            'receiver_image' => $receiverInformation['image'] ?? '',
        ];

        return response()->json([
            'success' => true,
            'message' => 'Payment request fetched successfully',
            'data' => $data,

        ]);
    }


    public function payment_process_3d(Request $request)
    {
     
        try {
            // Get payment request by payment_id from URL
            $payment_req = PaymentRequest::where('id', $request->payment_id)->firstOrFail();

            // Decode JSON fields
            $additionalData = json_decode($payment_req->additional_data, true);
            $payerInformation = json_decode($payment_req->payer_information, true);
            $receiverInformation = json_decode($payment_req->receiver_information, true);


            // =========================
            // CONFIG
            // =========================
            $config = $this->getConfig();



            // =========================
            // CONFIG
            // =========================
            $baseUrl = $config['base_url'];
            $merchantId = $config['merchant_id'];
            $keyId = $config['api_key'];
            $secretKey = base64_decode($config['secret_key']);

            $endpoint = "/pts/v2/payments";
            $host = parse_url($baseUrl, PHP_URL_HOST);

            // Toggle Demo Mode
            $demoMode = $payment_req->mode == 'live' ? true : false;

            $expiry_date = $request->expiry_date;

            [$month, $year] = explode('/', $expiry_date);
            $year = '20' . $year;

            // dd($demoMode, $month, $year);
            //  dd($payment_req);
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
                        "totalAmount" => (string) ($payment_req->payment_amount ?? "10.00"),
                        "currency" => $payment_req->currency_code ?? "JOD"
                    ],
                    "billTo" => [
                        "firstName" => $payerInformation['name'] ?? "John",
                        "lastName" => $payerInformation['name'] ?? "demo",
                        "address1" => !empty($payerInformation['address'])
                            ? $payerInformation['address']
                            : "1 Market St",
                        "locality" => "San Francisco",
                        "administrativeArea" => "CA",
                        "postalCode" => "94105",
                        "country" => "US",
                        "email" => $payerInformation['email'] ?? "test@example.com",
                        "phoneNumber" => $payerInformation['phone']
                    ]
                ],
                "paymentInformation" => [
                    "card" => [
                        "number" => $demoMode ? "4111111111111111" : $request->card_number,
                        "expirationMonth" => $demoMode ? "12" : $month,
                        "expirationYear" => $demoMode ? "2030" : $year,
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

            if (in_array($status, [200, 201])) {

                $payment_req->transaction_id = $body['id'] ?? null;
                $payment_req->is_paid = 1;
                $payment_req->save();

                $order = Order::where('id', $payment_req->attribute_id)->first();

                if ($order) {
                    $order->payment_status = 'paid';
                    $order->payment_method = $payment_req->payment_method;
                    $order->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment completed successfully',
                    'transaction_id' => $body['id'] ?? null,
                    'payment_status' => 'paid'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment Failed: ' . ($body['message'] ?? 'Unknown error'),
                'payment_status' => 'failed'
            ], 400);

        } catch (\Exception $e) {

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function success(Request $request)
    {

        return view('payment-views.cybersource.success', [
            'message' => 'Payment completed successfully',
        ]);
    }

    public function canceled(Request $request)
    {
        return view('payment-views.cybersource.failed', [
            'message' => session('error') ?? 'Payment was canceled',
        ]);
    }

}