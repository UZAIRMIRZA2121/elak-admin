<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Category;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use App\Exports\StoreCategoryExport;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Exports\StoreSubCategoryExport;

class CategoryController extends Controller
{
   public function index(Request $request)
    {
     
        $key = explode(' ', $request['search']);
        $categories=Category::where(['position'=>0])->module(Helpers::get_store_data()->module_id)
        ->when(isset($key) , function($q) use($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->paginate(config('default_pagination'));

        $taxData = Helpers::getTaxSystemType();
        $categoryWiseTax = $taxData['categoryWiseTax'];
        return view('vendor-views.category.index',compact('categories','categoryWiseTax'));
    }

    public function get_all(Request $request){
         
        $data = Category::where('name', 'like', '%'.$request->q.'%')->module(Helpers::get_store_data()->module_id)->limit(8)->get([DB::raw('id, CONCAT(name, " (", if(position = 0, "'.translate('messages.main').'", "'.translate('messages.sub').'"),")") as text')]);
        if(isset($request->all))
        {
            $data[]=(object)['id'=>'all', 'text'=>translate('messages.all')];
        }
        return response()->json($data);
    }

    function sub_index(Request $request)
    {
        
        $key = explode(' ', $request['search']);
        $categories=Category::with(['parent'])
        ->whereHas('parent',function($query){
            $query->module(Helpers::get_store_data()->module_id);
        })
        ->where(['position'=>1])
        ->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
        ->latest()->paginate(config('default_pagination'));
        return view('vendor-views.category.sub-index',compact('categories'));
    }

    // public function search(Request $request){
    //     $key = explode(' ', $request['search']);
    //     $categories=Category::where(['position'=>0])
    //     ->module(Helpers::get_store_data()->module_id)
    //     ->where(function ($q) use ($key) {
    //         foreach ($key as $value) {
    //             $q->orWhere('name', 'like', "%{$value}%");
    //         }
    //     })
    //     ->latest()
    //     ->limit(50)->get();
    //     return response()->json([
    //         'view'=>view('vendor-views.category.partials._table',compact('categories'))->render(),
    //         'count'=>$categories->count()
    //     ]);
    // }

//    public function sub_search(Request $request){
//        $key = explode(' ', $request['search']);
//        $categories=Category::with(['parent'])
//        ->where(function ($q) use ($key) {
//            foreach ($key as $value) {
//                $q->orWhere('name', 'like', "%{$value}%");
//            }
//        })
//        ->where(['position'=>1])->limit(50)->get();
//
//        return response()->json([
//            'view'=>view('vendor-views.category.partials._sub_table',compact('categories'))->render(),
//            'count'=>$categories->count()
//        ]);
//    }

    public function export_categories(Request $request){
        $key = explode(' ', $request['search']);
        $categories=Category::where(['position'=>0])->module(Helpers::get_store_data()->module_id)
        ->when(isset($key) , function($q) use($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->get();

        $taxData = Helpers::getTaxSystemType();
        $categoryWiseTax = $taxData['categoryWiseTax'];
        $data=[
            'data' =>$categories,
            'search' =>$request['search'] ?? null,
            'categoryWiseTax' => $categoryWiseTax
        ];
        if($request->type == 'csv'){
            return Excel::download(new StoreCategoryExport($data), 'Categories.csv');
        }
        return Excel::download(new StoreCategoryExport($data), 'Categories.xlsx');


    }

    public function export_sub_categories(Request $request){
        $key = explode(' ', $request['search']);
        $categories=Category::with(['parent'])
        ->whereHas('parent',function($query){
            $query->module(Helpers::get_store_data()->module_id);
        })
        ->where(['position'=>1])
        ->when(isset($key) , function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->get();

            $data=[
                'data' =>$categories,
                'search' =>$request['search'] ?? null,
            ];
            if($request->type == 'csv'){
                return Excel::download(new StoreSubCategoryExport($data), 'SubCategories.csv');
            }
            return Excel::download(new StoreSubCategoryExport($data), 'SubCategories.xlsx');

    }

      public function get_stores(Request $request , $id)
    {
        dd($id);
        $vendor = $request['vendor'];
        try {
            $categories = Category::where(['position' => 0, 'status' => 1])
                ->when(config('module.current_module_data'), function ($query) {
                    $query->module(config('module.current_module_data')['id']);
                })
                ->orderBy('priority', 'desc')
                ->select('categories.*')
                ->selectRaw('categories.*, ( SELECT COUNT(*)
                            FROM items WHERE items.store_id = ?
                            AND JSON_CONTAINS(items.category_ids, JSON_OBJECT("id", CAST(categories.id AS CHAR)), "$") AND JSON_CONTAINS(items.category_ids, JSON_OBJECT("position", 1), "$") ) AS products_count', [$vendor->stores[0]->id])
                ->get();

            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([$e], 200);
        }
    }
}
