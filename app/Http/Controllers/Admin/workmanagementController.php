<?php
namespace App\Http\Controllers\Admin;

use DateTime;
use DB;
use App\Models\Item;
use App\Models\Client;
use App\Models\Segment;
use App\Models\FlashSale;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use App\Models\FlashSaleItem;
use App\CentralLogics\Helpers;
use App\Models\ManagementType;
use App\Models\WorkManagement;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;

class workmanagementController extends Controller
{

      public function list(Request $request)
        {
            $search = $request->input('search');

          $WorkManagements = WorkManagement::query()
            ->leftJoin('voucher_types', 'work_managements.voucher_id', '=', 'voucher_types.id')
            ->when($search, function ($q) use ($search) {
                $q->where('work_managements.guide_title', 'like', "%{$search}%")
                ->orWhere('voucher_types.name', 'like', "%{$search}%");
            })
            ->orderBy('work_managements.guide_title', 'asc')
            ->select('work_managements.*', 'voucher_types.name as voucher_name')
            ->paginate(config('default_pagination'));
            // dd($WorkManagements);
            return view('admin-views.work_management.index', compact('WorkManagements'));
        }

      public function index(Request $request)
        {
            $search = $request->input('search');

             $vouchers = VoucherType::get();
            // dd($vouchers);
            $ManagementType = WorkManagement::query()
                ->when($search, function ($q) use ($search) {
                    $q->where('guide_title', 'like', "%{$search}%");
                })
                ->orderBy('guide_title', 'asc')
                ->paginate(config('default_pagination'));
            return view('admin-views.work_management.add', compact('ManagementType','vouchers'));
        }


        // In WorkManagementController

    public function show($id)
    {
        try {
            $workManagement = WorkManagement::findOrFail($id);

            // Get voucher name if voucher_id exists
            // $voucherName = 'N/A';
            // if ($workManagement->voucher_id) {
            //     $voucher = Voucher::find($workManagement->voucher_id);
            //     $voucherName = $voucher ? $voucher->name : 'N/A';
            // }

            return response()->json([
                'id' => $workManagement->id,
                'guide_title' => $workManagement->guide_title,
                // 'voucher_name' => $voucherName,
                'sections' => $workManagement->sections,
                'status' => $workManagement->status,
                'created_at' => $workManagement->created_at->format('d M Y, h:i A'),
                'updated_at' => $workManagement->updated_at->format('d M Y, h:i A'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Work Management not found'
            ], 404);
        }
    }
        public function store(Request $request)
        {
            $request->validate([
                'voucher_type' => 'required|max:100',
                'guide_title'  => 'required',
                'sections'     => 'required|array',
            ]);

            \DB::table('work_managements')->insert([
                'voucher_id'  => $request->voucher_type,
                'guide_title'  => $request->guide_title,
                'sections'    => json_encode($request->sections),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            Toastr::success('Works Guide added successfully');
            return back();
        }



    public function edit($id)
    {
           $vouchers = VoucherType::get();
        $ManagementType = WorkManagement::where('id', $id)->first();
        // dd($ManagementType);
      $sections = [];

    if (!empty($ManagementType->sections)) {
        $json = $ManagementType->sections; // already array

        foreach ($json as $title => $steps) {
            $sections[] = [
                'title' => $title,
                'steps' => $steps
            ];
        }
    }



        // dd($sections);
        return view('admin-views.work_management.edit', compact('ManagementType','vouchers','sections'));
    }

    public function update(Request $request, $id)
    {
        $termType = $request->term_type;

            //  Validation
            $request->validate([
            'voucher_type'          => 'required|max:100',
            'guide_title'          => 'required',
            'sections'    => 'required|array',
        ]);

            //  Record find and update
            $ManagementType = WorkManagement::findOrFail($id);
            $ManagementType->voucher_id         = $request->voucher_type;
            $ManagementType->guide_title         = $request->guide_title;
            $ManagementType->sections   = json_encode($request->sections);
            $ManagementType->save();

            Toastr::success('Work Management updated successfully');
            return back();
    }


    public function delete(Request $request, $id)
    {
        $ManagementType = WorkManagement::findOrFail($id);

        $ManagementType->delete();

        Toastr::success('Work Management deleted successfully');
        return back();
    }

    public function status( $id)
    {
        $ManagementType = WorkManagement::findOrFail($id);
        // dd($ManagementType);
        // agar active hai to inactive karo, warna active karo
        $ManagementType->status = $ManagementType->status === 'active' ? 'inactive' : 'active';
        $ManagementType->save();
        Toastr::success('Work Management Status successfully  '.$ManagementType->status);
        return back();

    }
}
