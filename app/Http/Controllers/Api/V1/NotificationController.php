<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;

class NotificationController extends Controller
{
    public function get_notifications(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            return response()->json([
                'errors' => [
                    ['code' => 'zoneId', 'message' => 'Zone id is required!']
                ]
            ], 403);
        }

        $zone_id = json_decode($request->header('zoneId'), true) ?? [];
        $user = $request->user();

        try {
            $notifications = Notification::with(['voucher:id,name,description,image'])
                ->active()
                ->where('tergat', 'customer')

                // ✅ Zone filter
                ->where(function ($q) use ($zone_id) {
                    $q->whereNull('zone_id')
                        ->orWhereIn('zone_id', $zone_id);
                })

                // ✅ Segment filter
                ->where(function ($q) use ($user) {
                    $q->whereNull('segment_id')
                        ->orWhere('segment_id', $user->segment_id);
                })

                // ✅ Client filter
                ->where(function ($q) use ($user) {
                    $q->whereNull('client_id')
                        ->orWhere('client_id', $user->client_id);
                })

                // ✅ Date filter
                ->where('updated_at', '>=', \Carbon\Carbon::today()->subDays(15))
                ->get();

            $notifications->append('data');

            $user_notifications = UserNotification::where('user_id', $user->id)
                ->where('updated_at', '>=', \Carbon\Carbon::today()->subDays(15))
                ->get();

            $notifications = $notifications->merge($user_notifications);

            return response()->json($notifications, 200);

        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

}
