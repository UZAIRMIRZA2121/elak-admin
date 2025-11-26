<?php
namespace App\Http\Controllers\Admin;

use DateTime;
use App\Models\Item;
use App\Models\Client;
use App\Models\Segment;
use App\Models\FlashSale;
use Illuminate\Http\Request;
use App\Models\FlashSaleItem;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GiftOccasions;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;
use App\Models\VoucherType;

class GiftOccasionsController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');

        $GiftOccasions = GiftOccasions::query()
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->orderBy('title', 'asc')
            ->paginate(config('default_pagination'));
            // dd($clients);
        return view('admin-views.gift_occasions.index', compact('GiftOccasions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100',
            'icon'  => 'required',
            'icon.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $GiftOccasions = new GiftOccasions();
        $GiftOccasions->title = $request->title;
        $GiftOccasions->status = "active";

        $icons = [];

        if ($request->hasFile('icon')) {

            foreach ($request->file('icon') as $file) {

                $extension = $file->getClientOriginalExtension();
                $imageName = time() . '_' . uniqid() . '.' . $extension;

                $destination = public_path('uploads/gift_occasions');

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                $file->move($destination, $imageName);

                $icons[] = 'uploads/gift_occasions/' . $imageName;
            }
        }

        // Save as JSON in DB
        $GiftOccasions->icon = json_encode($icons);

        $GiftOccasions->save();

        Toastr::success('Icons added successfully');
        return back();
    }


    public function edit($id)
    {
        $GiftOccasions = GiftOccasions::where('id', $id)->first();

        return view('admin-views.gift_occasions.edit', compact('GiftOccasions'));
    }

   public function getGallery($id)
{
    $occasion = GiftOccasions::findOrFail($id);

    // Icon column se images array lein
    $imagePaths = json_decode($occasion->icon, true) ?? [];

    // Full URLs banayein
    $images = collect($imagePaths)->map(function($path) {
        return ['url' => asset($path)];
    })->toArray();

    return response()->json(['images' => $images]);
}



    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:100',
            'icon.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $GiftOccasions = GiftOccasions::findOrFail($id);

        $GiftOccasions->title = $request->title;

        // ===========================
        // 🔥 Old icons delete
        // ===========================
        if ($GiftOccasions->icon) {
            $oldIcons = json_decode($GiftOccasions->icon, true);

            if (is_array($oldIcons)) {
                foreach ($oldIcons as $oldFile) {
                    $filePath = public_path($oldFile);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        // ===========================
        // 🔥 New icons upload
        // ===========================
        $newIcons = [];

        if ($request->hasFile('icon')) {
            foreach ($request->file('icon') as $file) {

                $extension = $file->getClientOriginalExtension();
                $imageName = time() . '_' . uniqid() . '.' . $extension;

                $destination = public_path('uploads/gift_occasions');

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                $file->move($destination, $imageName);

                $newIcons[] = 'uploads/gift_occasions/' . $imageName;
            }
        }

        // Save new icon list
        $GiftOccasions->icon = json_encode($newIcons);

        $GiftOccasions->save();

        Toastr::success('Gift Occasions updated successfully');
        return back();
    }


    public function delete(Request $request, $id)
    {
        $GiftOccasions = GiftOccasions::findOrFail($id);

        // -----------------------------------------
        // 🔥 Delete ALL old icons (JSON array)
        // -----------------------------------------
        if ($GiftOccasions->icon) {
            $icons = json_decode($GiftOccasions->icon, true);

            if (is_array($icons)) {
                foreach ($icons as $img) {
                    $path = public_path($img);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }
        }

        // -----------------------------------------
        // 🔥 Delete DB record
        // -----------------------------------------
        $GiftOccasions->delete();

        Toastr::success('Deleted successfully');
        return back();
    }

    public function status( $id)
    {
        $GiftOccasions = GiftOccasions::findOrFail($id);
        // dd($Voucher);
        // agar active hai to inactive karo, warna active karo
        $GiftOccasions->status = $GiftOccasions->status === 'active' ? 'inactive' : 'active';
        $GiftOccasions->save();
        Toastr::success('GiftOccasions Status successfully  '.$GiftOccasions->status);
        return back();

    }
}
