<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Cart;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ItemCampaign;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CartController extends Controller
{

    public function status_cart(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'cart_id' => 'required',
                'status' => 'required|in:pending,approved,rejected,status_cart',
            ],
            [
                'status.in' => 'Status value invalid hai.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $cart = Cart::find($request->cart_id);
        $cart->status = $request->status;
        $cart->save();
        return response()->json($cart, 200);
    }

    public function get_carts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;
        $carts = Cart::where('user_id', $user_id)->where('is_guest', $is_guest)->where('module_id', $request->header('moduleId'))->get()
            ->map(function ($data) {
                $data->add_on_ids = json_decode($data->add_on_ids, true);
                $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                $data->variation = json_decode($data->variation, true);
                $data->item = Helpers::cart_product_data_formatting(
                    $data->item,
                    $data->variation,
                    $data->add_on_ids,
                    $data->add_on_qtys,
                    false,
                    app()->getLocale()
                );
                return $data;
            });
        return response()->json($carts, 200);
    }

    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => $request->user ? 'nullable' : 'required',
            'item_id' => 'required|integer',
            'cart_group' => 'required|string',
            'model' => 'required|string|in:Item,ItemCampaign',
            'price' => 'required|numeric',
            'total_price' => 'nullable|numeric',
            'offer_type' => 'nullable',
            'discount_amount' => 'nullable|numeric',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;
        $model = $request->model === 'Item' ? 'App\Models\Item' : 'App\Models\ItemCampaign';
        $item = $request->model === 'Item' ? Item::find($request->item_id) : ItemCampaign::find($request->item_id);


        $cart = Cart::where('item_id', $request->item_id)->where('item_type', $model)->where('user_id', $user_id)->where('is_guest', $is_guest)->where('module_id', $request->header('moduleId'))->first();

        if ($cart && json_decode($cart->variation, true) == $request->variation) {

            return response()->json([
                'errors' => [
                    ['code' => 'cart_item', 'message' => translate('messages.Item_already_exists')]
                ]
            ], 403);
        }

        if ($item->maximum_cart_quantity && ($request->quantity > $item->maximum_cart_quantity)) {
            return response()->json([
                'errors' => [
                    ['code' => 'cart_item_limit', 'message' => translate('messages.maximum_cart_quantity_exceeded')]
                ]
            ], 403);
        }
        if ($item->voucher_ids === 'Flat discount') {
            $config = json_decode($item->discount_configuration, true); // Decode JSON to array

            $price = $request->price;
            $selectedTier = null;

            // Loop through config to find matching tier
            foreach ($config as $row) {
                if ($price >= $row['min_amount'] && $price <= $row['max_amount']) {
                    $selectedTier = $row;
                    break;
                }
            }

            // Check if a tier was found
            if ($selectedTier) {
                // Price is valid, continue processing
                // Example: calculate bonus
                $bonus = ($price * $selectedTier['bonus_percentage']) / 100;
                $totalPaid = $price + $bonus;

                // Continue your logic
                // dd($selectedTier, $bonus, $totalPaid);
            } else {
                // Price does not fall in any tier, return error
                return response()->json([
                    'status' => 'error',
                    'message' => 'The price does not qualify for any discount tier for this voucher.'
                ], 400);
            }
        }

        $cart = new Cart();
        $cart->cart_group = $request->cart_group; // ✅ new column
        $cart->store_id = ($item->voucher_ids === 'Flat discount') ? $request->store_id : null;
        $cart->user_id = $user_id;
        $cart->module_id = $request->header('moduleId');
        $cart->item_id = $request->item_id;
        $cart->is_guest = $is_guest;
        $cart->add_on_ids = isset($request->add_on_ids) ? json_encode($request->add_on_ids) : json_encode([]);
        $cart->add_on_qtys = isset($request->add_on_qtys) ? json_encode($request->add_on_qtys) : json_encode([]);
        $cart->item_type = $request->model;
      
        $cart->quantity = $request->quantity;
        $cart->variation = isset($request->variation) ? json_encode($request->variation) : json_encode([]);
        $cart->status = ($item->voucher_ids === 'Flat discount') ? 'pending' : null;
        $cart->type = $item->voucher_ids ?? null;
        $cart->gift_details = $request->gift_details ?? null;

//   $result = calculate_discount(2000, , 10);

//         dd($result);

        
        $cart->price = $request->price;
        $cart->is_paid = $request->price > 0 ? 1 : 0; // Mark as paid if price is greater than 0, otherwise not paid

        $cart->total_price = $request->total_price ?? null;
        $cart->offer_type = $request->offer_type ?? null;
        $cart->discount_amount = $request->discount_amount ?? null;

        $cart->save();

        $item->carts()->save($cart);

        $carts = Cart::where('user_id', $user_id)->where('is_guest', $is_guest)->where('module_id', $request->header('moduleId'))->get()
            ->map(function ($data) {
                $data->add_on_ids = json_decode($data->add_on_ids, true);
                $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                $data->variation = json_decode($data->variation, true);
                $data->item = Helpers::cart_product_data_formatting(
                    $data->item,
                    $data->variation,
                    $data->add_on_ids,
                    $data->add_on_qtys,
                    false,
                    app()->getLocale()
                );
                return $data;
            });


        if ($cart->status === 'pending') {

            // Time limit = 5 minutes
            $expireTime = Carbon::parse($cart->created_at)->addMinutes(5);
            $now = Carbon::now();

            // ✅ If already expired → auto mark not responded
            if ($now->greaterThanOrEqualTo($expireTime)) {

                $cart->status = 'not_responded';
                $cart->save();

                return response()->json([
                    'cart_id' => $cart->id,
                    'status' => 'not_responded',
                    'message' => 'Vendor did not respond in time'
                ], 408);
            }

            // ✅ Otherwise wait until expire or vendor response
            $maxWait = $expireTime->diffInSeconds($now);
            $interval = 5;
            $elapsed = 0;

            while ($elapsed < $maxWait) {

                sleep($interval);
                $elapsed += $interval;

                $cart->refresh();

                // Vendor responded
                if ($cart->status !== 'pending') {

                    return response()->json([
                        'cart_id' => $cart->id,
                        'status' => $cart->status,
                        'message' => 'Vendor responded'
                    ], 200);
                }
            }

            // ✅ Time finished → auto update
            $cart->status = 'not_responded';
            $cart->save();

            return response()->json([
                'cart_id' => $cart->id,
                'status' => 'not_responded',
                'message' => 'Vendor did not respond in time'
            ], 408);
        }
        return response()->json($carts, 200);

    }

    public function update_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'guest_id' => $request->user ? 'nullable' : 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;
        $cart = Cart::find($request->cart_id);
        $item = $cart->item_type === 'App\Models\Item' ? Item::find($cart->item_id) : ItemCampaign::find($cart->item_id);
        if ($item->maximum_cart_quantity && ($request->quantity > $item->maximum_cart_quantity)) {
            return response()->json([
                'errors' => [
                    ['code' => 'cart_item_limit', 'message' => translate('messages.maximum_cart_quantity_exceeded')]
                ]
            ], 403);
        }

        $cart->user_id = $user_id;
        $cart->module_id = $request->header('moduleId');
        $cart->is_guest = $is_guest;
        $cart->add_on_ids = isset($request->add_on_ids) ? json_encode($request->add_on_ids) : $cart->add_on_ids;
        $cart->add_on_qtys = isset($request->add_on_qtys) ? json_encode($request->add_on_qtys) : $cart->add_on_qtys;
        $cart->price = $request->price;
        $cart->quantity = $request->quantity;
        $cart->variation = isset($request->variation) ? json_encode($request->variation) : $cart->variation;
        $cart->save();

        $carts = Cart::where('user_id', $user_id)->where('is_guest', $is_guest)->where('module_id', $request->header('moduleId'))->get()
            ->map(function ($data) {
                $data->add_on_ids = json_decode($data->add_on_ids, true);
                $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                $data->variation = json_decode($data->variation, true);
                $data->item = Helpers::cart_product_data_formatting(
                    $data->item,
                    $data->variation,
                    $data->add_on_ids,
                    $data->add_on_qtys,
                    false,
                    app()->getLocale()
                );
                return $data;
            });
        return response()->json($carts, 200);
    }

    public function remove_cart_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required',
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;

        $cart = Cart::find($request->cart_id);
        $cart?->delete();

        $carts = Cart::where('user_id', $user_id)->where('is_guest', $is_guest)->where('module_id', $request->header('moduleId'))->get()
            ->map(function ($data) {
                $data->add_on_ids = json_decode($data->add_on_ids, true);
                $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                $data->variation = json_decode($data->variation, true);
                $data->item = Helpers::cart_product_data_formatting(
                    $data->item,
                    $data->variation,
                    $data->add_on_ids,
                    $data->add_on_qtys,
                    false,
                    app()->getLocale()
                );
                return $data;
            });
        return response()->json($carts, 200);
    }

    public function remove_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => $request->user ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request->user ? $request->user->id : $request['guest_id'];
        $is_guest = $request->user ? 0 : 1;

        $carts = Cart::where('user_id', $user_id)->where('is_guest', $is_guest)->where('module_id', $request->header('moduleId'))->get();

        foreach ($carts as $cart) {
            $cart?->delete();
        }


        $carts = Cart::where('user_id', $user_id)->where('is_guest', $is_guest)->where('module_id', $request->header('moduleId'))->get()
            ->map(function ($data) {
                $data->add_on_ids = json_decode($data->add_on_ids, true);
                $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                $data->variation = json_decode($data->variation, true);
                $data->item = Helpers::cart_product_data_formatting(
                    $data->item,
                    $data->variation,
                    $data->add_on_ids,
                    $data->add_on_qtys,
                    false,
                    app()->getLocale()
                );
                return $data;
            });
        return response()->json($carts, 200);
    }
}
