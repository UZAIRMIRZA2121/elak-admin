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
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;

class SegmentsController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');

        $Segments = Segment::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(config('default_pagination'));
        // dd($clients);
        return view('admin-views.client-segmets.index', compact('Segments'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'type' => 'required',
            'validation_date' => 'required',
        ]);

        $Segment = new Segment();
        $Segment->name = $request->name;
        $Segment->type = $request->type;
        $Segment->validation_date = $request->validation_date;
        $Segment->save();

        Toastr::success('Segment added successfully');
        return back();
    }


    public function edit($id)
    {
        $clients = Client::all();
        $Segments = Segment::where('id', $id)->first();

        // if (!$Segments) {
        //     dd("Record not found for ID: " . $id);
        // }

        return view('admin-views.client-segmets.edit', compact('Segments', 'clients'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'type' => 'required',
            'validation_date' => 'required',
        ]);

        $client = Segment::findOrFail($id);

        $client->name = $request->name;
        $client->type = $request->type;
        $client->validation_date = $request->validation_date;
        $client->save();

        Toastr::success('Segment updated successfully');
        return back();
    }

    public function delete(Request $request, $id)
    {
        $Segment = Segment::findOrFail($id);
        $Segment->delete();

        Toastr::success('Segment deleted successfully');
        return back();
    }

    public function status($id)
    {
        $Segment = Segment::findOrFail($id);
        // dd($Segment);
        // agar active hai to inactive karo, warna active karo
        $Segment->status = $Segment->status === 'active' ? 'inactive' : 'active';
        $Segment->save();
        Toastr::success('Segment Status successfully  ' . $Segment->status);
        return back();

    }





    public function getSegments($id)
    {
        $segments = Segment::where('client_id', $id)->get();

        return response()->json([
            'segments' => $segments
        ]);
    }
public function storeMultiple(Request $request)
{
    $names = $request->input('name', []);
    $types = $request->input('type', []);
    $validity_days = $request->input('validity_days', []);
    $validation_dates = $request->input('validation_date', []);
    $statuses = $request->input('status', []); // 'active' / 'inactive'
    $segment_ids = $request->input('segment_id', []);
    $client_id = $request->client_id;

    if (empty($client_id)) {
        return response()->json(['error' => 'Client ID is required'], 400);
    }

    // Get existing segment IDs
    $existingSegmentIds = Segment::where('client_id', $client_id)->pluck('id')->toArray();

    // Delete segments not present in the form
    $idsToKeep = array_filter($segment_ids); // non-empty IDs
    $idsToDelete = array_diff($existingSegmentIds, $idsToKeep);
    if (!empty($idsToDelete)) {
        Segment::whereIn('id', $idsToDelete)->delete();
    }

    $savedSegments = [];

    // Loop through submitted segments
    for ($i = 0; $i < count($names); $i++) {
        if (empty($names[$i])) continue;

        $days = !empty($validity_days[$i]) ? $validity_days[$i] : null;
        $date = !empty($validation_dates[$i]) ? $validation_dates[$i] : null;

        // Mutual exclusivity logic
        if ($date) {
            $days = null; // if date exists, days must be null
        } elseif ($days) {
            $date = null; // if days exists, date must be null
        }

        $data = [
            'name' => $names[$i],
            'type' => $types[$i] ?? 'free',
            'validity_days' => $days,
            'validation_date' => $date,
            'status' => $statuses[$i] ?? 'active',
            'client_id' => $client_id
        ];

        if (!empty($segment_ids[$i])) {
            // Update existing segment
            $segment = Segment::find($segment_ids[$i]);
            if ($segment) {
                $segment->update($data);
            }
        } else {
            // Create new segment
            $segment = Segment::create($data);
        }

        $savedSegments[] = $segment;
    }

    return response()->json([
        'success' => true,
        'segments' => $savedSegments
    ]);
}


}
