<?php

namespace App\Models;

use App\Scopes\ZoneScope;
use App\Scopes\StoreScope;
use Illuminate\Support\Str;
use App\Traits\ReportFilter;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TaxModule\Entities\Taxable;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Store;
use App\Models\Client;
use App\Models\App;
use App\Models\Segment;
use App\Models\Category;
use App\Models\GiftOccasions;
use App\Models\MessageTemplate;
use App\Models\DeliveryOption;

class Item extends Model
{
    use HasFactory, ReportFilter;
    protected $guarded = ['id'];
    protected $with = ['translations', 'storage'];
    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'avg_rating' => 'float',
        'set_menu' => 'integer',
        'category_id' => 'integer',
        'store_id' => 'integer',
        'reviews_count' => 'integer',
        'recommended' => 'integer',
        'maximum_cart_quantity' => 'integer',
        'organic' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'veg' => 'integer',
        'images' => 'array',
        'module_id' => 'integer',
        'is_approved' => 'integer',
        'stock' => 'integer',
        'min_price' => 'float',
        'max_price' => 'float',
        'order_count' => 'integer',
        'rating_count' => 'integer',
        'unit_id' => 'integer',
        'is_halal' => 'integer',
        'type' => 'string',
        'clients_section' => 'array',
        'client_id' => 'array',
        'segment_ids' => 'array',
        'sub_category_ids' => 'array',
        'branch_ids' => 'array',
        'valid_until' => 'string',
        'offer_type' => 'string',
        'voucher_ids' => 'string',
        'bundle_type' => 'string',
        'tags_ids' => 'array',
        'how_and_condition_ids' => 'array',
        'term_and_condition_ids' => 'array',
        'product' => 'array',
        'product_b' => 'array',
        'required_quantity' => 'integer',
        'name' => 'string',


        'discount_configuration' => 'string',

        'occasions_id' => 'array',
        'recipient_info_form_fields' => 'array',//add
        'message_template_style' => 'string',//add
        'delivery_options' => 'array',
        'amount_configuration' => 'string',
        'amount_type' => 'string',//add
        'enable_custom_amount' => 'string',//add
        'fixed_amount_options' => 'array', //add
        'min_max_amount' => 'array', //add
        'bonus_configuration' => 'string', //add

        'redemption_process' => 'string',

        'validity_period' => 'string',
        'usage_restrictions' => 'string',
        'blackout_dates' => 'string',




    ];



    protected $appends = ['unit_type', 'image_full_url', 'images_full_url'];

    public function scopeRecommended($query)
    {
        return $query->where('recommended', 1);
    }

    public function carts()
    {
        return $this->morphMany(Cart::class, 'item');
    }

    public function temp_product()
    {
        return $this->hasOne(TempProduct::class, 'item_id')->with('translations');
    }

    public function scopeDiscounted($query)
    {
        // return $query->where('discount','>',0);

        $nowDate = now()->format('Y-m-d');
        $nowTime = now()->format('H:i');

        return $query->where(function ($query) use ($nowDate, $nowTime) {
            $query->where('discount', '>', 0)
                ->orWhereHas('store.discount', function ($q) use ($nowDate, $nowTime) {
                    $q->whereDate('start_date', '<=', $nowDate)
                        ->whereDate('end_date', '>=', $nowDate)
                        ->whereTime('start_time', '<=', $nowTime)
                        ->whereTime('end_time', '>=', $nowTime);
                })
                ->orWhereHas('flashSaleItems.flashSale', function ($q) use ($nowDate, $nowTime) {
                    $q->where('is_publish', 1)
                        ->whereDate('start_date', '<=', $nowDate)
                        ->whereDate('end_date', '>=', $nowDate);
                });
        });
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function scopeModule($query, $module_id)
    {
        return $query->where('module_id', $module_id);
    }


    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('is_approved', 1)
            ->whereHas('store', function ($query) {
                $query->where('status', 1)
                    ->where(function ($query) {
                        $query->where('store_business_model', 'commission')
                            ->orWhereHas('store_sub', function ($query) {
                                $query->where(function ($query) {
                                    $query->where('max_order', 'unlimited')->orWhere('max_order', '>', 0);
                                });
                            });
                    });
            });
    }
    public function scopePopular($query)
    {
        return $query->orderBy('order_count', 'desc');
    }
    public function scopeApproved($query)
    {
        return $query->where('is_approved', 1);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function whislists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    // public function scopeHasRunningFlashSale($query)
    // {
    //     return $query->whereHas('flashSaleItems', function ($query) {
    //         $query->whereHas('flashSale', function ($query) {
    //             $query->Running();
    //         });
    //     });
    // }

    public function flashSaleItems()
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function getUnitTypeAttribute()
    {
        return $this->unit ? $this->unit->unit : null;
    }

    public function getNameAttribute($value)
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'name') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        if (count($this->translations) > 0) {
            foreach ($this->translations as $translation) {
                if ($translation['key'] == 'description') {
                    return $translation['value'];
                }
            }
        }

        return $value;
    }
    public function getImageFullUrlAttribute()
    {
        $value = $this->image;
        if (count($this->storage) > 0) {
            foreach ($this->storage as $storage) {
                if ($storage['key'] == 'image') {
                    return Helpers::get_full_url('product', $value, $storage['value']);
                }
            }
        }

        return Helpers::get_full_url('product', $value, 'public');
    }
    public function getImagesFullUrlAttribute()
    {
        $images = [];
        $value = is_array($this->images)
            ? $this->images
            : ($this->images && is_string($this->images) && $this->isValidJson($this->images)
                ? json_decode($this->images, true)
                : []);
        if ($value) {
            foreach ($value as $item) {
                $item = is_array($item) ? $item : (is_object($item) && get_class($item) == 'stdClass' ? json_decode(json_encode($item), true) : ['img' => $item, 'storage' => 'public']);
                $images[] = Helpers::get_full_url('product', $item['img'], $item['storage']);
            }
        }

        return $images;
    }

    private function isValidJson($string)
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }


    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function pharmacy_item_details()
    {
        return $this->hasOne(PharmacyItemDetails::class, 'item_id');
    }
    public function ecommerce_item_details()
    {
        return $this->hasOne(EcommerceItemDetails::class, 'item_id');
    }

    public function orders()
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected static function booted()
    {
        if (auth('vendor')->check() || auth('vendor_employee')->check()) {
            static::addGlobalScope(new StoreScope);
        }

        static::addGlobalScope(new ZoneScope);
        static::addGlobalScope('storage', function ($builder) {
            $builder->with('storage');
        });

        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with([
                'translations' => function ($query) {
                    return $query->where('locale', app()->getLocale());
                }
            ]);
        });
    }


    public function scopeType($query, $type)
    {
        if ($type == 'veg') {
            return $query->where('veg', true);
        } else if ($type == 'non_veg') {
            return $query->where('veg', false);
        }
        return $query;
    }

    public function scopeAvailable($query, $time)
    {
        $query->where(function ($q) use ($time) {
            $q->where('available_time_starts', '<=', $time)->where('available_time_ends', '>=', $time);
        });
    }
    public function scopeUnAvailable($query, $time)
    {
        $query->whereNot(function ($q) use ($time) {
            $q->where('available_time_starts', '<=', $time)->where('available_time_ends', '>=', $time);
        });
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function allergies()
    {
        return $this->belongsToMany(Allergy::class);
    }
    public function generic()
    {
        return $this->belongsToMany(GenericName::class, 'item_generic_names');
    }
    public function nutritions()
    {
        return $this->belongsToMany(Nutrition::class);
    }
    public function storage()
    {
        return $this->morphMany(Storage::class, 'data');
    }
    protected static function boot()
    {
        parent::boot();
        static::created(function ($item) {
            $item->slug = $item->generateSlug($item->name);
            $item->save();
        });
        static::creating(function ($item) {
            if (empty($item->uuid)) {
                $item->uuid = (string) Str::uuid();
            }
        });
        static::saved(function ($model) {
            if ($model->isDirty('image')) {
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'image',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($model->isDirty('images')) {
                $value = Helpers::getDisk();

                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'images',
                ], [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        if ($max_slug = static::where('slug', 'like', "{$slug}%")->latest('id')->value('slug')) {

            if ($max_slug == $slug)
                return "{$slug}-2";

            $max_slug = explode('-', $max_slug);
            $count = array_pop($max_slug);
            if (isset($count) && is_numeric($count)) {
                $max_slug[] = ++$count;
                return implode('-', $max_slug);
            }
        }
        return $slug;
    }

    public function taxVats()
    {
        return $this->morphMany(Taxable::class, 'taxable');
    }






    public function branches(): Collection
    {
        $branchIds = $this->branch_ids;

        // Decode the JSON string safely
        if (is_string($branchIds)) {
            // The extra escapes need double decoding
            $branchIds = json_decode($branchIds, true);
            if (is_string($branchIds)) {
                // In case it’s still a string after first decode
                $branchIds = json_decode($branchIds, true);
            }
        }

        // Ensure it's an array
        if (!is_array($branchIds)) {
            $branchIds = [];
        }

        return Store::whereIn('id', $branchIds)->get();
    }


    /**
     * Get clients from clients_section JSON
     */
    public function clients(): Collection
    {
        $clientsSection = $this->clients_section;

        if (is_string($clientsSection)) {
            $clientsSection = json_decode($clientsSection, true);
            if (!is_array($clientsSection))
                $clientsSection = [];
        }

        $clientIds = collect($clientsSection)->pluck('client_id')->filter()->toArray();

        return Client::whereIn('id', $clientIds)->get();
    }

    /**
     * Get apps from clients_section JSON
     */
    public function apps(): Collection
    {
        $clientsSection = $this->clients_section;

        if (is_string($clientsSection)) {
            $clientsSection = json_decode($clientsSection, true);
            if (!is_array($clientsSection))
                $clientsSection = [];
        }

        $appIds = collect($clientsSection)->pluck('app_name_id')->filter()->toArray();

        return App::whereIn('id', $appIds)->get();
    }

    /**
     * Get segments from clients_section JSON
     */
    public function segments(): Collection
    {
        $clientsSection = $this->clients_section;

        if (is_string($clientsSection)) {
            $clientsSection = json_decode($clientsSection, true);
            if (!is_array($clientsSection))
                $clientsSection = [];
        }

        // pluck all segment arrays and flatten them
        $segmentIds = collect($clientsSection)
            ->pluck('segment')
            ->flatten()
            ->filter()
            ->toArray();

        return Segment::whereIn('id', $segmentIds)->get();
    }
    /**
     * Get related products from `product` JSON column
     */



    public function relatedProducts(): Collection
    {
        $products = $this->product;

        /**
         * Step 1: Decode related products JSON
         */
        if (is_string($products)) {
            $products = json_decode($products, true);
            if (!is_array($products)) {
                $products = [];
            }
        }

        /**
         * Step 2: Extract product IDs
         */
        $productIds = collect($products)
            ->pluck('product_id')
            ->filter()
            ->toArray();

        if (empty($productIds)) {
            return collect([]);
        }

        /**
         * Step 3: Map product_id => allowed variations
         */
        $selectedVariationsByProduct = collect($products)
            ->mapWithKeys(function ($item) {
                return [
                    $item['product_id'] => $item['variations'] ?? []
                ];
            });

        /**
         * Step 4: Fetch products
         */
        $productsCollection = self::whereIn('id', $productIds)->get();

        /**
         * Step 5: Filter food_variations per product
         */
        $formattedProducts = $productsCollection->map(function ($item) use ($selectedVariationsByProduct) {

            $allowedVariations = $selectedVariationsByProduct[$item->id] ?? [];

            // Decode food_variations
            $foodVariations = is_string($item->food_variations)
                ? json_decode($item->food_variations, true)
                : $item->food_variations;

            if (is_array($foodVariations)) {
                foreach ($foodVariations as &$variationGroup) {
                    if (!empty($variationGroup['values'])) {
                        $variationGroup['values'] = collect($variationGroup['values'])
                            ->filter(function ($value) use ($allowedVariations) {
                                return in_array($value['label'], $allowedVariations);
                            })
                            ->values()
                            ->toArray();
                    }
                }

                // Remove empty variation groups
                $foodVariations = array_values(array_filter($foodVariations, function ($group) {
                    return !empty($group['values']);
                }));
            }

            // Assign filtered variations back
            $item->food_variations = $foodVariations;

            /**
             * Step 6: Apply existing formatter
             */
            return Helpers::product_data_formatting(
                $item,
                false,
                true,
                app()->getLocale()
            );
        });

        return $formattedProducts->values();
    }

    /**
     * Get related products from `product_b` JSON column
     */
    public function relatedProductsB(): Collection
    {
        $products = $this->product_b;

        /**
         * Step 1: Decode related products JSON
         */
        if (is_string($products)) {
            $products = json_decode($products, true);
            if (!is_array($products)) {
                $products = [];
            }
        }

        /**
         * Step 2: Extract product IDs
         */
        $productIds = collect($products)
            ->pluck('product_id')
            ->filter()
            ->toArray();

        if (empty($productIds)) {
            return collect([]);
        }

        /**
         * Step 3: Map product_id => allowed variations
         */
        $selectedVariationsByProduct = collect($products)
            ->mapWithKeys(function ($item) {
                return [
                    $item['product_id'] => $item['variations'] ?? []
                ];
            });

        /**
         * Step 4: Fetch products
         */
        $productsCollection = self::whereIn('id', $productIds)->get();

        /**
         * Step 5: Filter food_variations per product
         */
        $formattedProducts = $productsCollection->map(function ($item) use ($selectedVariationsByProduct) {

            $allowedVariations = $selectedVariationsByProduct[$item->id] ?? [];

            // Decode food_variations
            $foodVariations = is_string($item->food_variations)
                ? json_decode($item->food_variations, true)
                : $item->food_variations;

            if (is_array($foodVariations)) {
                foreach ($foodVariations as &$variationGroup) {
                    if (!empty($variationGroup['values'])) {
                        $variationGroup['values'] = collect($variationGroup['values'])
                            ->filter(function ($value) use ($allowedVariations) {
                                return in_array($value['label'], $allowedVariations);
                            })
                            ->values()
                            ->toArray();
                    }
                }

                // Remove empty variation groups
                $foodVariations = array_values(array_filter($foodVariations, function ($group) {
                    return !empty($group['values']);
                }));
            }

            // Assign filtered variations back
            $item->food_variations = $foodVariations;

            /**
             * Step 6: Apply existing formatter
             */
            return Helpers::product_data_formatting(
                $item,
                false,
                true,
                app()->getLocale()
            );
        });

        return $formattedProducts->values();
    }


    /**
     * All categories (ordered)
     */
    public function categories(): Collection
    {
        $data = $this->category_id;

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!is_array($data)) {
            return collect();
        }

        $ids = collect($data)->pluck('id')->toArray();

        return Category::whereIn('id', $ids)->get();
    }

    /**
     * Main category (last one)
     */
    public function mainCategory(): ?Category
    {
        $data = $this->category_id;

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!is_array($data) || empty($data)) {
            return null;
        }

        $last = collect($data)->last();

        return Category::find($last['id'] ?? null);
    }

    /**
     * Sub categories (all except last)
     */
    public function subCategories(): Collection
    {
        $data = $this->category_ids;

        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!is_array($data) || empty($data)) {
            return new Collection();
        }

        // If ONLY one category → treat as sub category
        if (count($data) === 1) {
            return Category::where('id', $data[0]['id'])->get();
        }

        // More than one → all except last are sub categories
        $subIds = collect($data)
            ->slice(0, -1)
            ->pluck('id')
            ->toArray();

        return Category::whereIn('id', $subIds)->get();
    }



    public function getGiftOccasionsAttribute()
    {
        $ids = $this->occasions_id;

        // If it's empty or null, return null
        if (empty($ids)) {
            return null;
        }

        // Decode JSON safely if needed
        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        // If after decoding it's still empty, return null
        if (empty($ids) || !is_array($ids)) {
            return null;
        }

        // Return collection of GiftOccasions
        return GiftOccasions::whereIn('id', $ids)->get();
    }


    public function getMessageTemplatesAttribute()
    {
        $id = $this->message_template_style;
        if (empty($id)) {
            return collect();
        }
        // If it's a single ID (string/int), wrap in array for whereIn
        $ids = is_array($id) ? $id : [$id];
        return MessageTemplate::whereIn('id', $ids)->get();
    }


    // public function getDeliveryOptionsAttribute()
    // {
    //     return empty($this->delivery_options)
    //         ? collect()
    //         : DeliveryOption::whereIn('id', $this->delivery_options)->get();
    // }
    // public function getDeliveryOptionsAttribute($value)
    // {
    //     if (empty($value)) {
    //         return collect();
    //     }

    //     // If it's a JSON string, decode it
    //     if (is_string($value)) {
    //         $decoded = json_decode($value, true);
    //         if (json_last_error() === JSON_ERROR_NONE) {
    //             $value = $decoded;
    //         }
    //     }

    //     // If it's a comma-separated string (unlikely but possible), explode it
    //     if (is_string($value)) {
    //          $value = explode(',', $value);
    //     }

    //     // Ensure it's an array
    //     if (!is_array($value)) {
    //          $value = [$value];
    //     }

    //     return DeliveryOption::whereIn('id', $value)->get();
    // }

    public function getDeliveryOptionsAttribute($value)
    {
        if (empty($value)) {
            return collect();
        }

        // Decode JSON if needed
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                // Fallback: comma-separated string
                $value = explode(',', $value);
            }
        }

        // Ensure array
        if (!is_array($value)) {
            $value = [$value];
        }

        // Normalize IDs
        $ids = collect($value)
            ->map(fn($id) => trim($id))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();

        if (empty($ids)) {
            return collect();
        }

        return DeliveryOption::whereIn('id', $ids)->get();
    }

  public function usageTerms(): Collection
    {
        $usageTermIds = $this->how_and_condition_ids; // JSON column in items table

        // Decode JSON safely
        if (is_string($usageTermIds)) {
            $usageTermIds = json_decode($usageTermIds, true);
            if (is_string($usageTermIds)) {
                $usageTermIds = json_decode($usageTermIds, true);
            }
        }

        if (!is_array($usageTermIds)) {
            $usageTermIds = [];
        }

        // Fetch related usage terms
        return \App\Models\WorkManagement::whereIn('id', $usageTermIds)->get();
    }


  public function getUsageTerms(): Collection
{
    $usageTermIds = $this->how_and_condition_ids; // JSON column

    if (is_string($usageTermIds)) {
        $usageTermIds = json_decode($usageTermIds, true);
        if (is_string($usageTermIds)) {
            $usageTermIds = json_decode($usageTermIds, true);
        }
    }

    if (!is_array($usageTermIds)) {
        $usageTermIds = [];
    }

    return \App\Models\WorkManagement::whereIn('id', $usageTermIds)->get();
}

    /**  Relation: Item → VoucherSetting */
    public function voucherSetting()
    {
        return $this->hasOne(VoucherSetting::class, 'item_id', 'id');
    }


    /** Get full HolidayOccasion objects */
    public function holidays()
    {
        return HolidayOccasion::whereIn('id', $this->holidays_occasions ?? [])->get();
    }

    /** Optional accessor for API */
    public function getHolidaysOccasionsAttribute()
    {
        return $this->holidays();
    }







    public function getBranchesAttribute()
    {
        $branchIds = $this->branch_ids ?? [];

        if (is_string($branchIds)) {
            $branchIds = json_decode($branchIds, true);
        }

        if (!is_array($branchIds)) {
            $branchIds = [];
        }
        $stores = Store::whereIn('id', $branchIds)->get();
     
        return $stores;
    }

    public function termsAndConditions(): Collection
    {
        $termIds = $this->term_and_condition_ids;

        if (is_string($termIds)) {
            $termIds = json_decode($termIds, true);
            if (is_string($termIds)) {
                $termIds = json_decode($termIds, true);
            }
        }

        if (!is_array($termIds)) {
            $termIds = [];
        }

        return \App\Models\UsageTermManagement::whereIn('id', $termIds)->get();
    }

}
