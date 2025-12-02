<?php

namespace App\Http\Controllers\Admin;

use App\Models\App;
use App\Models\AppTheme;
use App\Models\ColorCode;
use DateTime;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Banner;
use App\Models\Client;
use App\Models\Segment;
use App\Models\FlashSale;
use Illuminate\View\View;
use App\Models\ColorTheme;
use App\Exports\ZoneExport;
use Illuminate\Http\Request;

use App\Models\FlashSaleItem;
use App\Services\ZoneService;
use App\CentralLogics\Helpers;
use Illuminate\Http\JsonResponse;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use App\Exports\CollectUserListExport;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\BaseController;
use App\Enums\ExportFileNames\Admin\Zone;
use App\Http\Requests\Admin\ZoneAddRequest;
use Illuminate\Database\Eloquent\Collection;
use App\Exports\CollectCashTransactionExport;
use App\Http\Requests\Admin\ZoneUpdateRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\ViewPaths\Admin\Zone as ZoneViewPath;
use App\Http\Requests\Admin\ZoneModuleUpdateRequest;
use App\Contracts\Repositories\ZoneRepositoryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Models\AppBanner;
use App\Models\Category;

class ClientSideController extends Controller
{
    public function __construct(
        protected ZoneRepositoryInterface $zoneRepo,
        protected ZoneService $zoneService,
        protected TranslationRepositoryInterface $translationRepo
    ) {
    }


    public function export_account_transaction(Request $request)
    {
        // $all_users = User::latest()->get();
        $all_users = User::latest()->get();

        // Excel/CSV export
        if ($request->type == 'excel') {
            return Excel::download(new CollectUserListExport($all_users), 'UsersList.xlsx');
        } elseif ($request->type == 'csv') {
            return Excel::download(new CollectUserListExport($all_users), 'UsersList.csv');
        }
    }

    public function index(Request $request)
    {
        $Segment = Segment::all();
        $search = $request->input('search');

        $clients = Client::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(config('default_pagination'));

        // add type_names attribute for each client
        $clients->getCollection()->transform(function ($client) {
            if ($client->type) {
                $ids = explode(',', $client->type);
                $client->type_names = \App\Models\Segment::whereIn('id', $ids)->pluck('name')->toArray();
            } else {
                $client->type_names = [];
            }

            return $client;
        });
        $app = App::all();
        return view('admin-views.client-side.index', compact('clients', 'Segment', 'app'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:clients,email',
            'password' => 'required|min:6',
            'logo_image' => 'nullable|image',
            'cover_image' => 'nullable|image',
        ]);

        $client = new Client();
        $client->name = $request->name;
        $client->email = $request->email;
        $client->password = bcrypt($request->password);


        //  Logo Upload
        if ($request->hasFile('logo_image')) {
            $file = $request->file('logo_image');
            $extension = $file->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;

            $destination = public_path('uploads/clients/logos');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $imageName);
            $client->logo = 'uploads/clients/logos/' . $imageName; // save path in DB
        }

        //  Cover Upload
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $extension = $file->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;

            $destination = public_path('uploads/clients/covers');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $imageName);
            $client->cover = 'uploads/clients/covers/' . $imageName;
        }


        $client->save();

        Toastr::success('Client added successfully');
        return back();
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $selectedTypes = $client->type ? explode(',', $client->type) : [];
        $Segment = Segment::all(); // tumhara segment list

        return view('admin-views.client-side.edit', compact('client', 'selectedTypes', 'Segment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:clients,email,' . $id, // ignore current email
            'password' => 'nullable|min:6',
            'logo_image' => 'nullable|image',
            'cover_image' => 'nullable|image',


        ]);

        $client = Client::findOrFail($id);

        $client->name = $request->name;
        $client->email = $request->email;

        if ($request->filled('password')) {
            $client->password = bcrypt($request->password);
        }


        //  Logo Upload
        if ($request->hasFile('logo_image')) {
            if ($client->logo && file_exists(public_path($client->logo))) {
                unlink(public_path($client->logo)); // old delete
            }
            $file = $request->file('logo_image');
            $extension = $file->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;

            $destination = public_path('uploads/clients/logos');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $imageName);
            $client->logo = 'uploads/clients/logos/' . $imageName;
        }

        //  Cover Upload
        if ($request->hasFile('cover_image')) {
            if ($client->cover && file_exists(public_path($client->cover))) {
                unlink(public_path($client->cover));
            }
            $file = $request->file('cover_image');
            $extension = $file->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;

            $destination = public_path('uploads/clients/covers');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $imageName);
            $client->cover = 'uploads/clients/covers/' . $imageName;
        }

        $client->save();

        Toastr::success('Client updated successfully');
        return back();
    }

    public function delete(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        //  Delete Logo
        if ($client->logo && file_exists(public_path($client->logo))) {
            unlink(public_path($client->logo));
        }

        //  Delete Cover
        if ($client->cover && file_exists(public_path($client->cover))) {
            unlink(public_path($client->cover));
        }

        //  Delete Avatar/Profile
        if ($client->avatar && file_exists(public_path($client->avatar))) {
            unlink(public_path($client->avatar));
        }

        //  Delete Client Record
        $client->delete();

        Toastr::success('Client deleted successfully with all images');
        return back();
    }

    public function status($id)
    {
        $Client = Client::findOrFail($id);
        // dd($Client);
        // agar active hai to inactive karo, warna active karo
        $Client->status = $Client->status === 'active' ? 'inactive' : 'active';
        $Client->save();
        Toastr::success('Client Status successfully  ' . $Client->status);
        return back();
    }

    public function listclient_user(Request $request)
    {
        $Clients = Client::all();
        $Segment = Segment::all();
        $search = $request->input('search');

        $Users = User::query()
            ->leftJoin('clients', 'users.client_id', '=', 'clients.id')
            ->select('users.*', 'clients.name as client_name')
            ->when($search, function ($q) use ($search) {
                $q->where('users.status', 'like', "%{$search}%")
                    ->orWhere('clients.name', 'like', "%{$search}%")
                    ->orWhere('users.id', 'like', "%{$search}%");
            })
            ->orderBy('users.f_name', 'asc')
            ->paginate(config('default_pagination'));

        // Transform users to add segment names based on client type field
        $Users->getCollection()->transform(function ($user) {
            // Initialize type_names as empty array
            $user->type_names = [];

            // Check if client has type field with values (from clients table)
            // if (!empty($user->client_type)) {
            //     // Split comma-separated values and remove any whitespace
            //     $segmentIds = array_filter(array_map('trim', explode(',', $user->client_type)));

            //     if (!empty($segmentIds)) {
            //         // Get segment names from segments table
            //         $user->type_names = \App\Models\Segment::whereIn('id', $segmentIds)
            //             ->pluck('name')
            //             ->toArray();
            //     }
            // }

            return $user;
        });

        return view('admin-views.client-user.index', compact('Users', 'Segment', 'Clients'));
    }


    public function listclient_store(Request $request)
    {
        $request->validate([
            'segment_type' => 'required|integer',
            'select_client' => 'required|integer',
            'add_record_client' => 'required|integer|min:1|max:10000000',
        ]);

        $count = $request->add_record_client;

        DB::beginTransaction();
        try {
            $existingRefs = DB::table('users')->pluck('ref_by')->toArray(); // existing ref_ids
            $records = [];

            for ($i = 0; $i < $count; $i++) {
                do {
                    $randomRef = mt_rand(10000000, 99999999); // generate 8-digit random
                } while (in_array($randomRef, $existingRefs)); // ensure uniqueness

                $existingRefs[] = $randomRef; // add to existing to prevent duplicates in this batch

                $records[] = [
                    'segment_id' => $request->segment_type,
                    'client_id' => $request->select_client,
                    'role' => 'user',
                    'status' => 0,
                    'ref_by' => $randomRef,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // insert in chunks of 1000
                if (count($records) === 1000) {
                    DB::table('users')->insert($records);
                    $records = [];
                }
            }

            if (!empty($records)) {
                DB::table('users')->insert($records);
            }

            DB::commit();

            Toastr::success("$count Clients added successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error: ' . $e->getMessage());
        }

        return back();
    }


    // public function getSegments($clientId)
    // {
    //     dd($clientId);
    //     $Client = Client::where('id', $clientId)->first();
    //     // dd($Client->type);
    //     $ids = explode(',', trim($Client->type));
    //     $segments = Segment::whereIn('id', $ids)->get();
    //     return response()->json($segments);
    // }

    public function getSegments($clientId)
    {
        // Get all active segments for the given client_id
        $segments = Segment::where('client_id', $clientId)
            ->where('status', 'active')
            ->get();

        return response()->json($segments);
    }




    public function filter(Request $request)
    {
        $Clients = Client::all();
        $Segment = Segment::all();

        // saare filter values form se le lo
        $clientId = $request->input('select_client');
        $segmentId = $request->input('segment_type');
        $refId = $request->input('ref_id');
        $status = $request->input('status');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        // $search     = $request->input('search');

        // dd($active);
        $Users = User::query()
            ->leftJoin('clients', 'users.client_id', '=', 'clients.id')
            ->select('users.*', 'clients.name as client_name', 'clients.type as client_type')


            // client filter
            ->when($clientId, function ($q) use ($clientId) {
                $q->where('users.client_id', $clientId);
            })

            // ref_id filter
            ->when($refId, function ($q) use ($refId) {
                $q->where('users.ref_by', $refId);
            })

            // status filter
            // status filter
            ->when(isset($status) && $status !== '', function ($q) use ($status) {
                $q->where('users.status', $status);
            })

            // date range filter
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('users.created_at', [$fromDate, $toDate]);
            })

            ->orderBy('users.f_name', 'asc')
            ->paginate(config('default_pagination'));

        // add type_names attribute for each user
        $Users->getCollection()->transform(function ($user) {
            if ($user->client_type) {
                $ids = explode(',', $user->client_type);
                $user->type_names = \App\Models\Segment::whereIn('id', $ids)->pluck('name')->toArray();
            } else {
                $user->type_names = [];
            }

            return $user;
        });

        //  dd($Users);
        return view('admin-views.client-user.filter', compact('Users', 'Segment', 'Clients'));
    }

    // banner section
    public function banner(Request $request)
    {

        if ($request->isMethod('post')) {

            //  Validation
            $validated = $request->validate([
                'app_id' => 'required|max:100',
                'title_name' => 'required|string|max:255',
                'zone_id' => 'required|integer',
                'banner_type' => 'required|string',
                'display_number' => 'required|integer',

                // file image/video
                'image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv|max:20480',

                // optional
                'store_id' => 'nullable|string',
                'external_link' => 'nullable|url',
                'category' => 'nullable|string',
                'voucher' => 'nullable|string',
                'voucher_type' => 'nullable|string',
            ]);

            //  Model create
            $Banner = new AppBanner();
            $Banner->app_id = $validated['app_id'];
            $Banner->title = $validated['title_name'];
            $Banner->type_priority = $validated['display_number'];
            $Banner->status = 1;
            $Banner->zone_id = $validated['zone_id'];
            $Banner->banner_type = $validated['banner_type'];

            // optional fields
            $Banner->store_id = $validated['store_id'] ?? null;
            $Banner->voucher_id = $validated['voucher'] ?? null;
            $Banner->category_id = $validated['category'] ?? null;
            $Banner->voucher_type = $validated['voucher_type'] ?? null;
            $Banner->external_lnk = $validated['external_link'] ?? null;

            //  File Upload (image OR video)
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = strtolower($file->getClientOriginalExtension());
                $fileName = time() . '_' . uniqid() . '.' . $extension;

                $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $videoExt = ['mp4', 'mov', 'avi', 'mkv'];

                if (in_array($extension, $imageExt)) {
                    $path = 'uploads/Banner/images/';
                    $Banner->image_or_video = 'image';
                } elseif (in_array($extension, $videoExt)) {
                    $path = 'uploads/Banner/videos/';
                    $Banner->image_or_video = 'video';
                } else {
                    return back()->withErrors(['image' => 'Invalid file type.']);
                }

                $destination = public_path($path);
                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                $file->move($destination, $fileName);
                $Banner->image_or_video = $path . $fileName;
            }

            //  Save
            $Banner->save();

            Toastr::success('Banner added successfully');
            return back();
        }

        // Agar GET ya koi aur request hai

        $Banners = AppBanner::paginate(10);
        $zones = $this->zoneRepo->getList();
        $apps = App::all();
        $category = Category::all();
        return view("admin-views.client-user.banner", compact('Banners', 'zones', 'apps', 'category'));
    }

    public function edit_banner($id)
    {
        $Banner = AppBanner::findOrFail($id);
        $apps = App::all();
        $zones = $this->zoneRepo->getList();
        $category = Category::all();
        return view('admin-views.client-user.banner_edit', compact('Banner', 'apps', 'zones', 'category'));
    }
    public function update_banner(Request $request, $id)
    {
        // Validation
        $validated = $request->validate([
            'app_id' => 'required|max:100',
            'title_name' => 'required|string|max:255',
            'zone_id' => 'required|integer',
            'banner_type' => 'required|string',
            'display_number' => 'required|integer',

            // file image/video
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv|max:20480',

            // optional
            'store_id' => 'nullable|string',
            'external_link' => 'nullable|url',
            'category' => 'nullable|string',
            'voucher' => 'nullable|string',
            'voucher_type' => 'nullable|string',
        ]);

        // Banner find
        $Banner = AppBanner::findOrFail($id);
        $Banner->app_id = $validated['app_id'];
        $Banner->title = $validated['title_name'];
        $Banner->type_priority = $validated['display_number'];
        $Banner->zone_id = $validated['zone_id'];
        $Banner->banner_type = $validated['banner_type'];

        // optional fields
        $Banner->store_id = $validated['store_id'] ?? null;
        $Banner->voucher_id = $validated['voucher'] ?? null;
        $Banner->category_id = $validated['category'] ?? null;
        $Banner->voucher_type = $validated['voucher_type'] ?? null;
        $Banner->external_lnk = $validated['external_link'] ?? null;

        // File Upload (image OR video)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = time() . '_' . uniqid() . '.' . $extension;

            $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $videoExt = ['mp4', 'mov', 'avi', 'mkv'];

            if (in_array($extension, $imageExt)) {
                $path = 'uploads/Banner/images/';
            } elseif (in_array($extension, $videoExt)) {
                $path = 'uploads/Banner/videos/';
            } else {
                return back()->withErrors(['image' => 'Invalid file type.']);
            }

            $destination = public_path($path);
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // 🗑 Purana file delete karo agar exist karta ho
            if (!empty($Banner->image_or_video) && file_exists(public_path($Banner->image_or_video))) {
                unlink(public_path($Banner->image_or_video));
            }

            // Naya file save karo
            $file->move($destination, $fileName);
            $Banner->image_or_video = $path . $fileName;
        }

        // Save
        $Banner->save();

        // Success Message
        Toastr::success('Banner updated successfully');
        return back();
    }


    public function delete_banner(Request $request, $id)
    {
        $Banner = AppBanner::findOrFail($id);
        //  Delete Logo
        if ($Banner->image_or_video && file_exists(public_path($Banner->image_or_video))) {
            unlink(public_path($Banner->image_or_video));
        }
        //  Delete Client Record
        $Banner->delete();

        Toastr::success('Client deleted successfully with all images');
        return back();
    }

    public function status_banner($id)
    {
        $Banner = AppBanner::findOrFail($id);
        // dd($Client);
        // agar active hai to inactive karo, warna active karo
        $Banner->status = $Banner->status === '1' ? '0' : '1';
        $Banner->save();
        Toastr::success('Banner Status successfully  ' . $Banner->status);
        return back();
    }


    public function color_theme(Request $request)
    {

        if ($request->isMethod('post')) {

            // Validate theme fields
            $request->validate([
                'name' => 'required|max:100',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|in:active,inactive',
                'colors' => 'required|array',
            ]);

            DB::beginTransaction();
            try {

                // Create new ColorTheme
                $theme = new ColorTheme();
                $theme->name = $request->name;
                $theme->start_date = $request->start_date;
                $theme->end_date = $request->end_date;
                $theme->status = $request->status;
                $theme->save();

                // Save color groups
                foreach ($request->colors as $key => $colorGroup) {

                    // Skip if empty or invalid
                    if (!is_array($colorGroup) || empty($colorGroup['value'])) {
                        continue;
                    }

                    ColorCode::create([
                        'color_theme_id' => $theme->id,
                        'color_name' => $colorGroup['name'] ?? ucfirst(str_replace('_', ' ', $key)), // custom name or fallback
                        'color_code' => $colorGroup['value'],       // hex value
                        'color_type' => $key,                        // <-- use the array key as type
                        'color_gradient' => isset($colorGroup['gradient']) ? 1 : 0,
                        'status' => 'active',
                    ]);
                }

                DB::commit();
                dd(11);
                Toastr::success('Color Theme and Colors added successfully');
                return back();

            } catch (\Exception $e) {

                DB::rollBack();
                Toastr::error('Error: ' . $e->getMessage());
                return back();
            }
        }

        // Fetch themes
        $themes = ColorTheme::with('colorCodes')->paginate(10);

        return view("admin-views.client-user.colortheme", compact('themes'));
    }



    public function edit_color_theme($id)
    {
        // Get the theme
        $colorTheme = ColorTheme::with('colorCodes')->findOrFail($id);

        // Prepare colors array for the form (predefined)
        $predefinedColors = [
            'primary_color',
            'secondary_color',
            'background_color',
            'text_color',
            'button_color',
            'button_text_color',
            'navbar_color',
            'navbar_text_color'
        ];

        // Map existing color values for easy access in Blade
        $colors = [];
        foreach ($predefinedColors as $key) {
            $color = $colorTheme->colorCodes->firstWhere('color_type', $key);
            $colors[$key] = [
                'value' => $color->color_code ?? '#000000',
                'name' => $color->color_name ?? ucwords(str_replace('_', ' ', $key)),
                'gradient' => $color->color_gradient ?? 0
            ];
        }

        // Pass the data to the edit view
        return view('admin-views.client-user.colortheme_edit', compact('colorTheme', 'colors'));
    }

    public function update_color_theme(Request $request, $id)
    {
        // Validation
        $request->validate([
            'name' => 'required|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:active,inactive',
            'colors' => 'required|array',
            'colors.*.value' => 'required',
            'colors.*.name' => 'nullable|string|max:100',
            'colors.*.gradient' => 'nullable|boolean',
        ]);

        // Start transaction
        DB::beginTransaction();
        try {
            // Find the theme
            $theme = ColorTheme::findOrFail($id);

            // Update theme fields
            $theme->name = $request->name;
            $theme->start_date = $request->start_date;
            $theme->end_date = $request->end_date;
            $theme->status = $request->has('status') && $request->status == 'active' ? 'active' : 'inactive';
            $theme->save();

            // Loop through each submitted color
            foreach ($request->colors as $key => $colorGroup) {

                // Skip if value is empty
                if (empty($colorGroup['value']))
                    continue;

                // Check if the color code exists
                $colorCode = ColorCode::where('color_theme_id', $theme->id)
                    ->where('color_type', $key)
                    ->first();

                if ($colorCode) {
                    // Update existing color
                    $colorCode->update([
                        'color_name' => $colorGroup['name'] ?? ucfirst(str_replace('_', ' ', $key)),
                        'color_code' => $colorGroup['value'],
                        'color_gradient' => isset($colorGroup['gradient']) ? 1 : 0,
                        'status' => 'active',
                    ]);
                } else {
                    // Create new color if missing
                    ColorCode::create([
                        'color_theme_id' => $theme->id,
                        'color_name' => $colorGroup['name'] ?? ucfirst(str_replace('_', ' ', $key)),
                        'color_code' => $colorGroup['value'],
                        'color_type' => $key,
                        'color_gradient' => isset($colorGroup['gradient']) ? 1 : 0,
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();

            Toastr::success('Color Theme updated successfully');
            return redirect()->route('admin.client-side.color_theme'); // Redirect to index page

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error updating color theme: ' . $e->getMessage());
            return back()->withInput();
        }
    }


    public function delete_color_theme(Request $request, $id)
    {
        // Find the theme or fail
        $theme = ColorTheme::findOrFail($id);

        // Delete associated color codes
        $theme->colorCodes()->delete();

        // Delete the theme itself
        $theme->delete();

        Toastr::success('Color Theme and its colors deleted successfully');
        return back();
    }

    public function status_color_theme(Request $request, $id)
    {
        $ColorTheme = ColorTheme::findOrFail($id);
        $ColorTheme->status = $request->status; // set status from AJAX
        $ColorTheme->save();

        return response()->json([
            'success' => true,
            'status' => $ColorTheme->status
        ]);
    }


    public function get_app_themes(Request $request)
    {
        $appId = $request->app_id;

        // Fetch active themes for this app
        $themes = AppTheme::with(['theme.colorCodes'])
            ->where('app_id', $appId)
            ->get()
            ->map(function ($appTheme) {
                return [
                    'id' => $appTheme->theme->id,
                    'name' => $appTheme->theme->name,
                    'status' => $appTheme->theme->status,
                    'colorCodes' => $appTheme->theme->colorCodes->map(function ($color) {
                        return [
                            'color_name' => $color->color_name,
                            'color_code' => $color->color_code
                        ];
                    }),
                ];
            });

        return response()->json(['themes' => $themes]);
    }

    public function toggle_app_theme(Request $request)
    {
        $request->validate([
            'app_id' => 'required|integer',
            'theme_id' => 'required|integer',
            'assigned' => 'required|boolean',
        ]);

        if ($request->assigned) {
            // Assign theme
            AppTheme::firstOrCreate([
                'app_id' => $request->app_id,
                'theme_id' => $request->theme_id
            ]);

            $message = 'Theme assigned successfully';
        } else {
            // Remove theme
            AppTheme::where('app_id', $request->app_id)
                ->where('theme_id', $request->theme_id)
                ->delete();

            $message = 'Theme removed successfully';
        }

        return response()->json(['message' => $message]);
    }

    public function updateAppThemeForm(Request $request)
{
    $request->validate([
        'app_id' => 'required|integer',
        'themes' => 'array',
    ]);

    $appId = $request->app_id;

    // Remove all existing themes of this app
    AppTheme::where('app_id', $appId)->delete();

    // Insert new ones
    if ($request->themes) {
        foreach ($request->themes as $themeId) {
            AppTheme::create([
                'app_id' => $appId,
                'theme_id' => $themeId,
            ]);
        }
    }

    Toastr::success("Themes Updated Successfully!");
    return back();
}



}