<?php

namespace App\Http\Controllers;

use App\CentralLogics\OrderLogic;
use App\Models\OrderPayment;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Library\Payer;
use App\Traits\Payment;
use App\Library\Receiver;
use App\Library\Payment as PaymentInfo;

class PaymentController extends Controller
{
    public function __construct()
    {
        if (is_dir('App\Traits') && trait_exists('App\Traits\Payment')) {
            $this->extendWithPaymentGatewayTrait();
        }
    }

    private function extendWithPaymentGatewayTrait()
    {
        $extendedControllerClass = $this->generateExtendedControllerClass();

        // Only declare if it doesn't exist
        if (!class_exists('ExtendedController')) {
            eval ($extendedControllerClass);
        }
    }

    private function generateExtendedControllerClass()
    {
        $baseControllerClass = get_class($this);
        $traitClassName = 'App\Traits\Payment';

        $extendedControllerClass = "
            class ExtendedController extends $baseControllerClass {
                use $traitClassName;
            }
        ";

        return $extendedControllerClass;
    }
    public function payment(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['id' => $request->order_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('payment_platform', $request['payment_platform']);
        session()->put('order_id', $request->order_id);

        $order = Order::where(['id' => $request->order_id, 'user_id' => $request['customer_id']])->first();
        if (!$order) {
            return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
        }
        if ($order->is_guest) {
            $customer_details = json_decode($order['delivery_address'], true);
        } else {
            $customer = User::find($request['customer_id']);
        }

        //guest user check
        if ($order->is_guest) {
            $address = json_decode($order['delivery_address'], true);
            $customer = collect([
                'first_name' => $address['contact_person_name'],
                'last_name' => '',
                'phone' => $address['contact_person_number'],
                'email' => $address['contact_person_email'],
            ]);

        } else {
            $customer = User::find($request['customer_id']);
            $customer = collect([
                'first_name' => $customer['f_name'],
                'last_name' => $customer['l_name'],
                'phone' => $customer['phone'],
                'email' => $customer['email'],
            ]);
        }


        if (session()->has('payment_method') == false) {
            session()->put('payment_method', 'ssl_commerz_payment');
        }

        $order_amount = $order->order_amount - $order->partially_paid_amount;

        if (!isset($customer)) {
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        }

        if (!isset($order_amount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }

        $payer = new Payer($customer['first_name'] . ' ' . $customer['last_name'], $customer['email'], $customer['phone'], '');

        $currency = BusinessSetting::where(['key' => 'currency'])->first()->value;

        $store_logo = BusinessSetting::where(['key' => 'logo'])->first();
        $additional_data = [
            'business_name' => BusinessSetting::where(['key' => 'business_name'])->first()?->value,
            'business_logo' => \App\CentralLogics\Helpers::get_full_url('business', $store_logo?->value, $store_logo?->storage[0]?->value ?? 'public')
        ];

        $payment_info = new PaymentInfo(
            success_hook: 'order_place',
            failure_hook: 'order_failed',
            currency_code: $currency,
            payment_method: $request->payment_method,
            payment_platform: $request['payment_platform'],
            payer_id: $request['customer_id'],
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $order_amount,
            external_redirect_link: $request->has('callback') ? $request['callback'] : session('callback'),
            attribute: 'order',
            attribute_id: $order->id
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return redirect($redirect_link);

    }

    public function success()
    {

        $order = Order::where(['id' => session('order_id'), 'user_id' => session('customer_id')])->first();
        
        if ($order->voucher_type == 'Flat discount') {
            if ($order->transaction == null) {
                $unpaid_payment = OrderPayment::where('payment_status', 'unpaid')->where('order_id', $order->id)->first()?->payment_method;
                $unpaid_pay_method = 'digital_payment';
                if ($unpaid_payment) {
                    $unpaid_pay_method = $unpaid_payment;
                }
                if ($order->payment_method == 'cash_on_delivery' || $unpaid_pay_method == 'cash_on_delivery') {
                    $ol = OrderLogic::create_transaction($order, 'store', null);
                } else {
                    $ol = OrderLogic::create_transaction($order, 'admin', null);
                }
                if (!$ol) {
                    Toastr::warning(translate('messages.faield_to_create_order_transaction'));
                    return back();
                }
                        $order->order_status = 'delivered';
        $order->delivered = Carbon::now();
        $order->save();
            }

            $order->payment_status = 'paid';

            OrderLogic::update_unpaid_order_payment(order_id: $order->id, payment_method: $order->payment_method);


        }


        if (isset($order) && $order->callback != null) {
            return redirect($order->callback . '&status=success');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        $order = Order::where(['id' => session('order_id'), 'user_id' => session('customer_id')])->first();
        if (isset($order) && $order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }
    public function cancel(Request $request)
    {
        $order = Order::where(['id' => session('order_id'), 'user_id' => session('customer_id')])->first();
        if (isset($order) && $order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }

}
