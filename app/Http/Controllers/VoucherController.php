<?php

namespace App\Http\Controllers;


use App\Models\GiftOccasions;
use App\Models\MessageTemplate;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\HolidayOccasion;
use App\Models\CustomBlackoutData;
use App\Models\GeneralRestriction;
use App\Models\Tag;
use App\Models\Item;
use App\Models\Brand;
use App\Models\App;
use App\Models\Client;
use App\Models\VoucherSetting;
use App\Models\Store;
use App\Models\Review;
use App\Models\Allergy;
use App\Models\Category;
use App\Models\Segment;
use App\Models\Nutrition;
use App\Scopes\StoreScope;
use App\Models\GenericName;
use App\Models\TempProduct;
use App\Models\Translation;
use App\Models\VoucherType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Models\ItemCampaign;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Exports\ItemListExport;
use App\Models\CommonCondition;
use Illuminate\Validation\Rule;
use App\Exports\StoreItemExport;
use App\Exports\ItemReviewExport;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\ProductLogic;
use App\Models\PharmacyItemDetails;
use App\Http\Controllers\Controller;
use App\Models\EcommerceItemDetails;
use App\Models\ManagementType;
use App\Models\Module;
use App\Models\UsageTermManagement;
use App\Models\WorkManagement;
use Aws\WorkDocs\WorkDocsClient;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class VoucherController extends Controller
{

    public function shareVoucher(Request $request, $qr_code)
    {
        $order = Order::where('qr_code', $qr_code)->first();

        if (!$order) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }
      
        // Assuming you have a view named 'voucher.share' to display the voucher details
        return view('voucher.share', compact('order'));
    }
}
