<?php
namespace App\Http\Controllers\Admin;

use DateTime;
use App\Models\Item;
use App\Models\Client;
use App\Models\Segment;
use App\Models\FlashSale;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use App\Models\FlashSaleItem;
use App\CentralLogics\Helpers;
use App\Models\ManagementType;
use App\Models\VoucherSetting;
use App\Models\WorkManagement;
use App\Models\HolidayOccasion;
use App\Models\GeneralRestriction;
use App\Http\Controllers\Controller;
use App\Models\AgeRestrictin;
use App\Models\CustomBlackoutData;
use App\Models\GroupSizeRequirement;
use App\Models\OfferValidatyPeroid;
use App\Models\StoreSchedule;
use App\Models\UsagePeriod;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class VoucherSettingController extends Controller
{

       public function list(Request $request)
        {
            $search = $request->input('search');
             $HolidayOccasion = HolidayOccasion::get();
            $GeneralRestriction = GeneralRestriction::get();

            $VoucherSetting = VoucherSetting::query()
                ->when($search, function ($q) use ($search) {
                    $q->where('validity_period', 'like', "%{$search}%");
                })
                ->orderBy('validity_period', 'asc')
                ->paginate(config('default_pagination'));

            return view('admin-views.voucher_setting.index', compact('VoucherSetting','HolidayOccasion','GeneralRestriction'));
        }

//   voucher setting insert  or update
        public function index(Request $request, $id)
        {
            // dd($id);
            $items = item::findOrFail($id);

            $days = [
                1 => 'monday',
                2 => 'tuesday',
                3 => 'wednesday',
                4 => 'thursday',
                5 => 'friday',
                6 => 'saturday',
                7 => 'sunday'
            ];

                    // Use first() instead of findOrFail() to avoid exception
                    $StoreSchedule = StoreSchedule::where('store_id', $items->store_id)->first();

                    if ($StoreSchedule) {
                        // Convert day number to string
                        $dayKey = $days[$StoreSchedule->day] ?? null;

                        // 24-hour format for <input type="time">
                        $opening = date("H:i", strtotime($StoreSchedule->opening_time));
                        $closing = date("H:i", strtotime($StoreSchedule->closing_time));

                        // Working hours array
                        $working_hours = [
                            $dayKey => [
                                "start" => $opening,
                                "end"   => $closing,
                            ]
                        ];
                    } else {
                        // If store schedule doesn't exist, set empty array
                        $working_hours = [];
                    }

            $VoucherSetting = VoucherSetting::where("item_id", $items->id)->first();

                    if (!empty($VoucherSetting)) {
                        $check_data = 1;  // data exists

                    
                        $validityPeriod = is_string($VoucherSetting->validity_period) 
                        ? json_decode($VoucherSetting->validity_period, true) 
                        : ($VoucherSetting->validity_period ?? []);

                        $specificDays = is_string($VoucherSetting->specific_days_of_week) 
                                        ? json_decode($VoucherSetting->specific_days_of_week, true) 
                                        : ($VoucherSetting->specific_days_of_week ?? []);

                        $holidays = is_string($VoucherSetting->holidays_occasions) 
                                        ? json_decode($VoucherSetting->holidays_occasions, true) 
                                        : ($VoucherSetting->holidays_occasions ?? []);

                        $custom_blackout_dates = is_string($VoucherSetting->custom_blackout_dates) 
                                        ? json_decode($VoucherSetting->custom_blackout_dates, true) 
                                        : ($VoucherSetting->custom_blackout_dates ?? []);

                        $userLimit = is_string($VoucherSetting->usage_limit_per_user) 
                                        ? json_decode($VoucherSetting->usage_limit_per_user, true) 
                                        : ($VoucherSetting->usage_limit_per_user ?? []);

                        $storeLimit = is_string($VoucherSetting->usage_limit_per_store) 
                                        ? json_decode($VoucherSetting->usage_limit_per_store, true) 
                                        : ($VoucherSetting->usage_limit_per_store ?? []);

                        $generalRestrictions = is_string($VoucherSetting->general_restrictions) 
                                        ? json_decode($VoucherSetting->general_restrictions, true) 
                                        : ($VoucherSetting->general_restrictions ?? []);

                    } else {
                        $check_data = 0; // no data

                        $validityPeriod = [];
                        $specificDays = [];
                        $holidays = [];
                        $custom_blackout_dates = [];
                        $userLimit = [];
                        $storeLimit = [];
                        $generalRestrictions = [];
                    }

            $search = $request->input('search');
            $CustomBlackoutData = CustomBlackoutData::get();
            $HolidayOccasion = HolidayOccasion::get();
            $GeneralRestriction = GeneralRestriction::get();
            $AgeRestrictin = AgeRestrictin::get();
            $GroupSizeRequirement = GroupSizeRequirement::get();
            $UsagePeriod = UsagePeriod::get();
            $OfferValidatyPeroid = OfferValidatyPeroid::get();

            // dd($VoucherSettings);

            $VoucherSettings = VoucherSetting::query()
                ->when($search, function ($q) use ($search) {
                    $q->where('validity_period', 'like', "%{$search}%");
                })
                ->orderBy('validity_period', 'asc')
                ->paginate(config('default_pagination'));

            // dd($VoucherSetting->group_size_requirement);


                return view('admin-views.voucher_setting.add', compact(
                    'VoucherSetting',
                    'CustomBlackoutData',
                    'HolidayOccasion',
                    'GeneralRestriction',
                    'AgeRestrictin',
                    'GroupSizeRequirement',
                    'UsagePeriod',
                    'OfferValidatyPeroid',
                    'items',
                    'check_data',
                    'custom_blackout_dates',
                    'validityPeriod',
                    'specificDays',
                    'holidays',
                    'userLimit',
                    'storeLimit',
                    'generalRestrictions',
                    'working_hours'
                ));
        }



    public function store(Request $request)
    {
      
        $request->validate([
            'validity_period' => 'required',
        ]);

        // Voucher_id aaya hai?
        $item_id = $request->item_id;
        // dd($item_id);
        // If record exists → UPDATE
        // If not exists → INSERT
        $VoucherSetting = VoucherSetting::updateOrCreate(
            ['item_id' => $item_id], // check condition
            [
                'validity_period' => json_encode($request->validity_period ?? []),
                'specific_days_of_week' => json_encode($request->working_hours ?? []),
                'holidays_occasions' => json_encode($request->exclude_national ?? []),
                'custom_blackout_dates' => json_encode($request->custom_blackout_dates ?? []),
                // 'age_restriction' =>  json_encode($request->age_restriction ?? []), 
                // 'group_size_requirement' =>  json_encode($request->group_size ?? []),  
                'age_restriction' =>  json_encode($request->age_restriction ?? []), 
                'group_size_requirement' =>  json_encode($request->group_size ?? []),  
                // Store

                'usage_limit_per_user' => json_encode($request->user_limit ?? []),
                'usage_limit_per_store' => json_encode($request->store_limit ?? []),
                'offer_validity_after_purchase' => $request->validity_after ?? null,
                'general_restrictions' => json_encode($request->no_other_offers ?? []),
                'status' => "active",
            ]
        );

        Toastr::success('Voucher Settings Saved Successfully');
        return back();
    }




      public function add_setting(Request $request)
    {

        $search = $request->input('search');
        $HolidayOccasion = HolidayOccasion::get();
        $GeneralRestriction = GeneralRestriction::get();

        $VoucherSetting = VoucherSetting::query()
            ->when($search, function ($q) use ($search) {
                $q->where('validity_period', 'like', "%{$search}%");
            })
            ->orderBy('validity_period', 'asc')
            ->paginate(config('default_pagination'));
        return view('admin-views.voucher_setting.add_setting', compact('VoucherSetting','HolidayOccasion','GeneralRestriction'));
    }

    public function conditions_store(Request $request)
    {
        $type = $request->input('type');

        try {

            if ($type === 'blackout_date') {

                $validated = $request->validate([
                    'date' => 'required|date',
                    'description' => 'required|string|max:255',
                ]);

                $record = CustomBlackoutData::create([
                    'date' => $request->date,
                    'description' => $request->description,
                ]);

            } elseif ($type === 'holiday') {

                $validated = $request->validate([
                    'name_ar' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                    'start_date' => 'nullable|date',
                    'end_date' => 'nullable|date|after_or_equal:start_date',
                ]);

                $record = HolidayOccasion::create([
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

            } elseif ($type === 'restriction') {
                // dd("generr");
                $validated = $request->validate([
                    'name_ar' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                ]);

                $record = GeneralRestriction::create([
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);

            } elseif ($type === 'age_restriction') {

                $validated = $request->validate([
                    'name_ar' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                ]);

                $record = AgeRestrictin::create([
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);

            } elseif ($type === 'group_size_requirement') {

                $validated = $request->validate([
                    'name_ar' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                ]);

                $record = GroupSizeRequirement::create([
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);

            } elseif ($type === 'usage_period') {

                $validated = $request->validate([
                    'name_ar' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                ]);

                $record = UsagePeriod::create([
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);

            } elseif ($type === 'validity_period') {

                $validated = $request->validate([
                    'name_ar' => 'required|string|max:255',
                    'name_en' => 'required|string|max:255',
                ]);
                // dd($request->name_ar);
                $record = OfferValidatyPeroid::create([
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);

            } else {
                return response()->json([
                    'success' => false,  // Changed from 'status' to 'success'
                    'message' => 'Invalid type given.'
                ], 400);
            }

            // SUCCESS RESPONSE - Changed 'status' to 'success'
            return response()->json([
                'success' => true,  // Changed from 'status' => 'success'
                'message' => 'Record inserted successfully!',
                'data' => $record
            ]);

        } catch (ValidationException $e) {

            // ERROR RESPONSE
            return response()->json([
                'success' => false,  // Changed from 'status'
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            // General error handling
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }



    public function getAll()
    {
        try {
            $data = [
                'holidays' => HolidayOccasion::all()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name_ar' => $item->name_ar,
                        'name_en' => $item->name_en,
                        'start_date' => $item->start_date,
                        'end_date' => $item->end_date,
                        'type' => 'holiday'
                    ];
                }),
                'restrictions' => GeneralRestriction::all()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name_ar' => $item->name_ar,
                        'name_en' => $item->name_en,
                        'type' => 'restriction'
                    ];
                }),
                'blackoutDates' => CustomBlackoutData::all()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->date,
                        'description' => $item->description,
                        'type' => 'blackout_date'
                    ];
                }),
                'ageRestrictions' => AgeRestrictin::all()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name_ar' => $item->name_ar,
                        'name_en' => $item->name_en,
                        'type' => 'age_restriction'
                    ];
                }),
                'groupSizeRequirements' => GroupSizeRequirement::all()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name_ar' => $item->name_ar,
                        'name_en' => $item->name_en,
                        'type' => 'group_size_requirement'
                    ];
                }),
                'usagePeriods' => UsagePeriod::all()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name_ar' => $item->name_ar,
                        'name_en' => $item->name_en,
                        'type' => 'usage_period'
                    ];
                }),
                'validityPeriods' => OfferValidatyPeroid::all()->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name_ar' => $item->name_ar,
                        'name_en' => $item->name_en,
                        'type' => 'validity_period'
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function conditions_delete(Request $request)
    {
        try {
            $id = $request->input('id');
            $type = $request->input('type');

            if ($type === 'blackout_date') {
                CustomBlackoutData::findOrFail($id)->delete();
            } elseif ($type === 'holiday') {
                HolidayOccasion::findOrFail($id)->delete();
            } elseif ($type === 'restriction') {
                GeneralRestriction::findOrFail($id)->delete();
            } elseif ($type === 'age_restriction') {
                AgeRestrictin::findOrFail($id)->delete();
            } elseif ($type === 'group_size_requirement') {
                GroupSizeRequirement::findOrFail($id)->delete();
            } elseif ($type === 'usage_period') {
                UsagePeriod::findOrFail($id)->delete();
            } elseif ($type === 'validity_period') {
                OfferValidatyPeroid::findOrFail($id)->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting: ' . $e->getMessage()
            ], 500);
        }
    }


}
