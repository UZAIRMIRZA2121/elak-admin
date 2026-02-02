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
            'title' => 'required|max:255',
            'messages' => 'required',
            'icon' => 'required',
            'icon.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $GiftOccasions = new GiftOccasions();
        $GiftOccasions->title = $request->title;
        $GiftOccasions->message = json_encode($request->messages);
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

                $icons[] = 'public/uploads/gift_occasions/' . $imageName;
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
        $images = collect($imagePaths)->map(function ($path) {
            // Remove 'public/' from start for URL
            $urlPath = str_replace('public/', '', $path);
            return ['url' => asset($urlPath)];
        })->toArray();

        return response()->json(['images' => $images]);
    }
    public function gallery_destroy(Request $request, $id)
    {
        $item = GiftOccasions::findOrFail($id);
        $icons = json_decode($item->icon, true);

        // Get the image path from request
        $imgToDelete = $request->img;

        if (($key = array_search($imgToDelete, $icons)) !== false) {

            // Delete the file from public folder if exists
            // Remove 'public/' prefix for public_path()
            $filePath = str_replace('public/', '', $imgToDelete);
            if (file_exists(public_path($filePath))) {
                unlink(public_path($filePath));
            }

            // Remove from array
            unset($icons[$key]);

            // Reindex array
            $icons = array_values($icons);

            // Save updated JSON back to DB
            $item->icon = json_encode($icons);
            $item->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Image not found']);
    }


    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'title' => 'required|max:255',
            'messages' => 'required',
            'icon.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $GiftOccasions = GiftOccasions::findOrFail($id);

        // Update title and message
        $GiftOccasions->title = $request->title;
        $GiftOccasions->message = json_encode($request->messages);

        // ===========================
        // 🔥 Keep old icons
        // ===========================
        $existingIcons = $GiftOccasions->icon ? json_decode($GiftOccasions->icon, true) : [];
        $newIcons = $existingIcons; // start with existing icons

        // ===========================
        // 🔥 Upload new icons and append
        // ===========================
        if ($request->hasFile('icon')) {
            foreach ($request->file('icon') as $file) {

                $extension = $file->getClientOriginalExtension();
                $imageName = time() . '_' . uniqid() . '.' . $extension;

                $destination = public_path('uploads/gift_occasions');

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                $file->move($destination, $imageName);

                $newIcons[] = 'public/uploads/gift_occasions/' . $imageName;
            }

            // Save updated icon list
            $GiftOccasions->icon = json_encode($newIcons);
        }

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
                    // Remove 'public/' prefix for public_path()
                    $filePath = str_replace('public/', '', $img);
                    $path = public_path($filePath);
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

    public function status($id)
    {
        $GiftOccasions = GiftOccasions::findOrFail($id);
        // dd($Voucher);
        // agar active hai to inactive karo, warna active karo
        $GiftOccasions->status = $GiftOccasions->status === 'active' ? 'inactive' : 'active';
        $GiftOccasions->save();
        Toastr::success('GiftOccasions Status successfully  ' . $GiftOccasions->status);
        return back();

    }
}
