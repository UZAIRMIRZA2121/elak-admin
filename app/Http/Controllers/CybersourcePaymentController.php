<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CybersourcePaymentController extends Controller
{
    private function getConfig()
    {
        $payment = Setting::where('key_name', 'cybersource')->first();

        if (!$payment) {
            throw new \Exception('Cybersource configuration not found');
        }

        $configData = $payment->mode === 'live'
            ? json_decode($payment->live_values, true)
            : json_decode($payment->test_values, true);

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
        $data = [
            'amount' => session('payment_amount', 100),
            'currency' => session('payment_currency', 'USD'),
            'customer_name' => auth()->user()->name ?? 'Guest',
            'customer_email' => auth()->user()->email ?? 'guest@example.com',
        ];

        return view('payment-views.cybersource', compact('data'));
    }

    public function payment_process_3d(Request $request)
    {
        $config = $this->getConfig();

        $payload = [
            "targetOrigins" => [
                url('/')
            ],
            "clientVersion" => "0.21"
        ];

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'v-c-merchant-id' => $config['merchant_id'],
        ])->withBasicAuth(
                $config['api_key'],
                $config['secret_key']
            )->post(
                $config['base_url'] . '/up/v1/capture-contexts',
                $payload
            );

        return response()->json($response->json());
    }

    public function success(Request $request)
    {
        return view('payment.success', [
            'message' => 'Payment completed successfully',
        ]);
    }

    public function canceled(Request $request)
    {
        return view('payment.failed', [
            'message' => 'Payment was canceled',
        ]);
    }


}