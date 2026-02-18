<?php

namespace App\Http\Controllers\client;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Store;
use App\Models\Review;
use App\Models\Wishlist;
use App\Scopes\ZoneScope;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\OrderTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class UserAllDataController extends Controller
{

    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $client_id = auth('client')->id();

        $users = User::where('client_id', $client_id)
            ->when(isset($search), function ($query) use ($search) {
                $keys = explode(' ', $search);
                foreach ($keys as $key) {
                    $query->orWhere('f_name', 'LIKE', '%' . $key . '%')
                        ->orWhere('l_name', 'LIKE', '%' . $key . '%')
                        ->orWhere('phone', 'LIKE', '%' . $key . '%')
                        ->orWhere('email', 'LIKE', '%' . $key . '%');
                }
            })
            ->latest()
            ->paginate(Config::get('default_pagination'));

        if (isset($search)) {
            $query_param = ['search' => $search];
        }

        return view("client-views.user_management", compact('users', 'search'));
    }
    public function notification_show(Request $request)
    {
        // dd("fdbdfbdf");
        return view("client-views.usernotification");

    }

    public function voucher_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $store_name = $request['store_name'];
        $zone_id = $request['zone_id'];
        $client_id = auth('client')->id();

        $userIds = User::where('client_id', $client_id)->pluck('id');
        $orders = Order::with(['customer.segment', 'store', 'zone'])
            ->whereIn('user_id', $userIds)
            ->when(isset($search), function ($query) use ($search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('f_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('l_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('username', 'LIKE', '%' . $search . '%');
                })->orWhere('id', 'LIKE', '%' . $search . '%');
            })
            ->when(isset($store_name), function ($query) use ($store_name) {
                $query->whereHas('store', function ($q) use ($store_name) {
                    $q->where('name', 'LIKE', '%' . $store_name . '%');
                });
            })
            ->when(isset($zone_id), function ($query) use ($zone_id) {
                $query->where('zone_id', $zone_id);
            })
            ->latest()
            ->paginate(Config::get('default_pagination'));

        $zones = \App\Models\Zone::active()->get();

        return view('client-views.voucher_list', compact('orders', 'search', 'zones', 'store_name', 'zone_id'));
    }
}
