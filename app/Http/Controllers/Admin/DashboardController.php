<?php

namespace App\Http\Controllers\Admin;

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

class DashboardController extends Controller
{

    public function __construct()
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
    }
    public function user_dashboard(Request $request)
    {
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => Config::get('module.current_module_id'),
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
            'business_overview' => $request['business_overview'] ?? 'overall',
        ];

        session()->put('dash_params', $params);
        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $delivery_commission = $data['delivery_commission'];
        $customers = User::zone($params['zone_id'])->take(2)->get();

        $delivery_man = DeliveryMan::with('last_location')->when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()
            ->limit(2)->get('image');

        $active_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->Active()->count();

        $inactive_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->where('application_status', 'approved')->where('active', 0)->count();

        $blocked_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->where('application_status', 'approved')->where('status', 0)->count();

        $newly_joined_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'))->count();

        $reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params) {
                return $query->where('zone_id', $params['zone_id']);
            });
        })->count();

        $positive_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params) {
                return $query->where('zone_id', $params['zone_id']);
            });
        })->whereIn('rating', [4, 5])->get()->count();
        $good_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params) {
                return $query->where('zone_id', $params['zone_id']);
            });
        })->where('rating', 3)->count();
        $neutral_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params) {
                return $query->where('zone_id', $params['zone_id']);
            });
        })->where('rating', 2)->count();
        $negative_reviews = Review::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->whereHas('item.store', function ($query) use ($params) {
                return $query->where('zone_id', $params['zone_id']);
            });
        })->where('rating', 1)->count();

        $from = now()->startOfMonth(); // first date of the current month
        $to = now();
        $this_month = User::zone($params['zone_id'])->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'))->count();
        $number = 12;
        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        $last_year_users = User::zone($params['zone_id'])
            ->whereMonth('created_at', 12)
            ->whereYear('created_at', now()->format('Y') - 1)
            ->count();

        $users = User::zone($params['zone_id'])
            ->select(
                DB::raw('(count(id)) as total'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupBy('year', 'month')->get()->toArray();

        for ($inc = 1; $inc <= $number; $inc++) {
            $user_data[$inc] = 0;
            foreach ($users as $match) {
                if ($match['month'] == $inc) {
                    $user_data[$inc] = $match['total'];
                }
            }
        }

        $active_customers = User::zone($params['zone_id'])->where('status', 1)->count();
        $blocked_customers = User::zone($params['zone_id'])->where('status', 0)->count();
        $newly_joined = User::zone($params['zone_id'])->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'))->count();

        $employees = Admin::zone()->with(['role'])->where('role_id', '!=', '1')
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->where('zone_id', $params['zone_id']);
            })
            ->get();

        $deliveryMen = DeliveryMan::with('last_location')->when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })->zonewise()->available()->active()->get();

        $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);

        $module_type = Config::get('module.current_module_type');
        return view("admin-views.dashboard-{$module_type}", compact('data', 'reviews', 'this_month', 'user_data', 'neutral_reviews', 'good_reviews', 'negative_reviews', 'positive_reviews', 'employees', 'active_deliveryman', 'deliveryMen', 'inactive_deliveryman', 'newly_joined_deliveryman', 'delivery_man', 'total_sell', 'commission', 'delivery_commission', 'params', 'module_type', 'customers', 'active_customers', 'blocked_customers', 'newly_joined', 'last_year_users', 'blocked_deliveryman'));
    }

    public function transaction_dashboard(Request $request)
    {
        $module_type = Config::get('module.current_module_type');
        return view("admin-views.dashboard-{$module_type}");
    }

    public function dispatch_dashboard(Request $request)
    {
        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => Config::get('module.current_module_id'),
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
            'business_overview' => $request['business_overview'] ?? 'overall',
        ];

        session()->put('dash_params', $params);
        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $delivery_commission = $data['delivery_commission'];
        $label = $data['label'];
        $customers = User::zone($params['zone_id'])->take(2)->get();

        $delivery_man = DeliveryMan::with('last_location')->when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()
            ->limit(2)->get('image');

        $active_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->where('active', 1)->count();

        $inactive_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->where('application_status', 'approved')->where('active', 0)->count();

        $suspend_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->where('application_status', 'approved')->where('status', 0)->count();

        $unavailable_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->where('active', 1)->Unavailable()->count();

        $available_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->where('active', 1)->Available()->count();

        $newly_joined_deliveryman = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()->whereDate('created_at', '>=', now()->subDays(30)->format('Y-m-d'))->count();

        $deliveryMen = DeliveryMan::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })->zonewise()->available()->active()->get();

        $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);

        $module_type = Config::get('module.current_module_type');
        return view("admin-views.dashboard-{$module_type}", compact('data', 'active_deliveryman', 'deliveryMen', 'unavailable_deliveryman', 'available_deliveryman', 'inactive_deliveryman', 'newly_joined_deliveryman', 'delivery_man', 'total_sell', 'commission', 'delivery_commission', 'label', 'params', 'module_type', 'suspend_deliveryman'));
    }

    public function dashboard(Request $request)
    {

        $params = [
            'zone_id' => $request['zone_id'] ?? 'all',
            'module_id' => Config::get('module.current_module_id'),
            'statistics_type' => $request['statistics_type'] ?? 'overall',
            'user_overview' => $request['user_overview'] ?? 'overall',
            'commission_overview' => $request['commission_overview'] ?? 'this_year',
            'business_overview' => $request['business_overview'] ?? 'overall',
        ];
        session()->put('dash_params', $params);
        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $delivery_commission = $data['delivery_commission'];
        $label = $data['label'];
        $module_type = Config::get('module.current_module_type');
        if ($module_type == 'settings') {
            return redirect()->route('admin.business-settings.business-setup');
        }
        if ($module_type == 'rental' && addon_published_status('Rental') == 1) {
            return redirect()->route('admin.rental.dashboard');
        }
        if ($module_type == 'rental' && addon_published_status('Rental') == 0) {
            return view('errors.404');
        }
      
        return view("admin-views.dashboard-{$module_type}", compact('data', 'total_sell', 'commission', 'delivery_commission', 'label', 'params', 'module_type'));

    }

    public function order(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'statistics_type') {
                $params['statistics_type'] = $request['statistics_type'];
            }
        }
        session()->put('dash_params', $params);

        if ($params['zone_id'] != 'all') {
            $store_ids = Store::where(['module_id' => $params['module_id']])->where(['zone_id' => $params['zone_id']])->pluck('id')->toArray();
        } else {
            $store_ids = Store::where(['module_id' => $params['module_id']])->pluck('id')->toArray();
        }
        $data = self::order_stats_calc($params['zone_id'], $params['module_id']);
        $module_type = Config::get('module.current_module_type');
        if ($module_type == 'parcel') {
            return response()->json([
                'view' => view('admin-views.partials._dashboard-order-stats-parcel', compact('data'))->render()
            ], 200);
        } elseif ($module_type == 'food') {
            return response()->json([
                'view' => view('admin-views.partials._dashboard-order-stats-food', compact('data'))->render()
            ], 200);
        }
        return response()->json([
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    public function zone(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'zone_id') {
                $params['zone_id'] = $request['zone_id'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::dashboard_data($request);
        $total_sell = $data['total_sell'];
        $commission = $data['commission'];
        $popular = $data['popular'];
        $top_deliveryman = $data['top_deliveryman'];
        $top_rated_foods = $data['top_rated_foods'];
        $top_restaurants = $data['top_restaurants'];
        $top_customers = $data['top_customers'];
        $top_sell = $data['top_sell'];
        $delivery_commission = $data['delivery_commission'];
        $module_type = Config::get('module.current_module_type');

        return response()->json([
            'popular_restaurants' => view('admin-views.partials._popular-restaurants', compact('popular'))->render(),
            'top_deliveryman' => view('admin-views.partials._top-deliveryman', compact('top_deliveryman'))->render(),
            'top_rated_foods' => view('admin-views.partials._top-rated-foods', compact('top_rated_foods'))->render(),
            'top_restaurants' => view('admin-views.partials._top-restaurants', compact('top_restaurants'))->render(),
            'top_customers' => view('admin-views.partials._top-customer', compact('top_customers'))->render(),
            'top_selling_foods' => view('admin-views.partials._top-selling-foods', compact('top_sell'))->render(),




            'user_overview' => view('admin-views.partials._user-overview-chart', compact('data'))->render(),
            'monthly_graph' => view('admin-views.partials._monthly-earning-graph', compact('total_sell', 'commission', 'delivery_commission'))->render(),
            'stat_zone' => view('admin-views.partials._zone-change', compact('data'))->render(),
            'order_stats' => $module_type == 'parcel' ? view('admin-views.partials._dashboard-order-stats-parcel', compact('data'))->render() :
                ($module_type == 'food' ? view('admin-views.partials._dashboard-order-stats-food', compact('data'))->render() :
                    view('admin-views.partials._dashboard-order-stats', compact('data'))->render()),
        ], 200);
    }

    public function user_overview(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'user_overview') {
                $params['user_overview'] = $request['user_overview'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::user_overview_calc($params['zone_id'], $params['module_id']);
        $module_type = Config::get('module.current_module_type');
        if ($module_type == 'parcel') {
            return response()->json([
                'view' => view('admin-views.partials._user-overview-chart-parcel', compact('data'))->render()
            ], 200);
        }

        return response()->json([
            'view' => view('admin-views.partials._user-overview-chart', compact('data'))->render()
        ], 200);
    }
    public function commission_overview(Request $request)
    {
        $params = session('dash_params');
        foreach ($params as $key => $value) {
            if ($key == 'commission_overview') {
                $params['commission_overview'] = $request['commission_overview'];
            }
        }
        session()->put('dash_params', $params);

        $data = self::dashboard_data($request);

        return response()->json([
            'view' => view('admin-views.partials._commission-overview-chart', compact('data'))->render(),
            'gross_sale' => view('admin-views.partials._gross_sale', compact('data'))->render()
        ], 200);
    }

    public function order_stats_calc($zone_id, $module_id)
    {
        $params = session('dash_params');
        $module_type = Config::get('module.current_module_type');

        if ($module_id && $params['statistics_type'] == 'today') {
                 $voucherBase = Order::where('module_id', $module_id)
           ->whereDate('created_at', now());

            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id)->whereDate('created_at', now());
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id)->whereDate('accepted', now());
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id)->whereDate('processing', now());
            $pending_in_rs = Order::Pending()->where('module_id', $module_id)->whereDate('pending', now());
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id)->whereDate('picked_up', now());
            $delivered = Order::Delivered()->where('module_id', $module_id)->whereDate('delivered', now());
            $canceled = Order::where('module_id', $module_id)->where('order_status', 'canceled')->whereDate('canceled', now());
            $refund_requested = Order::where('module_id', $module_id)->where('order_status', 'refund_requested')->whereDate('refund_requested', now());
            $refunded = Order::where('module_id', $module_id)->where('order_status', 'refunded')->whereDate('refunded', now());

            $new_orders = Order::where('module_id', $module_id)->whereDate('schedule_at', now());
            $new_items = Item::where('is_approved', 1)->where('module_id', $module_id)->whereDate('created_at', now());
            $new_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id)->whereDate('created_at', now());
            $new_customers = User::whereDate('created_at', now());

            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('is_approved', 1)->where('module_id', $module_id);
            $total_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id);
            $total_customers = User::query();
        } elseif ($module_id && $params['statistics_type'] == 'this_year') {
                 $voucherBase = Order::where('module_id', $module_id)->whereYear('created_at', now()->year);

            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id)->whereYear('created_at', now()->year);
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id)->whereYear('accepted', now()->year);
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id)->whereYear('processing', now()->year);
             $pending_in_rs = Order::Pending()->where('module_id', $module_id)->whereYear('pending', now()->year);
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id)->whereYear('picked_up', now()->year);
            $delivered = Order::Delivered()->where('module_id', $module_id)->whereYear('delivered', now()->year);
            $canceled = Order::where('module_id', $module_id)->where('order_status', 'canceled')->whereYear('canceled', now()->year);
            $refund_requested = Order::where('module_id', $module_id)->where('order_status', 'refund_requested')->whereYear('refund_requested', now()->year);
            $refunded = Order::where('module_id', $module_id)->where('order_status', 'refunded')->whereYear('refunded', now()->year);

            $new_orders = Order::where('module_id', $module_id)->whereYear('schedule_at', now()->year);
            $new_items = Item::where('is_approved', 1)->where('module_id', $module_id)->whereYear('created_at', now()->year);
            $new_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id)->whereYear('created_at', now()->year);
            $new_customers = User::whereYear('created_at', now()->year);

            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('is_approved', 1)->where('module_id', $module_id);
            $total_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id);
            $total_customers = User::query();
        } elseif ($module_id && $params['statistics_type'] == 'this_month') {
                 $voucherBase = Order::where('module_id', $module_id)->whereYear('created_at', now()->year);

            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id)->whereMonth('accepted', now()->month)->whereYear('accepted', now()->year);
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id)->whereMonth('processing', now()->month)->whereYear('processing', now()->year);
            $pending_in_rs = Order::Pending()->where('module_id', $module_id)->whereMonth('pending', now()->month)->whereYear('pending', now()->year);
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id)->whereMonth('picked_up', now()->month)->whereYear('picked_up', now()->year);
            $delivered = Order::Delivered()->where('module_id', $module_id)->whereMonth('delivered', now()->month)->whereYear('delivered', now()->year);

            $canceled = Order::where('module_id', $module_id)->where('order_status', 'canceled')->whereMonth('canceled', now()->month)->whereYear('canceled', now()->year);
            $refund_requested = Order::where('module_id', $module_id)->where('order_status', 'refund_requested')->whereMonth('refund_requested', now()->month)->whereYear('refund_requested', now()->year);
            $refunded = Order::where('module_id', $module_id)->where('order_status', 'refunded')->whereMonth('refunded', now()->month)->whereYear('refunded', now()->year);

            $new_orders = Order::where('module_id', $module_id)->whereMonth('schedule_at', now()->month)->whereYear('schedule_at', now()->year);
            $new_items = Item::where('is_approved', 1)->where('module_id', $module_id)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $new_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $new_customers = User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);

            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('is_approved', 1)->where('module_id', $module_id);
            $total_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id);
            $total_customers = User::query();
        } else {
            $voucherBase = Order::where('module_id', $module_id)->whereDate('created_at', '>=', now()->subDays(30));

            // default = last 30 days
            $searching_for_dm = Order::SearchingForDeliveryman()->where('module_id', $module_id);
            $accepted_by_dm = Order::AccepteByDeliveryman()->where('module_id', $module_id);
            $preparing_in_rs = Order::Preparing()->where('module_id', $module_id);
            $pending_in_rs = Order::Pending()->where('module_id', $module_id);
            $picked_up = Order::ItemOnTheWay()->where('module_id', $module_id);
            $delivered = Order::Delivered()->where('module_id', $module_id);
            $canceled = Order::Canceled()->where('module_id', $module_id);
            $refund_requested = Order::failed()->where('module_id', $module_id);
            $refunded = Order::Refunded()->where('module_id', $module_id);

            $new_orders = Order::where('module_id', $module_id)->whereDate('created_at', '>=', now()->subDays(30));
            $new_items = Item::where('is_approved', 1)->where('module_id', $module_id)->whereDate('created_at', '>=', now()->subDays(30));
            $new_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id)->whereDate('created_at', '>=', now()->subDays(30));
            $new_customers = User::whereDate('created_at', '>=', now()->subDays(30));

            $total_orders = Order::where('module_id', $module_id);
            $total_items = Item::where('is_approved', 1)->where('module_id', $module_id);
            $total_stores = Store::whereHas('vendor', fn($q) => $q->where('status', 1))->where('module_id', $module_id);
            $total_customers = User::query();
        }

        // ================= VOUCHERS =================


        $total_active_voucher = (clone $voucherBase)->where('order_status', 'active')->count();
        $total_redeemed_voucher = (clone $voucherBase)->whereIn('order_status', ['pending', 'processing', 'delivered'])->count();
        $total_expired_voucher = (clone $voucherBase)->where('order_status', 'expired')->count();

        $new_active_voucher = $total_active_voucher;
        $new_redeemed_voucher = $total_redeemed_voucher;
        $new_expired_voucher = $total_expired_voucher;

        // ================= FINAL COUNTS =================
        return [
            'searching_for_dm' => $searching_for_dm->count(),
            'accepted_by_dm' => $accepted_by_dm->count(),
            'preparing_in_rs' => $preparing_in_rs->count(),
            'pending_in_rs' => $pending_in_rs->count(),
            'picked_up' => $picked_up->count(),
            'delivered' => $delivered->count(),
            'canceled' => $canceled->count(),
            'refund_requested' => $refund_requested->count(),
            'refunded' => $refunded->count(),

            'total_orders' => $total_orders->count(),
            'total_items' => $total_items->count(),
            'total_stores' => $total_stores->count(),
            'total_customers' => $total_customers->count(),

            'new_orders' => $new_orders->count(),
            'new_items' => $new_items->count(),
            'new_stores' => $new_stores->count(),
            'new_customers' => $new_customers->count(),

            'total_active_voucher' => $total_active_voucher,
            'new_active_voucher' => $new_active_voucher,
            'total_redeemed_voucher' => $total_redeemed_voucher,
            'new_redeemed_voucher' => $new_redeemed_voucher,
            'total_expired_voucher' => $total_expired_voucher,
            'new_expired_voucher' => $new_expired_voucher,
        ];
    }
    public function user_overview_calc($zone_id, $module_id)
    {
        $params = session('dash_params');
        //zone
        if (is_numeric($zone_id)) {
            $customer = User::where('zone_id', $zone_id);
            $stores = Store::whereHas('vendor', fn($query) => $query->where('status', 1))->where('module_id', $module_id)->where(['zone_id' => $zone_id]);
            $delivery_man = DeliveryMan::where('application_status', 'approved')->where('zone_id', $zone_id)->Zonewise();
        } else {
            $customer = User::whereNotNull('id');
            $stores = Store::whereHas('vendor', fn($query) => $query->where('status', 1))->where('module_id', $module_id)->whereNotNull('id');
            $delivery_man = DeliveryMan::where('application_status', 'approved')->Zonewise();
        }
        //user overview
        if ($params['user_overview'] == 'overall') {
            $customer = $customer->count();
            $stores = $stores->count();
            $delivery_man = $delivery_man->count();
        } elseif ($params['user_overview'] == 'this_month') {
            $customer = $customer->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $stores = $stores->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
            $delivery_man = $delivery_man->whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))->count();
        } elseif ($params['user_overview'] == 'this_year') {
            $customer = $customer
                ->whereYear('created_at', date('Y'))->count();
            $stores = $stores
                ->whereYear('created_at', date('Y'))->count();
            $delivery_man = $delivery_man
                ->whereYear('created_at', date('Y'))->count();
        } else {
            $customer = $customer->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')])->count();
            $stores = $stores->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')])->count();
            $delivery_man = $delivery_man->whereDate('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')])->count();
        }
        $data = [
            'customer' => $customer,
            'stores' => $stores,
            'delivery_man' => $delivery_man
        ];
        return $data;
    }


    public function dashboard_data($request)
    {
        $params = session('dash_params');
        if (!url()->current() == $request->is('admin/users')) {
            $data_os = self::order_stats_calc($params['zone_id'], $params['module_id']);
            $data_uo = self::user_overview_calc($params['zone_id'], $params['module_id']);
        }
        $popular = Wishlist::with(['store'])
            ->whereHas('store')
            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id']);
                });
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('zone_id', $params['zone_id']);
                });
            })
            ->select('store_id', DB::raw('COUNT(store_id) as count'))->groupBy('store_id')
            ->having("count", '>', 0)
            ->orderBy('count', 'DESC')
            ->limit(6)->get();
        $top_sell = Item::withoutGlobalScope(ZoneScope::class)
            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id']);
                });
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id'])->where('zone_id', $params['zone_id']);
                });
            })
            ->having("order_count", '>', 0)
            ->orderBy("order_count", 'desc')
            ->take(6)
            ->get();
        $top_rated_foods = Item::withoutGlobalScope(ZoneScope::class)
            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('module_id', $params['module_id']);
                });
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->whereHas('store', function ($query) use ($params) {
                    return $query->where('zone_id', $params['zone_id']);
                });
            })
            ->having("rating_count", '>', 0)
            ->orderBy('rating_count', 'desc')
            ->take(6)
            ->get();

        $top_deliveryman = DeliveryMan::withCount('orders')->when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->Zonewise()
            ->having("orders_count", '>', 0)
            ->orderBy("orders_count", 'desc')
            ->take(6)
            ->get();

        $top_customers = User::when(is_numeric($params['zone_id']), function ($q) use ($params) {
            return $q->where('zone_id', $params['zone_id']);
        })
            ->having("order_count", '>', 0)
            ->orderBy("order_count", 'desc')
            ->take(6)
            ->get();

        $top_restaurants = Store::whereHas('vendor', fn($query) => $query->where('status', 1))->when(is_numeric($params['module_id']), function ($q) use ($params) {
            return $q->where('module_id', $params['module_id']);
        })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->where('zone_id', $params['zone_id']);
            })
            ->having("order_count", '>', 0)
            ->orderBy("order_count", 'desc')
            ->take(6)
            ->get();


        // custom filtering for bar chart
        $months = array(
            '"' . translate('Jan') . '"',
            '"' . translate('Feb') . '"',
            '"' . translate('Mar') . '"',
            '"' . translate('Apr') . '"',
            '"' . translate('May') . '"',
            '"' . translate('Jun') . '"',
            '"' . translate('Jul') . '"',
            '"' . translate('Aug') . '"',
            '"' . translate('Sep') . '"',
            '"' . translate('Oct') . '"',
            '"' . translate('Nov') . '"',
            '"' . translate('Dec') . '"'
        );
        $days = array(
            '"' . translate('Mon') . '"',
            '"' . translate('Tue') . '"',
            '"' . translate('Wed') . '"',
            '"' . translate('Thu') . '"',
            '"' . translate('Fri') . '"',
            '"' . translate('Sat') . '"',
            '"' . translate('Sun') . '"',
        );
        $total_sell = [];
        $commission = [];
        $label = [];
        $query = OrderTransaction::NotRefunded()
            ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                return $q->where('module_id', $params['module_id']);
            })
            ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                return $q->where('zone_id', $params['zone_id']);
            });
        switch ($params['commission_overview']) {
            case "this_year":
                for ($i = 1; $i <= 12; $i++) {
                    $total_sell[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum('order_amount');

                    $commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));

                    $delivery_commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum('delivery_fee_comission');
                }
                $label = $months;
                break;

            case "this_week":
                $weekStartDate = now()->startOfWeek(); // Start from Monday

                for ($i = 0; $i < 7; $i++) { // Loop through each day of the week
                    $currentDate = $weekStartDate->copy()->addDays($i); // Get the date for the current day in the loop

                    $total_sell[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereDate('created_at', $currentDate->format('Y-m-d'))
                        ->sum('order_amount');

                    $commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereDate('created_at', $currentDate->format('Y-m-d'))
                        ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));

                    $delivery_commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereDate('created_at', $currentDate->format('Y-m-d'))
                        ->sum('delivery_fee_comission');
                }

                $label = $days;
                break;

            case "this_month":
                $start = now()->startOfMonth();
                $total_days = now()->daysInMonth;
                $weeks = array(
                    '"Day 1-7"',
                    '"Day 8-14"',
                    '"Day 15-21"',
                    '"Day 22-' . $total_days . '"',
                );

                for ($i = 1; $i <= 4; $i++) {
                    $end = $start->copy()->addDays(6); // Set the end date for each week

                    // Adjust for the last week of the month
                    if ($i == 4) {
                        $end = now()->endOfMonth();
                    }

                    $total_sell[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereBetween('created_at', ["{$start->format('Y-m-d')} 00:00:00", "{$end->format('Y-m-d')} 23:59:59"])
                        ->sum('order_amount');

                    $commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereBetween('created_at', ["{$start->format('Y-m-d')} 00:00:00", "{$end->format('Y-m-d')} 23:59:59"])
                        ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));

                    $delivery_commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereBetween('created_at', ["{$start->format('Y-m-d')} 00:00:00", "{$end->format('Y-m-d')} 23:59:59"])
                        ->sum('delivery_fee_comission');

                    // Move to the next week
                    $start = $end->copy()->addDay();
                }

                $label = $weeks;
                break;

            default:
                for ($i = 1; $i <= 12; $i++) {
                    $total_sell[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum('order_amount');

                    $commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum(DB::raw('admin_commission + admin_expense - delivery_fee_comission'));

                    $delivery_commission[$i] = OrderTransaction::NotRefunded()
                        ->when(is_numeric($params['module_id']), function ($q) use ($params) {
                            return $q->where('module_id', $params['module_id']);
                        })
                        ->when(is_numeric($params['zone_id']), function ($q) use ($params) {
                            return $q->where('zone_id', $params['zone_id']);
                        })
                        ->whereMonth('created_at', $i)->whereYear('created_at', now()->format('Y'))
                        ->sum('delivery_fee_comission');
                }
                $label = $months;
        }

        if (!url()->current() == $request->is('admin/users')) {
            $dash_data = array_merge($data_os, $data_uo);
        }

        $dash_data['popular'] = $popular;
        $dash_data['top_sell'] = $top_sell;
        $dash_data['top_rated_foods'] = $top_rated_foods;
        $dash_data['top_deliveryman'] = $top_deliveryman;
        $dash_data['top_restaurants'] = $top_restaurants;
        $dash_data['top_customers'] = $top_customers;
        $dash_data['total_sell'] = $total_sell;
        $dash_data['commission'] = $commission;
        $dash_data['delivery_commission'] = $delivery_commission;
        $dash_data['label'] = $label;
        return $dash_data;
    }
}
