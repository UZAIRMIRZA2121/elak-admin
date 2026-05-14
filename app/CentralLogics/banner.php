<?php

namespace App\CentralLogics;

use App\Models\Banner;
use App\Models\Item;
use App\Models\Store;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Cache;

class BannerLogic
{
    public static function get_banners($zone_id, $featured = false)
    {
        /*
        |--------------------------------------------------------------------------
        | Normalize Zone IDs
        |--------------------------------------------------------------------------
        | Accepts:
        | - "[1,2,3]" (JSON string)
        | - [1,2,3]   (array)
        | - 3         (single integer)
        */
        if (is_string($zone_id)) {
            $decoded = json_decode($zone_id, true);
            $zone_id = is_array($decoded) ? $decoded : [$zone_id];
        } elseif (is_numeric($zone_id)) {
            $zone_id = [(int) $zone_id];
        }

        if (!is_array($zone_id)) {
            $zone_id = [];
        }

        // Convert all values to integers and remove invalid entries
        $zone_id = array_values(array_filter(array_map('intval', $zone_id)));

        /*
        |--------------------------------------------------------------------------
        | Build Banner Query
        |--------------------------------------------------------------------------
        */
        $query = Banner::active()
            ->when($featured, function ($q) {
                $q->featured();
            });

        /*
        |--------------------------------------------------------------------------
        | Module Filter
        |--------------------------------------------------------------------------
        */
        if (config('module.current_module_data')) {
            $module = config('module.current_module_data');

            $query->whereHas('zone.modules', function ($q) use ($module) {
                $q->where('modules.id', $module['id']);
            })
                ->module($module['id']);
        }

        /*
        |--------------------------------------------------------------------------
        | Zone Filter
        |--------------------------------------------------------------------------
        | Show:
        | - store_wise banners only if zone_id matches
        | - default banners regardless of zone
        | - null type banners regardless of zone
        */
        if (!empty($zone_id)) {
            $query->where(function ($q) use ($zone_id) {
                $q->where(function ($sub) use ($zone_id) {
                    $sub->where('type', 'store_wise')
                        ->whereIn('zone_id', $zone_id);
                })
                    ->orWhere('type', 'default')
                    ->orWhereNull('type');
            });
        } else {
            $query->where(function ($q) {
                $q->where('type', 'default')
                    ->orWhereNull('type');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Final Query Execution
        |--------------------------------------------------------------------------
        */
        $banners = $query
            ->whereHas('module', function ($q) {
                $q->active();
            })
            ->where('created_by', 'admin')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Format Response
        |--------------------------------------------------------------------------
        */
        $data = [];

        foreach ($banners as $banner) {
            if ($banner->type === 'store_wise') {
                $store = Store::active()
                    ->when(config('module.current_module_data'), function ($query) {
                        $query->whereHas('zone.modules', function ($q) {
                            $q->where('modules.id', config('module.current_module_data')['id']);
                        });
                    })
                    ->find($banner->data);

                if ($store) {
                    $data[] = [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'type' => $banner->type,
                        'image' => $banner->image,
                        'link' => null,
                        'store' => Helpers::store_data_formatting($store, false),
                        'item' => null,
                        'image_full_url' => $banner->image_full_url,
                    ];
                }
            } elseif ($banner->type === 'item_wise') {
                $item = Item::active()
                    ->when(
                        config('module.current_module_data') && !empty($zone_id),
                        function ($query) use ($zone_id) {
                            $query->whereHas('module.zones', function ($q) use ($zone_id) {
                                $q->whereIn('zones.id', $zone_id);
                            });
                        }
                    )
                    ->find($banner->data);

                $data[] = [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'type' => $banner->type,
                    'image' => $banner->image,
                    'link' => null,
                    'store' => null,
                    'item' => $item
                        ? Helpers::product_data_formatting(
                            $item,
                            false,
                            false,
                            app()->getLocale()
                        )
                        : null,
                    'image_full_url' => $banner->image_full_url,
                ];
            } else {
                // default or null type
                $data[] = [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'type' => $banner->type,
                    'image' => $banner->image,
                    'link' => $banner->default_link,
                    'store' => null,
                    'item' => null,
                    'image_full_url' => $banner->image_full_url,
                ];
            }
        }

        return $data;
    }
}
