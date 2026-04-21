<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;// ✅ YE LINE IMPORTANT

class CyberSourceController extends Controller
{
    public function testPayment(Request $request)
    {
        $payload = [
            "clientReferenceInformation" => [
                "code" => "order_" . time()
            ],
            "processingInformation" => [
                "commerceIndicator" => "internet",
                "capture" => false
            ],
            "orderInformation" => [
                "amountDetails" => [
                    "totalAmount" => $request->amount ?? "10.00",
                    "currency" => "USD"
                ],
                "billTo" => [
                    "firstName" => "Test",
                    "lastName" => "User",
                    "address1" => "Street 1",
                    "locality" => "Faisalabad",
                    "administrativeArea" => "PB",
                    "postalCode" => "38000",
                    "country" => "PK",
                    "email" => "test@test.com",
                    "phoneNumber" => "03001234567"
                ]
            ],
            "paymentInformation" => [
                "card" => [
                    "number" => "4111111111111111",
                    "expirationMonth" => "12",
                    "expirationYear" => "2030",
                    "securityCode" => "123",
                    "type" => "001" // Visa
                ]
            ]
        ];

        $payloadJson = json_encode($payload);

        // Digest
        $digest = base64_encode(hash('sha256', $payloadJson, true));
        $digestHeader = "SHA-256=" . $digest;

        // Date
        $date = gmdate("D, d M Y H:i:s T");

        // Signature String
        $signatureString = "host: apitest.cybersource.com\n";
        $signatureString .= "date: $date\n";
        $signatureString .= "(request-target): post /pts/v2/payments\n";
        $signatureString .= "digest: $digestHeader\n";
        $signatureString .= "v-c-merchant-id: " . env('CYBERSOURCE_MERCHANT_ID');

        // Signature
        $signature = base64_encode(hash_hmac(
            'sha256',
            $signatureString,
            base64_decode(env('CYBERSOURCE_SECRET_KEY')),
            true
        ));

        // Headers
        $headers = [
            "Content-Type" => "application/json",
            "v-c-merchant-id" => env('CYBERSOURCE_MERCHANT_ID'),
            "Date" => $date,
            "Host" => "apitest.cybersource.com",
            "Digest" => $digestHeader,
            "Signature" => 'keyid="' . env('CYBERSOURCE_KEY_ID') . '", algorithm="HmacSHA256", headers="host date (request-target) digest v-c-merchant-id", signature="' . $signature . '"'
        ];

        $response = Http::withHeaders($headers)
            ->withBody($payloadJson, 'application/json')
            ->post("https://apitest.cybersource.com/pts/v2/payments");

        return response()->json($response->json());
    }
}