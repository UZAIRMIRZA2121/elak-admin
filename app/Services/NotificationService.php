<?php

namespace App\Services;

use App\Models\Item;
use App\Traits\FileManagerTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class NotificationService
{
    use FileManagerTrait;



    public function getAddData(object $request): array
    {

    
        // $voucher = Item::find($request->voucher_id);
        if ($request->has('image')) {
            $imageName = $this->upload('notification/', 'png', $request->file('image'));
        } 
        // elseif ($voucher && $voucher->image) {

        //     // old path (disk ke andar ka path, NOT full URL)
        //     $oldPath = 'product/' . $voucher->image;

        //     // new path
        //     $newName = Carbon::now()->toDateString() . "-" . uniqid() . ".png";
        //     $newPath = 'notification/' . $newName;

        //     // check existence
        //     if (Storage::disk(self::getDisk())->exists($oldPath)) {

        //         Storage::disk(self::getDisk())->copy($oldPath, $newPath);

        //         // save only filename OR full path (tumhari DB structure par depend karta hai)
        //         $imageName = $newName; // recommended
        //         // ya agar path save karte ho:
        //         // $imageName = $newPath;
        //     }
        // }
         else {
            $imageName = null;
        }

        return [
            'title' => $request->notification_title,
            'description' => $request->description,
            'image' => $imageName,
            'tergat' => $request->tergat ?? 'customer',
            'status' => 1,
            'client_id' => $request->client_id ?? null,
            'segment_id' => $request->segment_id ?? null,
            'zone_id' => $request->zone == 'all' ? null : $request->zone,
            'notification_link' => $request->notification_link ?? null,
            'voucher_id' => $request->voucher_id ?? null,

        ];
    }
    public function getUpdateData(object $request, object $notification): array
    {
        if ($request->has('image')) {
            $imageName = $this->updateAndUpload('notification/', $notification->image, 'png', $request->file('image'));
        } else {
            $imageName = $notification['image'];
        }
        return [
            'title' => $request->notification_title,
            'description' => $request->description,
            'image' => $imageName,
            'tergat' => $request->tergat,
            'status' => 1,
            'zone_id' => $request->zone == 'all' ? null : $request->zone,
            'updated_at' => now(),
        ];
    }

    public function getTopic(object $request): string
    {
        $topicAllZone = [
            'customer' => 'all_zone_customer',
            'deliveryman' => 'all_zone_delivery_man',
            'store' => 'all_zone_store',
        ];

        $topicZoneWise = [
            'customer' => 'zone_' . $request->zone . '_customer',
            'deliveryman' => 'zone_' . $request->zone . '_delivery_man_push',
            'store' => 'zone_' . $request->zone . '_store',
        ];

        return $request->zone == 'all' ? $topicAllZone[$request->tergat] : $topicZoneWise[$request->tergat];
    }


}
