<?php

namespace App\Http\Controllers\Vendor;

use App\Exports\DisbursementVendorReportExport;
use App\Models\DisbursementDetails;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Store;
use App\Models\WithdrawalMethod;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Exports\ExpenseReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Exports\TransactionReportExportVendor;


class ReportController extends Controller
{
    public function day_wise_report(Request $request)
    {

        $key = explode(' ', $request['search']);

        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if ($filter == 'custom') {
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }

        $store_id = Helpers::get_store_id();
        $zone_id = $request->query('zone_id', isset(auth('admin')->user()->zone_id) ? auth('admin')->user()->zone_id : 'all');
        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;
        $store_id = $request->query('store_id', $store_id ?? 'all');
        $store = is_numeric($store_id) ? Store::findOrFail($store_id) : null;

        $order_transactions = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->paginate(config('default_pagination'))->withQueryString();

        $admin_earned = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->notRefunded()
            ->sum(DB::raw('admin_commission'));

        $admin_earned_delivery_commission = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->sum('delivery_fee_comission');

        $store_earned = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->notRefunded()
            ->sum(DB::raw('store_amount'));
        // ->sum(DB::raw('store_amount - tax'));

        $deliveryman_earned = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->sum(DB::raw('original_delivery_charge + dm_tips'));

        return view('vendor-views.report.day-wise-report', compact('order_transactions', 'zone', 'store', 'filter', 'admin_earned', 'admin_earned_delivery_commission', 'store_earned', 'deliveryman_earned', 'key', 'from', 'to'));
    }

    public function day_wise_export(Request $request)
    {
        $key = explode(' ', $request['search']);

        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if ($filter == 'custom') {
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }
        $zone_id = $request->query('zone_id', isset(auth('admin')->user()->zone_id) ? auth('admin')->user()->zone_id : 'all');
        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;
        $store_id = $request->query('store_id', 'all');
        $store = is_numeric($store_id) ? Store::findOrFail($store_id) : null;

        $order_transactions = OrderTransaction::when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->get();

        $admin_earned = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->notRefunded()
            ->sum(DB::raw('admin_commission -  delivery_fee_comission'));
        // ->sum(DB::raw('(admin_commission + admin_expense) - delivery_fee_comission'));

        $admin_earned_delivery_commission = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->sum('delivery_fee_comission');

        $store_earned = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->notRefunded()
            ->sum(DB::raw('store_amount'));
        // ->sum(DB::raw('store_amount - tax'));

        $deliveryman_earned = OrderTransaction::with('order', 'order.details', 'order.customer', 'order.store')->when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(isset($store), function ($query) use ($store) {
                return $query->whereHas('order', function ($q) use ($store) {
                    $q->where('store_id', $store->id);
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })->orderBy('created_at', 'desc')
            ->sum(DB::raw('original_delivery_charge + dm_tips'));

        $delivered = Order::when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when(isset($store), function ($query) use ($store) {
                return $query->where('store_id', $store->id);
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [
                    now()
                        ->startOfWeek()
                        ->format('Y-m-d H:i:s'),
                    now()
                        ->endOfWeek()
                        ->format('Y-m-d H:i:s'),
                ]);
            })
            ->Notpos()
            ->sum('order_amount');
        $canceled = Order::when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%");
                    }
                });
            })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->where(['order_status' => 'refunded'])
            ->when(isset($store), function ($query) use ($store) {
                return $query->where('store_id', $store->id);
            })
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [
                    now()
                        ->startOfWeek()
                        ->format('Y-m-d H:i:s'),
                    now()
                        ->endOfWeek()
                        ->format('Y-m-d H:i:s'),
                ]);
            })
            ->Notpos()
            // ->sum(DB::raw('order_amount - original_delivery_charge'));
            ->sum(DB::raw('order_amount - delivery_charge - dm_tips'));

        $data = [
            'order_transactions' => $order_transactions,
            'search' => $request->search ?? null,
            'from' => (($filter == 'custom') && $from) ? $from : null,
            'to' => (($filter == 'custom') && $to) ? $to : null,
            'zone' => is_numeric($zone_id) ? Helpers::get_zones_name($zone_id) : null,
            'store' => is_numeric($store_id) ? Helpers::get_stores_name($store_id) : null,
            'module' => request('module_id') ? Helpers::get_module_name(request('module_id')) : null,
            'admin_earned' => $admin_earned + $admin_earned_delivery_commission,
            'store_earned' => $store_earned,
            'deliveryman_earned' => $deliveryman_earned,
            'delivered' => $delivered,
            'canceled' => $canceled,
            'filter' => $filter,
        ];

        if ($request->type == 'excel') {
            return Excel::download(new TransactionReportExportVendor($data), 'TransactionReport.xlsx');
        } else if ($request->type == 'csv') {
            return Excel::download(new TransactionReportExportVendor($data), 'TransactionReport.csv');
        }
    }
    public function set_date(Request $request)
    {
        session()->put('from_date', date('Y-m-d', strtotime($request['from'])));
        session()->put('to_date', date('Y-m-d', strtotime($request['to'])));
        return back();
    }

    public function expense_report(Request $request)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if ($filter == 'custom') {
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }
        $key = explode(' ', $request['search']);
        $expense = Expense::with('order')->where('created_by', 'vendor')->where('store_id', Helpers::get_store_id())->where('amount', '>', 0)
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })
            ->when(isset($key), function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('type', 'like', "%{$value}%")->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(config('default_pagination'))->withQueryString();
        $module_type = Helpers::get_store_data()->module->module_type;
        return view('vendor-views.report.expense-report', compact('expense', 'from', 'to', 'filter', 'module_type'));
    }




    public function expense_export(Request $request)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if ($filter == 'custom') {
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }
        $key = explode(' ', $request['search']);
        $expense = Expense::with('order')->where('created_by', 'vendor')->where('store_id', Helpers::get_store_id())->where('amount', '>', 0)
            ->when(isset($from) && isset($to) && $from != null && $to != null && $filter == 'custom', function ($query) use ($from, $to) {
                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
            })
            ->when(isset($filter) && $filter == 'this_year', function ($query) {
                return $query->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'this_month', function ($query) {
                return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
            })
            ->when(isset($filter) && $filter == 'previous_year', function ($query) {
                return $query->whereYear('created_at', date('Y') - 1);
            })
            ->when(isset($filter) && $filter == 'this_week', function ($query) {
                return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
            })
            ->when(isset($key), function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('type', 'like', "%{$value}%")->orWhere('order_id', 'like', "%{$value}%");
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();


        $data = [
            'expenses' => $expense,
            'search' => $request->search ?? null,
            'from' => (($filter == 'custom') && $from) ? $from : null,
            'to' => (($filter == 'custom') && $to) ? $to : null,
            'zone' => Helpers::get_zones_name(Helpers::get_store_data()->zone_id),
            'store' => Helpers::get_stores_name(Helpers::get_store_id()),
            'module_type' => Helpers::get_store_data()->module->module_type,
            // 'customer'=>is_numeric($customer_id)?Helpers::get_customer_name($customer_id):null,
            // 'module'=>request('module_id')?Helpers::get_module_name(request('module_id')):null,
            'filter' => $filter,
            'type' => 'store',
        ];

        if ($request->type == 'excel') {
            return Excel::download(new ExpenseReportExport($data), 'ExpenseReport.xlsx');
        } else if ($request->type == 'csv') {
            return Excel::download(new ExpenseReportExport($data), 'ExpenseReport.csv');
        }

    }

    public function disbursement_report(Request $request)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if ($filter == 'custom') {
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }
        $key = explode(' ', $request['search']);
        $store_id = Helpers::get_store_id();
        $withdrawal_methods = WithdrawalMethod::ofStatus(1)->get();
        $status = $request->query('status', 'all');
        $payment_method_id = $request->query('payment_method_id', 'all');

        $dis = DisbursementDetails::where('store_id', $store_id)
            ->when((isset($payment_method_id) && ($payment_method_id != 'all')), function ($query) use ($payment_method_id) {
                return $query->whereHas('withdraw_method', function ($q) use ($payment_method_id) {
                    $q->where('withdrawal_method_id', $payment_method_id);
                });
            })
            ->when((isset($status) && ($status != 'all')), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when(isset($filter), function ($query) use ($filter, $from, $to) {
                return $query->applyDateFilter($filter, $from, $to);
            })
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('disbursement_id', 'like', "%{$value}%")
                            ->orWhere('status', 'like', "%{$value}%");
                    }
                });
            })
            ->latest();

        $total_disbursements = $dis->get();

        $disbursements = $dis->paginate(config('default_pagination'))->withQueryString();

        $pending = (float) $total_disbursements->where('status', 'pending')->sum('disbursement_amount');
        $completed = (float) $total_disbursements->where('status', 'completed')->sum('disbursement_amount');
        $canceled = (float) $total_disbursements->where('status', 'canceled')->sum('disbursement_amount');

        return view('vendor-views.report.disbursement-report', compact('disbursements', 'pending', 'completed', 'canceled', 'filter', 'from', 'to', 'withdrawal_methods', 'status', 'payment_method_id'));

    }

    public function disbursement_report_export(Request $request, $type)
    {
        $from = null;
        $to = null;
        $filter = $request->query('filter', 'all_time');
        if ($filter == 'custom') {
            $from = $request->from ?? null;
            $to = $request->to ?? null;
        }
        $key = explode(' ', $request['search']);
        $store_id = Helpers::get_store_id();
        $withdrawal_methods = WithdrawalMethod::ofStatus(1)->get();
        $status = $request->query('status', 'all');
        $payment_method_id = $request->query('payment_method_id', 'all');

        $disbursements = DisbursementDetails::where('store_id', $store_id)
            ->when((isset($payment_method_id) && ($payment_method_id != 'all')), function ($query) use ($payment_method_id) {
                return $query->whereHas('withdraw_method', function ($q) use ($payment_method_id) {
                    $q->where('withdrawal_method_id', $payment_method_id);
                });
            })
            ->when((isset($status) && ($status != 'all')), function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when(isset($filter), function ($query) use ($filter, $from, $to) {
                return $query->applyDateFilter($filter, $from, $to);
            })
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('disbursement_id', 'like', "%{$value}%")
                            ->orWhere('status', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->get();

        $data = [
            'disbursements' => $disbursements,
            'search' => $request->search ?? null,
            'status' => $status,
            'filter' => $filter,
            'from' => (($filter == 'custom') && $from) ? $from : null,
            'to' => (($filter == 'custom') && $to) ? $to : null,
            'pending' => (float) $disbursements->where('status', 'pending')->sum('disbursement_amount'),
            'completed' => (float) $disbursements->where('status', 'completed')->sum('disbursement_amount'),
            'canceled' => (float) $disbursements->where('status', 'canceled')->sum('disbursement_amount'),
        ];
        if ($type == 'csv') {
            return Excel::download(new DisbursementVendorReportExport($data), 'DisbursementReport.csv');
        }
        return Excel::download(new DisbursementVendorReportExport($data), 'DisbursementReport.xlsx');

    }

}
