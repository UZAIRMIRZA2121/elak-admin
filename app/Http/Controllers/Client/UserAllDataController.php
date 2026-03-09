<?php

namespace App\Http\Controllers\Client;

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
use Brian2694\Toastr\Facades\Toastr;

class UserAllDataController extends Controller
{

    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $client_id = auth('client')->id();

        $users = User::withCount('all_orders')
            ->where('client_id', $client_id)
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
        
        // New filter values
        $ref_code = $request->input('ref_code', null);
        $segment_id = $request->input('segment_id', null);
        $partner_id = $request->input('partner_id', null);
        $zone_id_filter = $request->input('zone_id_filter', null);
        $category_id = $request->input('category_id', null);

        $client_id = auth('client')->id();

        $userIds = User::where('client_id', $client_id)->pluck('id');
        $orders = Order::with(['customer.segment', 'store', 'zone'])
            ->whereIn('user_id', $userIds)
            ->when(isset($search), function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('customer', function ($cq) use ($search) {
                        $cq->where('f_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('l_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('username', 'LIKE', '%' . $search . '%');
                    })->orWhere('id', 'LIKE', '%' . $search . '%');
                });
            })
            // New Filters
            ->when($ref_code, function ($query) use ($ref_code) {
                return $query->whereHas('customer', function ($q) use ($ref_code) {
                    return $q->where('ref_code', 'like', "%{$ref_code}%");
                });
            })
            ->when($segment_id && $segment_id != 'all', function ($query) use ($segment_id) {
                return $query->whereHas('customer', function ($q) use ($segment_id) {
                    return $q->where('segment_id', $segment_id);
                });
            })
            ->when($partner_id && $partner_id != 'all', function ($query) use ($partner_id) {
                return $query->where('store_id', $partner_id);
            })
            ->when($zone_id_filter && $zone_id_filter != 'all', function ($query) use ($zone_id_filter) {
                return $query->where('zone_id', $zone_id_filter);
            })
            ->when($category_id && $category_id != 'all', function ($query) use ($category_id) {
                return $query->whereHas('details.item', function ($q) use ($category_id) {
                    return $q->whereHas('category', function ($cq) use ($category_id) {
                        return $cq->where('id', $category_id)->orWhere('parent_id', $category_id);
                    });
                });
            })
            ->latest()
            ->paginate(Config::get('default_pagination'));

        // Dropdown Data
        $segments = \App\Models\Segment::where('status', 'active')->orderBy('name')->get();
        // Stores belonging to users of this client? Or just all stores? 
        // Admin side showed all stores. Client side likely wants stores they interact with, 
        // but for now, I'll follow the "same as admin" logic for dropdowns if applicable.
        // Actually, stores are global.
        $partners = Store::active()->orderBy('name')->get(['id', 'name']);
        $zones = \App\Models\Zone::active()->orderBy('name')->get(['id', 'name']);
        $categories = \App\Models\Category::where(['position' => 0, 'status' => 1])->orderBy('name')->get(['id', 'name']);

        return view('client-views.voucher_list', compact(
            'orders', 'search', 'zones', 'segments', 'partners', 'categories',
            'ref_code', 'segment_id', 'partner_id', 'zone_id_filter', 'category_id'
        ));
    }

    public function edit($id)
    {
        $client_id = auth('client')->id();
        $user = User::where(['id' => $id, 'client_id' => $client_id])->first();
        if (!$user) {
            Toastr::error(translate('messages.user_not_found'));
            return back();
        }
        return view('client-views.user_edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'password' => 'nullable|min:6',
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
            'l_name.required' => translate('messages.last_name_is_required'),
        ]);

        $client_id = auth('client')->id(); 
        $user = User::where(['id' => $id, 'client_id' => $client_id])->first();
        
        if (!$user) {
            Toastr::error(translate('messages.user_not_found'));
            return back();
        }

        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;
        $user->username = $request->username;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        Toastr::success(translate('messages.user_updated_successfully'));
        return redirect()->route('all_user.user_data');
    }
}
