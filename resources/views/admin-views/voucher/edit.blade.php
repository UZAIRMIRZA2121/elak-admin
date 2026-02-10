@extends('layouts.admin.app')

@section('title', translate('messages.add_new_item'))
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
@endpush

<style>
#selectedItemsSection .badge {
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
}

#selectedItemsSection {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

@section('content')
@php
    // Prepare existing images data for JavaScript (used in multiple includes)
    $existingImages = is_array($product->images) ? $product->images : json_decode($product->images ?? '[]', true);
    $imageUrls = [];
    if (!empty($existingImages) && is_array($existingImages)) {
        foreach ($existingImages as $image) {
            $imgPath = is_array($image) ? ($image['img'] ?? '') : $image;
            if ($imgPath) {
                $imageUrls[] = asset('storage/product/' . $imgPath);
            }
        }
    }
@endphp
  <link rel="stylesheet" href="{{asset('public/assets/admin/css/voucher.css')}}">
  <link rel="stylesheet" href="{{asset('assets/admin/css/voucher.css')}}">
     <!-- Page Header -->
     <div class="container-fluid px-4 py-3">
          @include("admin-views.voucher.edit_include.edit_include_heading")
        <div class="bg-white shadow rounded-lg p-4">


            {{-- Step 1: Select Voucher Type and Step 2: Select Management Type  --}}
             @include("admin-views.voucher.edit_include.edit_include_client_voucher_management")

            <form action="{{ route('admin.Voucher.update', $product->id) }}" method="post" id="item_form" enctype="multipart/form-data">
                 <input type="hidden" name="hidden_value" id="hidden_value" value="1"/>
                <input type="hidden" name="hidden_bundel" id="hidden_bundel" value="simple"/>
                <input type="hidden" name="hidden_name" id="hidden_name" value="{{ $product->voucher_ids ?? 'Delivery/Pickup' }}"/>
                <input type="hidden" name="hidden_voucher_id" id="hidden_voucher_id" value="{{ $product->id ?? '' }}"/>
                @csrf
                @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                @php($language = $language->value ?? null)
                @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                {{-- Client Information and Partner Information --}}
                 @include("admin-views.voucher.edit_include.edit_include_client_partner_information")




                   <!-- Voucher Details  Bundle Delivery/Pickup  == Food and Product Bundle-->
                    <div class="section-card rounded p-4 mb-4" id="bundel_food_voucher_fields_1_3_1_4">
                        <h3 class="h5 fw-semibold mb-4">Voucher Details</h3>
                        {{-- Voucher Title --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                 <label class="input-label" for="voucher_title">{{ translate('Voucher Title') }}
                                    <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
                                </label>
                                <input type="text" name="voucher_title" class="form-control" placeholder="Voucher Title" value="{{ $product->name ?? '' }}">
                            </div>
                            {{-- <div class="col-6">
                                <label class="form-label fw-medium">Valid Until</label>
                                <input type="date" name="valid_until" class="form-control">
                            </div> --}}
                        </div>
                            {{-- images --}}
                        <div class="row g-3">
                            <div class="col-12" >
                                @include("admin-views.voucher.edit_include.edit_include_images")
                            </div>
                        </div>
                        {{-- images  --}}
                        <div class="row g-3">
                            <div class="mb-3 col-12 ">
                                <label class="form-label fw-medium">Short Description (Default) <span class="text-danger">*</span></label>
                                <textarea type="text" name="description" class="form-control min-h-90px ckeditor">{{ $product->description ?? '' }}</textarea>
                            </div>
                        </div>

                        {{-- tags --}}
                        <div class="col-12 mt-3">
                            <div class="form-group">
                                <h3 class="h5 fw-semibold "> {{ translate('tags') }}</h3>
                                <input type="text" class="form-control" name="tags" placeholder="{{translate('messages.search_tags')}}" data-role="tagsinput" value="{{ $product->tags_ids ?? '' }}">
                            </div>
                        </div>
                    </div>

                   {{-- Bundle Products Configuration --}}
                    <div class="section-card rounded p-4 mb-4"  >
                        {{-- Bundle Type Selection --}}
                     <div class="col-12 col-md-12">
                         <div class="form-group mb-0">
                              <label class="input-label" for="bundle_offer_type">{{ translate('Bundle Type Selection') }}
                                    <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
                                </label>

                             <select name="bundle_offer_type" id="bundle_offer_type" class="form-control" >
                                 <option value="">Select Bundle Offer Type</option>
                                 <option value="simple" {{ ($product->bundle_type ?? '') == 'simple' ? 'selected' : '' }}>
                                     Simple
                                 </option>
                                 <option value="simple x" {{ ($product->bundle_type ?? '') == 'simple x' ? 'selected' : '' }}>
                                     Simple X
                                 </option>
                                 <option value="bundle" {{ ($product->bundle_type ?? '') == 'bundle' ? 'selected' : '' }}>
                                     Fixed Bundle - Specific products at set price
                                 </option>
                                 <option value="bogo_free" {{ ($product->bundle_type ?? '') == 'bogo_free' ? 'selected' : '' }}>
                                 Buy X Get Y - Buy products get different product free
                                 </option>
                                 <option value="mix_match" {{ ($product->bundle_type ?? '') == 'mix_match' ? 'selected' : '' }}>
                                     Mix & Match - Customer chooses from categories
                                 </option>
                             </select>
                         </div>
                     </div>
                    </div>
                    <div class="section-card rounded p-4 mb-4"  id="Bundle_products_configuration">
                            <label class="input-label" for="bundle_offer_type">{{ translate('Bundle Products Configuration') }}
                            <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
                        </label>

                        <div id="selectedProducts">
                            @php( $existingProducts = json_decode($product->product ?? '[]', true))
                                
                            
                            @if(!empty($existingProducts) && is_array($existingProducts))
                                {{-- Render existing products --}}
                                    {{-- Server-side rendering removed to rely on JS --}}
                                    {{-- Hidden input for all products --}}
                                    <input type="hidden" class="hidden-product-input" name="products_data" value='{{ json_encode($existingProducts) }}'>
                                

                            @else
                                <p style="text-align: center; color: #666; padding: 20px;">No products added yet. Click "Add Product to Bundle" to start.</p>
                            @endif
                        </div>
                        <button type="button" class="btn btn--primary" id="addProductBtn">+ Add Product to Bundle</button>
                        <!-- Available Products to Choose From -->
                        <div id="availableProducts" style="display: none;">
                            <h3 class="mt-3">Available Products: <span class="text-danger"> *</span></h3>
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    <div class="form-group">
                                        <select name="select_pro" id="select_pro" class="form-control js-select2-custom" data-placeholder="{{ translate('Select Product') }}" >
                                            <option value="" disabled selected>{{ translate('Select a Product') }}</option>
                                            @foreach (\App\Models\Item::whereIn('type', ['Food','Product'])->get() as $item)
                                                @php(
                                                    $variations = json_decode($item->food_variations, true) ?? []
                                                )
                                                @php(
                                                    $addonIds = json_decode($item->add_ons, true) ?? []
                                                )
                                                @php(
                                                    $addonDetails = []
                                                )
                                                
                                                @if(!empty($addonIds))
                                                    @foreach($addonIds as $addonId)
                                                        @php(
                                                            $addon = \App\Models\AddOn::find($addonId)
                                                        )
                                                        @if($addon)
                                                            @php(
                                                                $addonDetails[] = [
                                                                    'id' => $addon->id,
                                                                    'name' => $addon->name,
                                                                    'price' => $addon->price
                                                                ]
                                                            )
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <option value="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price }}"
                                                        data-variations='@json($variations)'
                                                        data-addons='@json($addonDetails)'>
                                                    {{ $item->name }}
                                                </option>
                                                @endforeach
                                        </select>
                                    </div>
                                     
                                    {{-- Product selection area --}}
                                    <div id="productDetails" class="mt-3 row mx-1"></div>

                                    {{-- Selected items display section --}}
                                    <div id="selectedItemsSection" class="mt-4" style="display: none;">
                                        <div class="card p-3 shadow-sm bg-light">
                                            <h5 class="mb-3">Selected Configuration</h5>

                                            <div id="selectedProductInfo" class="mb-2"></div>

                                            <div id="selectedVariationInfo" class="mb-2" style="display: none;">
                                                <strong>Selected Variation:</strong>
                                                <div id="selectedVariationDetails" class="ml-3"></div>
                                            </div>

                                            <div id="selectedAddonsInfo" class="mb-2" style="display: none;">
                                                <strong>Selected Add-ons:</strong>
                                                <div id="selectedAddonsDetails" class="ml-3"></div>
                                            </div>

                                            <hr>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>Final Total:</strong>
                                                <h5 class="text-success mb-0" id="finalTotalPrice">$0.00</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="availableProducts_get_x_buy_y" class="mt-3 rounded" style="display: none;">
                            <div class="row">
                                <div class="col-6 " style="border-right: 1px solid rgb(223 219 219)">
                                    <h3 class="mt-3">Available Products A: <span class="text-danger"> *</span></h3>
                                    <div class="form-group">
                                        <select name="select_pro1" id="select_pro1" class="form-control js-select2-custom" data-placeholder="{{ translate('Select Product') }}" >
                                            <option value="" disabled selected>{{ translate('Select a Product') }}</option>
                                            @foreach (\App\Models\Item::whereIn('type', ['Food','Product'])->get() as $item)
                                                @php(
                                                    $variations = json_decode($item->food_variations, true) ?? []
                                                )
                                                @php(
                                                    $addonIds = json_decode($item->add_ons, true) ?? []
                                                )
                                                @php(
                                                    $addonDetails = []
                                                )
                                                @if(!empty($addonIds))
                                                    @foreach($addonIds as $addonId)
                                                        @php(
                                                            $addon = \App\Models\AddOn::find($addonId)
                                                        )
                                                        @if($addon)
                                                            @php(
                                                                $addonDetails[] = [
                                                                    'id' => $addon->id,
                                                                    'name' => $addon->name,
                                                                    'price' => $addon->price
                                                                ]
                                                            )
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <option value="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price }}"
                                                        data-variations='@json($variations)'
                                                        data-addons='@json($addonDetails)'>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="productDetails_section_a" class="mt-3 row mx-1"></div>
                                </div>
                                <div class="col-6 ">
                                    <h3 class="mt-3">Available Products B: <span class="text-danger"> *</span></h3>
                                    <div class="form-group">
                                        <select name="select_pro2" id="select_pro2" class="form-control js-select2-custom" data-placeholder="{{ translate('Select Product') }}" >
                                            <option value="" disabled selected>{{ translate('Select a Product') }}</option>
                                            @foreach (\App\Models\Item::whereIn('type', ['Food','Product'])->get() as $item)
                                                @php(
                                                    $variations = json_decode($item->food_variations, true) ?? []
                                                )
                                                @php(
                                                    $addonIds = json_decode($item->add_ons, true) ?? []
                                                )
                                                @php(
                                                    $addonDetails = []
                                                )
                                                @if(!empty($addonIds))
                                                    @foreach($addonIds as $addonId)
                                                        @php(
                                                            $addon = \App\Models\AddOn::find($addonId)
                                                        )
                                                        @if($addon)
                                                            @php(
                                                                $addonDetails[] = [
                                                                    'id' => $addon->id,
                                                                    'name' => $addon->name,
                                                                    'price' => $addon->price
                                                                ]
                                                            )
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <option value="{{ $item->id }}"
                                                        data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price }}"
                                                        data-variations='@json($variations)'
                                                        data-addons='@json($addonDetails)'>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="productDetails_section_b" class="mt-3 row mx-1"></div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="container mt-5 p-1">
                            <div class="form-container">
                                <!-- Price Calculator -->
                                <div class="price-calculator" id="priceCalculator" style="display: none;">
                                    <h3> Bundle Price Calculation</h3>
                                    <div id="priceBreakdown"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                   <!--  Price Information one  Bundle Delivery/Pickup  == Food and Product Bundle-->
                    <div class="section-card rounded p-4 mb-4  "id="bundel_food_voucher_price_info_1_3_1_4">
                        <h3 class="h5 fw-semibold mb-4"> {{ translate('Price Information') }}</h3>
                        {{-- Price Information --}}
                        <div class="row g-2">
                            <div class="col-12 d-none" id="actual_price_input_hide">
                                <div class="form-group mb-0">
                                    <label class="input-label"  for="exampleFormControlInput1">{{ translate('Actual Price') }} <span class="form-label-secondary text-danger"  data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *  </span> </label>
                                    <input type="number" min="0" id="actual_price" max="999999999999.99" step="0.01" value="{{ $product->actual_price }}" name="actual_price_input_hide" class="form-control"placeholder="{{ translate('messages.Ex:') }} 100" required>
                                    <!-- <input type="hidden"  id="actual_price_input_hide"name="actual_price_input_hide" >
                                    <input type="hidden"  id="product_real_price"name="product_real_price" value="{{ $product->price }}" > -->
                                </div>
                            </div>
                            <div class="col-6 col-md-3" id="price_input_hide">
                                <div class="form-group mb-0">
                                    <label class="input-label"  for="exampleFormControlInput1">{{ translate('messages.price') }} <span class="form-label-secondary text-danger"  data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *  </span> </label>
                                    <input type="number" min="0" id="price" max="999999999999.99" step="0.01" value="{{ $product->price }}" name="price" class="form-control"placeholder="{{ translate('messages.Ex:') }} 100" required>
                                    <input type="hidden"  id="price_hidden"name="price_hidden" value="{{ $product->price }}">
                                </div>
                            </div>
                            <div class="col-6 col-md-3 d-none" id="required_qty">
                                <div class="form-group mb-0">
                                    <label class="input-label"  for="exampleFormControlInput1">{{ translate('Required Quantity') }} <span class="form-label-secondary text-danger"  data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *  </span> </label>
                                    <input type="number" min="0" id="required_qty" max="999999999999.99" step="0.01" value="{{ $product->required_quantity }}" name="required_qty" class="form-control"placeholder="{{ translate('messages.Ex:') }} 100" required>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="offer_type">{{ translate('Offer Type') }}
                                    </label>
                                    <!-- Dropdown: Only Percent & Fixed -->
                                    <select name="offer_type" id="offer_type" class="form-control js-select2-custom">
                                        <option value="direct discount" {{ $product->offer_type == 'direct discount' ? 'selected' : '' }}>{{ translate('Direct Discount') }} </option>
                                        <option value="cash back" {{ $product->offer_type == 'cash back' ? 'selected' : '' }}>{{ translate('Cash back') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-3" id="discount_input_hide">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="discount_type">{{ translate('Discount Type') }}
                                    </label>
                                    <!-- Dropdown: Only Percent & Fixed -->
                                    <select name="discount_type" id="discount_type"
                                        class="form-control js-select2-custom">
                                        <option value="percent" {{ $product->discount_type == 'percent' ? 'selected' : '' }}>{{ translate('messages.percent') }} (%)</option>
                                        <option value="fixed" {{ $product->discount_type == 'fixed' ? 'selected' : '' }}>{{ translate('Fixed') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-3" id="discount_value_input_hide">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('Discount Value') }}
                                        </label>
                                        <input type="number" min="0" max="9999999999999999999999" value="{{ $product->discount }}"
                                            name="discount" id="discount" class="form-control"
                                            placeholder="{{ translate('messages.Ex:') }} 100">
                                </div>
                            </div>
                        </div>
                    </div>


                    @include("admin-views.voucher.edit_include.edit_include_voucher")

            </form>
        </div>
      </div>

      @include("admin-views.voucher.edit_include.edit_include_model")



@endsection


@push('script_2')
{{-- dashboard code --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{asset('public/assets/admin')}}/js/view-pages/product-index.js"></script>
<script>

    // Elements
    const actualPrice = document.getElementById("actual_price");
    const price = document.getElementById("price");
    const discountType = document.getElementById("discount_type");
    const discountInput = document.getElementById("discount");

    // Function to update final price
    function updatePrice() {
        const actual = parseFloat(actualPrice.value) || 0;
        let discount = parseFloat(discountInput.value) || 0;
        let finalPrice = actual;

        // Percent Discount
        if (discountType.value === "percent") {
            finalPrice = actual - (actual * discount / 100);
        }

        // Fixed Discount
        if (discountType.value === "fixed") {
            finalPrice = actual - discount;
        }

        // Final price cannot be negative
        if (finalPrice < 0) finalPrice = 0;

        price.value = finalPrice.toFixed(2);
    }

    // When Actual Price Changes → Price = Actual Price
    actualPrice.addEventListener("input", updatePrice);

    // When Discount Type Changes → Recalculate
    discountType.addEventListener("change", updatePrice);

    // When Discount Value Changes → Recalculate
    discountInput.addEventListener("input", updatePrice);


</script>
 
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#select_pro, #select_pro1, #select_pro2').select2({
                width: '100%',
                placeholder: 'Select a Product'
            });

            // Store selected products
            let selectedProductsArray = [];
            
            // Set productCounter to existing products count
            @php($existingProductsJS = json_decode($product->product ?? '[]', true))
            @php($productCountJS = is_array($existingProductsJS) ? count($existingProductsJS) : 0)
            let productCounter = {{ $productCountJS }};
            
            // BOGO specific storage
            let bogoSelectedProductsA = [];
            let bogoSelectedProductsB = [];
            let bogoCounterA = 0;
            let bogoCounterB = 0;

            // On page load
            let bundleType = $('#bundle_offer_type').val();
            updateFieldsVisibility(bundleType);

            // Flag to prevent listener loops during initialization
            let isInitializingSavedProducts = false;

             function initializeSavedProducts() {
                isInitializingSavedProducts = true; // Start initialization
                
                try {
                
                let savedProducts = @json(json_decode($product->product ?? '[]', true));
                let savedProductsB = @json(json_decode($product->product_b ?? '[]', true));
                let bundleType = $('#bundle_offer_type').val();

                // Trigger visibility update
                updateFieldsVisibility(bundleType);

                if (bundleType === 'simple' || bundleType === 'bundle' || bundleType === 'mix_match') {
                    if (savedProducts && savedProducts.length > 0) {
                        $('#availableProducts').show(); // Force show interactive section
                        $('#selectedProducts p').hide(); // Hide "No products" text
                        
                        savedProducts.forEach(function(item) {
                            productCounter++;
                            
                            // 1. Find the Option element to get FULL variations data
                            let $option = $(`#select_pro option[value="${item.product_id}"]`);
                            let fullVariations = [];
                            
                            if ($option.length) {
                                 fullVariations = $option.data('variations'); 
                            } else {
                                // Fallback if product not found in dropdown (e.g. deleted)
                                fullVariations = item.variations || [];
                            }

                            // 2. Create card with FULL variations
                            let itemPrice = parseFloat($option.data('price')) || parseFloat(item.base_price || item.price || 0);
                            const cardHtml = createProductCard(item.product_id, item.product_name, itemPrice, fullVariations, productCounter);
                            $('#productDetails').append(cardHtml);
                            
                            // 3. Update global array
                            addToSelectedProducts(item.product_id, item.product_name, itemPrice, fullVariations, bundleType, null, productCounter);
                            
                            // 4. Check saved variations (The saved data contains the *selected* ones)
                            // item.variations usually contains the selected ones in the DB structure we saw earlier
                             let variationsToCheck = item.variations || [];
                             // Also check item.selected_variations if it exists (legacy support)
                             if (item.selected_variations) {
                                 variationsToCheck = item.selected_variations;
                             }

                            if (variationsToCheck && variationsToCheck.length > 0) {
                                variationsToCheck.forEach(function(v) {
                                    // Handle both string and object formats
                                    let typeToCheck = (typeof v === 'object') ? (v.type || v.label) : v;
                                    
                                    // Find checkbox by value
                                    let checkbox = $(`input.variation-checkbox[data-temp-id="${productCounter}"][value="${typeToCheck}"]`);
                                    if (checkbox.length) {
                                        checkbox.prop('checked', true);
                                    }
                                });
                                 // Update total after checking all boxes
                                updateProductTotal($(`.card[data-product-temp-id="${productCounter}"]`));
                            }
                            
                            // If Simple bundle, Select the product in the dropdown
                            if (bundleType === 'simple') {
                                $('#select_pro').val(item.product_id).trigger('change.select2');
                            }
                        });
                         if (bundleType === 'mix_match') {
                            updateMixMatchTotal();
                        } else {
                            updateBundleTotal();
                        }
                    }
                } else if (bundleType === 'bogo_free') {
                   // ... (BOGO logic) ...
                    $('#availableProducts_get_x_buy_y').show(); // Force show BOGO section
                    $('#selectedProducts p').hide();
    
                    // Section A
                     if (savedProducts && savedProducts.length > 0) {
                        savedProducts.forEach(function(item) {
                            bogoCounterA++;
                            
                             // 1. Find the Option element
                            let $option = $(`#select_pro1 option[value="${item.product_id}"]`);
                            let fullVariations = [];
                            if ($option.length) {
                                 fullVariations = $option.data('variations'); 
                            } else {
                                fullVariations = item.variations || [];
                            }
    
                            let itemPrice = parseFloat($option.data('price')) || parseFloat(item.base_price || item.price || 0);
                            const cardHtml = createBogoProductCard(item.product_id, item.product_name, itemPrice, fullVariations, 'a', bogoCounterA);
                            $('#productDetails_section_a').append(cardHtml);
    
                             // Add to bogo array
                            bogoSelectedProductsA.push({
                                product_id: item.product_id,
                                name: item.product_name,
                                price: itemPrice,
                                variations: fullVariations,
                                selected_variations: [],
                                temp_id: bogoCounterA
                            });
    
                             // Check saved variations
                            let variationsToCheck = item.variations || [];
                             if (item.selected_variations) {
                                 variationsToCheck = item.selected_variations;
                             }
    
                            if (variationsToCheck && variationsToCheck.length > 0) {
                                variationsToCheck.forEach(function(v) {
                                    let typeToCheck = (typeof v === 'object') ? (v.type || v.label) : v;
                                    let checkbox = $(`input.bogo-variation-checkbox[data-temp-id="${bogoCounterA}"][data-section="a"][value="${typeToCheck}"]`);
                                    if (checkbox.length) {
                                        checkbox.prop('checked', true);
                                    }
                                });
                                 updateBogoProductTotal($(`.card[data-bogo-counter="${bogoCounterA}"][data-bogo-section="a"]`));
                            }
                        });
                    }
    
                    // Section B
                     if (savedProductsB && savedProductsB.length > 0) {
                        savedProductsB.forEach(function(item) {
                            bogoCounterB++;
                            
                             // 1. Find the Option element
                            let $option = $(`#select_pro2 option[value="${item.product_id}"]`);
                            let fullVariations = [];
                            if ($option.length) {
                                 fullVariations = $option.data('variations'); 
                            } else {
                                fullVariations = item.variations || [];
                            }
                            
                            let itemPrice = parseFloat($option.data('price')) || parseFloat(item.base_price || item.price || 0);
                            const cardHtml = createBogoProductCard(item.product_id, item.product_name, itemPrice, fullVariations, 'b', bogoCounterB);
                            $('#productDetails_section_b').append(cardHtml);
    
                             // Add to bogo array
                            bogoSelectedProductsB.push({
                                product_id: item.product_id,
                                name: item.product_name,
                                price: itemPrice,
                                variations: fullVariations,
                                selected_variations: [],
                                temp_id: bogoCounterB
                            });
    
                             // Check saved variations
                            let variationsToCheck = item.variations || [];
                             if (item.selected_variations) {
                                 variationsToCheck = item.selected_variations;
                             }
    
                            if (variationsToCheck && variationsToCheck.length > 0) {
                                variationsToCheck.forEach(function(v) {
                                    let typeToCheck = (typeof v === 'object') ? (v.type || v.label) : v;
                                    let checkbox = $(`input.bogo-variation-checkbox[data-temp-id="${bogoCounterB}"][data-section="b"][value="${typeToCheck}"]`);
                                    if (checkbox.length) {
                                        checkbox.prop('checked', true);
                                    }
                                });
                                updateBogoProductTotal($(`.card[data-bogo-counter="${bogoCounterB}"][data-bogo-section="b"]`));
                            }
                        });
                    }
                    updateBogoTotal();
                }

                 // Hide placeholder if products exist
                if ($('#productDetails .card').length > 0 || $('#productDetails_section_a .card').length > 0 || $('#productDetails_section_b .card').length > 0) {
                    $('#selectedProducts p').hide();
                }

                } catch (e) {
                     console.error("Error initializing saved products:", e);
                } finally {
                     isInitializingSavedProducts = false; // End initialization
                }
            }
            
            // Call it!
            initializeSavedProducts();

            // Discount field change event
            $('#discount, #discount_type, #discount_value, #required_qty').on('change input', function() {
                let bundleType = $('#bundle_offer_type').val();
                
                if (bundleType === 'mix_match') {
                    updateMixMatchTotal();
                } else if (bundleType === 'simple' || bundleType === 'bundle') {
                    updateBundleTotal();
                }
            });

            // ==================== ADD PRODUCT TO BUNDLE ====================
            $('#addProductBtn').on('click', function() {
                let bundleType = $('#bundle_offer_type').val();
                
                if (bundleType === 'bogo_free') {
                    $('#availableProducts_get_x_buy_y').show();
                    $('#availableProducts').hide();
                } else if (bundleType === 'simple') {
                    if (selectedProductsArray.length > 0) {
                        alert('Simple bundle can only have 1 product.');
                        return;
                    }
                    $('#availableProducts').show();
                    $('#availableProducts_get_x_buy_y').hide();
                } else {
                    $('#availableProducts').show();
                    $('#availableProducts_get_x_buy_y').hide();
                }
            });

            // ==================== CHECK IF PRODUCT EXISTS ====================
            function productExists(productId, bundleType, section = null) {
                if (bundleType === 'simple' || bundleType === 'bundle' || bundleType === 'mix_match') {
                    return selectedProductsArray.some(product => product.id === productId);
                } else if (bundleType === 'bogo_free') {
                    if (section === 'a') {
                        return bogoSelectedProductsA.some(product => product.id === productId);
                    } else if (section === 'b') {
                        return bogoSelectedProductsB.some(product => product.id === productId);
                    }
                }
                return false;
            }

            // ==================== ADD TO SELECTED PRODUCTS ARRAY ====================
            function addToSelectedProducts(productId, productName, basePrice, variationsData, bundleType, section = null, tempId = null) {
                const productObj = {
                    id: productId,
                    name: productName,
                    base_price: basePrice,
                    temp_id: tempId,
                    variations: [] // Will store selected variations
                };
                
                if (bundleType === 'bogo_free') {
                    if (section === 'a') {
                        bogoSelectedProductsA.push(productObj);
                    } else if (section === 'b') {
                        bogoSelectedProductsB.push(productObj);
                    }
                } else {
                    selectedProductsArray.push(productObj);
                }
            }

            // ==================== UPDATE PRODUCT VARIATIONS IN ARRAY ====================
            function updateProductVariationsInArray(productTempId, selectedVariations, bundleType, section = null) {
                let productsArray;
                
                if (bundleType === 'bogo_free') {
                    if (section === 'a') {
                        productsArray = bogoSelectedProductsA;
                    } else if (section === 'b') {
                        productsArray = bogoSelectedProductsB;
                    }
                } else {
                    productsArray = selectedProductsArray;
                }
                
                // Find product by temp_id and update variations
                const product = productsArray.find(p => p.temp_id === productTempId);
                if (product) {
                    product.variations = selectedVariations;
                }
            }

            // ==================== CREATE HIDDEN INPUTS FOR FORM SUBMISSION ====================
            function createHiddenInputs() {
                // Clear existing hidden inputs
                $('.hidden-product-input').remove();
                $('.hidden-bogo-a-input').remove();
                $('.hidden-bogo-b-input').remove();
                
                let bundleType = $('#bundle_offer_type').val();
                
                if (bundleType === 'simple' || bundleType === 'bundle' || bundleType === 'mix_match') {
                    // Create JSON array for regular bundles
                    const productsData = selectedProductsArray.map(product => {
                        return {
                            product_id: product.id,
                            product_name: product.name,
                            base_price: product.base_price,
                            variations: product.variations || []
                        };
                    });
                    
                    // Create hidden input with JSON data
                    const hiddenInput = `
                        <input type="hidden" class="hidden-product-input" 
                               name="products_data" value='${JSON.stringify(productsData).replace(/'/g, "&apos;")}'>
                               
                    `;
                    $('#productDetails').append(hiddenInput);
                } else if (bundleType === 'bogo_free') {
                    // Create JSON arrays for BOGO
                    const bogoProductsA = bogoSelectedProductsA.map(product => {
                        return {
                            product_id: product.id,
                            product_name: product.name,
                            base_price: product.base_price,
                            variations: product.variations || []
                        };
                    });
                    
                    const bogoProductsB = bogoSelectedProductsB.map(product => {
                        return {
                            product_id: product.id,
                            product_name: product.name,
                            base_price: product.base_price,
                            variations: product.variations || []
                        };
                    });
                    
                    // Create hidden inputs with JSON data
                    const hiddenInputA = `
                        <input type="hidden" class="hidden-bogo-a-input" 
                               name="bogo_products_a" value='${JSON.stringify(bogoProductsA).replace(/'/g, "&apos;")}'>
                    `;
                    
                    const hiddenInputB = `
                        <input type="hidden" class="hidden-bogo-b-input" 
                               name="bogo_products_b" value='${JSON.stringify(bogoProductsB).replace(/'/g, "&apos;")}'>
                    `;
                    
                    $('#productDetails_section_a').append(hiddenInputA);
                    $('#productDetails_section_b').append(hiddenInputB);
                }
            }

            // Regular product selection
            $('#select_pro').on('change', function() {
                if (typeof isInitializingSavedProducts !== 'undefined' && isInitializingSavedProducts) return;

                const selectedOption = $(this).find('option:selected');
                const productId = selectedOption.val();
                const productName = selectedOption.data('name');
                const basePrice = parseFloat(selectedOption.data('price')) || 0;
                const variations = selectedOption.data('variations');
                
                if (!productId) return;
                
                let bundleType = $('#bundle_offer_type').val();
                
                // For simple bundle, replace if exists
                if (bundleType === 'simple' && selectedProductsArray.length > 0) {
                     if (productExists(productId, bundleType)) {
                        alert('This product is already selected!');
                        return;
                    }
                    // Remove existing
                    $('#productDetails').empty();
                    selectedProductsArray = [];
                    createHiddenInputs();
                } else {
                     // Check if product already exists (for other types)
                    if (productExists(productId, bundleType)) {
                        alert('This product is already added!');
                        $(this).val('');
                        return;
                    }
                }
                
                productCounter++;
                const cardHtml = createProductCard(productId, productName, basePrice, variations, productCounter);
                $('#productDetails').append(cardHtml);
                
                // Add to selected products array
                addToSelectedProducts(productId, productName, basePrice, variations, bundleType, null, productCounter);
                
                // Create hidden inputs
                createHiddenInputs();
                
                // Update total
                if (bundleType === 'mix_match') {
                    updateMixMatchTotal();
                } else {
                    updateBundleTotal();
                }
                
                // Only clear dropdown if NOT simple bundle (user wants to see selection)
                if (bundleType !== 'simple') {
                     $(this).val('').trigger('change.select2'); 
                }
                $('#selectedProducts p').hide();
            });

            // Product A selection for BOGO
            $('#select_pro1').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const productId = selectedOption.val();
                const productName = selectedOption.data('name');
                const basePrice = parseFloat(selectedOption.data('price')) || 0;
                const variations = selectedOption.data('variations');
                
                if (!productId) return;
                
                let bundleType = $('#bundle_offer_type').val();
                
                // Check if product already exists in Section A
                if (productExists(productId, bundleType, 'a')) {
                    alert('This product is already added to Section A!');
                    $(this).val('');
                    return;
                }
                
                bogoCounterA++;
                const cardHtml = createBogoProductCard(productId, productName, basePrice, variations, 'a', bogoCounterA);
                $('#productDetails_section_a').append(cardHtml);
                
                // Add to selected products array
                addToSelectedProducts(productId, productName, basePrice, variations, bundleType, 'a', bogoCounterA);
                
                // Create hidden inputs
                createHiddenInputs();
                
                updateBogoTotal();
                
                $(this).val('');
                $('#selectedProducts p').hide();
            });

            // Product B selection for BOGO
            $('#select_pro2').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const productId = selectedOption.val();
                const productName = selectedOption.data('name');
                const basePrice = parseFloat(selectedOption.data('price')) || 0;
                const variations = selectedOption.data('variations');
                
                if (!productId) return;
                
                let bundleType = $('#bundle_offer_type').val();
                
                // Check if product already exists in Section B
                if (productExists(productId, bundleType, 'b')) {
                    alert('This product is already added to Section B!');
                    $(this).val('');
                    return;
                }
                
                bogoCounterB++;
                const cardHtml = createBogoProductCard(productId, productName, basePrice, variations, 'b', bogoCounterB);
                $('#productDetails_section_b').append(cardHtml);
                
                // Add to selected products array
                addToSelectedProducts(productId, productName, basePrice, variations, bundleType, 'b', bogoCounterB);
                
                // Create hidden inputs
                createHiddenInputs();
                
                updateBogoTotal();
                
                $(this).val('');
                $('#selectedProducts p').hide();
            });

            // ==================== CREATE PRODUCT CARD ====================
            function createProductCard(productId, productName, basePrice, variations, counter) {
                const normalizedVariations = normalizeVariations(variations);
                
                let variationsHtml = '';
                if (normalizedVariations && normalizedVariations.length > 0) {
                    variationsHtml = `
                    <div class="variations mt-2">
                        <strong>Variations:</strong>
                    `;
                    
                    // Group variations by groupName
                    const grouped = {};
                    normalizedVariations.forEach(v => {
                        const group = v.groupName || 'Options';
                        if (!grouped[group]) {
                            grouped[group] = [];
                        }
                        grouped[group].push(v);
                    });
                    
                    // Har group ke liye HTML generate karo
                    Object.keys(grouped).forEach(groupName => {
                        const groupVariations = grouped[groupName];
                        
                        variationsHtml += `
                        <div class="variation-sub-group mb-1">
                            <strong class="small">${groupName}:</strong>
                        `;
                        
                        groupVariations.forEach((v, index) => {
                            variationsHtml += `
                            <label class="ms-2 small">
                                <input
                                    type="checkbox"
                                    name="variations_${counter}"
                                    class="variation-checkbox"
                                    value="${v.type || ''}"
                                    data-price="${v.price || 0}"
                                    data-type="${v.type || 'Option'}"
                                    data-temp-id="${counter}"
                                >
                                ${v.type || 'Option'} - $${v.price || 0}
                            </label>
                            `;
                        });
                        
                        variationsHtml += `</div>`;
                    });
                    
                    variationsHtml += `</div>`;
                }
                
                let html = `
                <div class="card p-3 shadow-sm mb-3 col-12 col-md-6" data-product-temp-id="${counter}" data-product-id="${productId}">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 border rounded p-2">
                        <div class="">
                            <h5 class="mb-0">${productName}</h5>
                            ${variationsHtml}
                        </div>
                        <div class="p-2 text-nowrap">
                            <span class="product-total text-success fw-bold" style="font-size: 1.2em;">
                                $${basePrice.toFixed(2)}
                            </span>
                        </div>
                        <button
                            type="button"
                            class="btn btn-danger btn-sm remove-product-btn"
                            data-temp-id="${counter}"
                            data-product-id="${productId}"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                    <input type="hidden" class="product-id" value="${productId}">
                    <input type="hidden" class="product-name" value="${productName}">
                    <input type="hidden" class="product-base-price" value="${basePrice}">
                    <input type="hidden" class="product-temp-id" value="${counter}">
                </div>`;

                return html;
            }

            // ==================== CREATE BOGO PRODUCT CARD ====================
            function createBogoProductCard(productId, productName, basePrice, variations, section, counter) {
                const normalizedVariations = normalizeVariations(variations);
                
                let variationsHtml = '';
                if (normalizedVariations && normalizedVariations.length > 0) {
                    variationsHtml = `<div class="mt-2"><strong>Variations:</strong>`;
                    
                    const grouped = {};
                    normalizedVariations.forEach(v => {
                        const group = v.groupName || 'Options';
                        if (!grouped[group]) {
                            grouped[group] = [];
                        }
                        grouped[group].push(v);
                    });
                    
                    Object.keys(grouped).forEach(groupName => {
                        const groupVariations = grouped[groupName];
                        
                        variationsHtml += `
                        <div class="variation-sub-group mb-1">
                            <strong class="small">${groupName}:</strong>
                        `;
                        
                        groupVariations.forEach((v, index) => {
                            variationsHtml += `
                            <label class="d-block small mt-1">
                                <input
                                    type="checkbox"
                                    name="bogo_variations_${section}_${counter}"
                                    class="bogo-variation-checkbox"
                                    value="${v.type || ''}"
                                    data-price="${v.price || 0}"
                                    data-type="${v.type || 'Option'}"
                                    data-temp-id="${counter}"
                                    data-section="${section}"
                                >
                                ${v.type || 'Option'} - <span class="text-success">$${(v.price || 0).toFixed(2)}</span>
                            </label>
                            `;
                        });
                        
                        variationsHtml += `</div>`;
                    });
                    
                    variationsHtml += `</div>`;
                }

                const html = `
                <div class="card p-3 shadow-sm mb-3 col-12" data-bogo-section="${section}" data-bogo-counter="${counter}" data-product-id="${productId}">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 border rounded p-2">
                        <div class="me-3 flex-grow-1">
                            <h5 class="mb-1">${productName}</h5>
                            ${variationsHtml}
                        </div>
                        <div class="p-2 text-nowrap">
                            <span class="product-total text-success fw-bold" style="font-size: 1.2em;">
                                $${basePrice.toFixed(2)}
                            </span>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-bogo-product-btn"
                            data-section="${section}" data-counter="${counter}" data-product-id="${productId}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                    <input type="hidden" class="bogo-product-id" value="${productId}">
                    <input type="hidden" class="bogo-product-name" value="${productName}">
                    <input type="hidden" class="bogo-product-base-price" value="${basePrice}">
                    <input type="hidden" class="bogo-product-temp-id" value="${counter}">
                </div>`;

                return html;
            }

            // ==================== NORMALIZE VARIATIONS ====================
            function normalizeVariations(variations) {
                if (!variations) return [];
                
                try {
                    if (typeof variations === 'string') {
                        variations = JSON.parse(variations);
                    }
                    
                    if (!Array.isArray(variations)) return [];
                    
                    let flat = [];
                    
                    variations.forEach(group => {
                        // Handle simple string variations (like in saved data: ["Small", "Large"])
                        if (typeof group === 'string') {
                            flat.push({
                                type: group,
                                price: 0,
                                groupName: 'Options'
                            });
                        }
                        // Handle standard object format
                        else if (group && group.values && Array.isArray(group.values)) {
                            group.values.forEach(v => {
                                if (v && typeof v === 'object') {
                                    flat.push({
                                        type: v.label || v.type || "",
                                        price: parseFloat(v.optionPrice || v.price || 0),
                                        groupName: group.name || group.groupName || ""
                                    });
                                }
                            });
                        } else if (group && (group.type || group.label)) {
                            flat.push({
                                type: group.label || group.type || "",
                                price: parseFloat(group.optionPrice || group.price || 0),
                                groupName: group.groupName || group.name || ""
                            });
                        }
                    });
                    
                    return flat;
                } catch (error) {
                    console.error('Error normalizing variations:', error);
                    return [];
                }
            }

            // ==================== UPDATE PRODUCT TOTAL & VARIATIONS ====================
            function updateProductTotal(card) {
                const basePrice = parseFloat(card.find('.product-base-price').val()) || 0;
                const productTotalElement = card.find('.product-total');
                const tempId = card.find('.product-temp-id').val();
                
                let total = basePrice;
                let selectedVariations = [];
                
                card.find('.variation-checkbox:checked').each(function() {
                    const variationPrice = parseFloat($(this).data('price')) || 0;
                    const variationType = $(this).data('type') || $(this).val();
                    total += variationPrice;
                    
                    selectedVariations.push(variationType);
                });
                
                productTotalElement.text('$' + total.toFixed(2));
                
                // Update variations in array
                const bundleType = $('#bundle_offer_type').val();
                updateProductVariationsInArray(parseInt(tempId), selectedVariations, bundleType);
                
                // Update hidden inputs
                createHiddenInputs();
                // alert(total);
                // $("#product_real_price").val("");
                $("#product_real_price").val(total);
                
                return total;
            }

            // ==================== UPDATE BOGO PRODUCT TOTAL & VARIATIONS ====================
            function updateBogoProductTotal(card) {
                const basePrice = parseFloat(card.find('.bogo-product-base-price').val()) || 0;
                const productTotalElement = card.find('.product-total');
                const tempId = card.find('.bogo-product-temp-id').val();
                const section = card.data('bogo-section');
                
                let total = basePrice;
                let selectedVariations = [];
                
                card.find('.bogo-variation-checkbox:checked').each(function() {
                    const variationPrice = parseFloat($(this).data('price')) || 0;
                    const variationType = $(this).data('type') || $(this).val();
                    total += variationPrice;
                    
                    selectedVariations.push(variationType);
                });
                
                productTotalElement.text('$' + total.toFixed(2));
                
                // Update variations in array
                const bundleType = $('#bundle_offer_type').val();
                updateProductVariationsInArray(parseInt(tempId), selectedVariations, bundleType, section);
                
                // Update hidden inputs
                createHiddenInputs();
                
                return total;
            }

            // ==================== UPDATE BUNDLE TOTAL ====================
            function updateBundleTotal() {
                let bundleTotal = 0;
                let productCount = 0;
                let breakdownHTML = '<h5>Bundle Price Breakdown:</h5><ul class="list-group">';

                $('#productDetails .card').each(function() {
                    let productName = $(this).find('.product-name').val();
                    let quantity = 1;
                    
                    let productTotal = updateProductTotal($(this));
                    productTotal = productTotal * quantity;
                    
                    bundleTotal += productTotal;
                    productCount++;

                    breakdownHTML += `
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong>${productName}</strong> (x${quantity})
                                </div>
                                <strong class="text-success ml-3">$${productTotal.toFixed(2)}</strong>
                            </div>
                        </li>`;
                });

                let discountValue = parseFloat($('#discount').val()) || 0;
                let discountType = $('#discount_type').val();
                let discountAmount = 0;

                if (discountType === 'percent') {
                    discountAmount = (bundleTotal * discountValue) / 100;
                } else {
                    discountAmount = discountValue;
                }
                let finalTotal = Math.max(bundleTotal - discountAmount, 0);
                // alert(finalTotal);

                breakdownHTML += `
                    <li class="list-group-item">
                        <strong>Subtotal: </strong><span class="text-primary">$${bundleTotal.toFixed(2)}</span>
                    </li>`;
                  $("#product_real_price").val(bundleTotal);
                if (discountAmount > 0) {
                    breakdownHTML += `
                        <li class="list-group-item text-danger">
                            <strong>Discount (${discountType === 'percent' ? discountValue + '%' : '$' + discountValue}): </strong>
                            -$${discountAmount.toFixed(2)}
                        </li>`;
                }

                breakdownHTML += `
                    <li class="list-group-item bg-success text-white">
                        <strong>Final Bundle Total: </strong>
                        <strong style="font-size: 1.3em;">$${finalTotal.toFixed(2)}</strong>
                    </li>
                </ul>`;

                if (productCount > 0) {
                    $('#priceCalculator').show();
                    $('#priceBreakdown').html(breakdownHTML);
                    $('#selectedProducts p').hide();
                } else {
                    $('#priceCalculator').hide();
                    $('#selectedProducts p').show();
                }

                $('#price').val(finalTotal.toFixed(2));
                $('#price_hidden').val(finalTotal.toFixed(2));
            }

            // ==================== UPDATE MIX & MATCH TOTAL ====================
            function updateMixMatchTotal() {
                let allProductPrices = [];
                let breakdownHTML = '<h5>Mix & Match Bundle Breakdown:</h5><ul class="list-group">';
                
                $('#productDetails .card').each(function() {
                    let productName = $(this).find('.product-name').val();
                    let quantity = 1;
                    
                    let productTotal = updateProductTotal($(this));
                    productTotal = productTotal * quantity;
                    allProductPrices.push(productTotal);

                    breakdownHTML += `
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong>${productName}</strong> (x${quantity})
                                </div>
                                <strong class="text-success ml-3">$${productTotal.toFixed(2)}</strong>
                            </div>
                        </li>`;
                });
                
                let subtotal = allProductPrices.reduce((sum, price) => sum + price, 0);
                let finalTotal = subtotal;
                let requiredQty = parseInt($('#required_qty').val()) || 0;
                let mixMatchDiscountValue = parseFloat($('#discount').val()) || 0;
                let discountAmount = 0;
                
                if (requiredQty > 0 && allProductPrices.length >= requiredQty) {
                    allProductPrices.sort((a, b) => a - b);
                    
                    let eligibleDiscounts = Math.floor(allProductPrices.length / requiredQty);
                    
                    for (let i = 0; i < eligibleDiscounts; i++) {
                        discountAmount += (allProductPrices[i] * mixMatchDiscountValue) / 100;
                    }
                    
                    finalTotal = subtotal - discountAmount;
                }
                
                breakdownHTML += `
                    <li class="list-group-item">
                        <strong>Subtotal: </strong><span class="text-primary">$${subtotal.toFixed(2)}</span>
                    </li>`;

                if (discountAmount > 0) {
                    breakdownHTML += `
                        <li class="list-group-item text-danger">
                            <strong>Mix & Match Discount (Buy ${requiredQty}, ${mixMatchDiscountValue}% off on cheapest): </strong>
                            -$${discountAmount.toFixed(2)}
                        </li>`;
                }

                breakdownHTML += `
                    <li class="list-group-item bg-success text-white">
                        <strong>Final Bundle Total: </strong>
                        <strong style="font-size: 1.3em;">$${finalTotal.toFixed(2)}</strong>
                    </li>
                </ul>`;
                
                $('#priceBreakdown').html(breakdownHTML);
                $('#price').val(finalTotal.toFixed(2));
                $('#price_hidden').val(finalTotal.toFixed(2));
            }

            // ==================== UPDATE BOGO TOTAL ====================
            function updateBogoTotal() {
                let allProductPrices = [];
                let breakdownHTML = '<h5>BOGO Bundle Breakdown:</h5><ul class="list-group">';

                // Section A products
                $('#productDetails_section_a .card').each(function() {
                    let productName = $(this).find('.bogo-product-name').val();
                    let quantity = 1;
                    let productTotal = updateBogoProductTotal($(this));
                    productTotal = productTotal * quantity;
                    allProductPrices.push({total: productTotal, name: productName, section: 'A'});

                    breakdownHTML += `
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong>Section A: ${productName}</strong> (x${quantity})
                                </div>
                                <strong class="text-success ml-3">$${productTotal.toFixed(2)}</strong>
                            </div>
                        </li>`;
                });

                // Section B products
                $('#productDetails_section_b .card').each(function() {
                    let productName = $(this).find('.bogo-product-name').val();
                    let quantity = 1;
                    let productTotal = updateBogoProductTotal($(this));
                    productTotal = productTotal * quantity;
                    allProductPrices.push({total: productTotal, name: productName, section: 'B'});

                    breakdownHTML += `
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong>Section B: ${productName}</strong> (x${quantity})
                                </div>
                                <strong class="text-success ml-3">$${productTotal.toFixed(2)}</strong>
                            </div>
                        </li>`;
                });

                // Calculate BOGO discount
                let subtotal = allProductPrices.reduce((sum, item) => sum + item.total, 0);
                let finalTotal = subtotal;
                let discountAmount = 0;

                // BOGO logic: For every pair (A + B), one is free
                // Pair the products based on their order
                for (let i = 0; i < Math.min(bogoSelectedProductsA.length, bogoSelectedProductsB.length); i++) {
                    let priceA = allProductPrices.find(p => p.section === 'A' && p.name === bogoSelectedProductsA[i]?.name)?.total || 0;
                    let priceB = allProductPrices.find(p => p.section === 'B' && p.name === bogoSelectedProductsB[i]?.name)?.total || 0;
                    
                    // Add the cheaper one to discount (free item)
                    discountAmount += Math.min(priceA, priceB);
                }

                finalTotal = subtotal - discountAmount;

                breakdownHTML += `
                    <li class="list-group-item">
                        <strong>Subtotal: </strong><span class="text-primary">$${subtotal.toFixed(2)}</span>
                    </li>`;
                  $("#product_real_price").val(subtotal);
                if (discountAmount > 0) {
                    breakdownHTML += `
                        <li class="list-group-item text-success">
                            <strong>BOGO Discount (Buy 1 Get 1 Free): </strong>
                            -$${discountAmount.toFixed(2)}
                        </li>`;
                }

                breakdownHTML += `
                    <li class="list-group-item bg-success text-white">
                        <strong>Final Bundle Total: </strong>
                        <strong style="font-size: 1.3em;">$${finalTotal.toFixed(2)}</strong>
                    </li>
                </ul>`;

                let hasProducts = $('#productDetails_section_a .card').length > 0 || $('#productDetails_section_b .card').length > 0;

                if (hasProducts) {
                    $('#priceCalculator').show();
                    $('#priceBreakdown').html(breakdownHTML);
                    $('#price').val(finalTotal.toFixed(2));
                    $('#price_hidden').val(finalTotal.toFixed(2));
                } else {
                    $('#priceCalculator').hide();
                    $('#price').val('0.00');
                    $('#price_hidden').val('0.00');
                }
            }

            // ==================== EVENT HANDLERS ====================
            
            $(document).on('change', '.variation-checkbox', function() {
                updateProductTotal($(this).closest('.card'));
                let bundleType = $('#bundle_offer_type').val();
                if (bundleType === 'mix_match') {
                    updateMixMatchTotal();
                } else if (bundleType === 'simple' || bundleType === 'bundle') {
                    updateBundleTotal();
                }
            });
            
            $(document).on('change', '.bogo-variation-checkbox', function() {
                updateBogoProductTotal($(this).closest('.card'));
                updateBogoTotal();
            });
            
            // Remove product button
            $(document).on('click', '.remove-product-btn', function() {
                const productId = $(this).data('product-id');
                const tempId = $(this).data('temp-id');
                const bundleType = $('#bundle_offer_type').val();
                
                // Remove from array
                selectedProductsArray = selectedProductsArray.filter(p => p.temp_id !== tempId);
                createHiddenInputs();
                
                $(this).closest('.card').fadeOut(300, function() {
                    $(this).remove();
                    
                    if (bundleType === 'mix_match') {
                        updateMixMatchTotal();
                    } else if (bundleType === 'simple' || bundleType === 'bundle') {
                        updateBundleTotal();
                    }
                    
                    if ($('#productDetails .card').length === 0) {
                        $('#selectedProducts p').show();
                    }
                });
            });
            
            // Remove BOGO product button
            $(document).on('click', '.remove-bogo-product-btn', function() {
                const productId = $(this).data('product-id');
                const section = $(this).data('section');
                const counter = $(this).data('counter');
                const bundleType = $('#bundle_offer_type').val();
                
                // Remove from array
                if (section === 'a') {
                    bogoSelectedProductsA = bogoSelectedProductsA.filter(p => p.temp_id !== counter);
                } else if (section === 'b') {
                    bogoSelectedProductsB = bogoSelectedProductsB.filter(p => p.temp_id !== counter);
                }
                
                createHiddenInputs();
                
                $(this).closest('.card').fadeOut(300, function() {
                    $(this).remove();
                    updateBogoTotal();
                    
                    if ($('#productDetails_section_a .card').length === 0 && $('#productDetails_section_b .card').length === 0) {
                        $('#selectedProducts p').show();
                    }
                });
            });

            // Before form submission
            $(document).on('submit', 'form', function(e) {
                let bundleType = $('#bundle_offer_type').val();
                
                // Make sure hidden inputs are created
                createHiddenInputs();
                
                // Validation
                if (bundleType === 'simple' && selectedProductsArray.length !== 1) {
                    e.preventDefault();
                    alert('Simple bundle must have exactly 1 product.');
                    return false;
                }
                
                if (bundleType === 'bogo_free') {
                    if (bogoSelectedProductsA.length === 0 || bogoSelectedProductsB.length === 0) {
                        e.preventDefault();
                        alert('BOGO bundle must have at least one product in both Section A and Section B.');
                        return false;
                    }
                }
                
                return true;
            });

            // Bundle type change
            $('#bundle_offer_type').on('change', function() {
                let bundleType = $(this).val();
                updateFieldsVisibility(bundleType);

                $('#availableProducts').hide();
                $('#availableProducts_get_x_buy_y').hide();

                // Clear everything
                $('#productDetails').empty();
                selectedProductsArray = [];
                productCounter = 0;

                $('#productDetails_section_a').empty();
                $('#productDetails_section_b').empty();
                bogoSelectedProductsA = [];
                bogoSelectedProductsB = [];
                bogoCounterA = 0;
                bogoCounterB = 0;

                $('.hidden-product-input, .hidden-bogo-a-input, .hidden-bogo-b-input').remove();

                $('#priceCalculator').hide();
                $('#price').val('0.00');
                $('#price_hidden').val('0.00');
                $('#discount').val('0');
                $('#selectedProducts p').show();
            });

        });

        // ==================== UPDATE FIELDS VISIBILITY ====================
        function updateFieldsVisibility(bundleType) {
            $('#price_input_hide, #discount_input_hide, #required_qty, #discount_value_input_hide, #actual_price_input_hide, #Bundle_products_configuration, #availableProducts, #availableProducts_get_x_buy_y')
                .addClass('d-none');
            
            // Hide all panels first
            $('.bogo_free_div, .bundle_div, .mix_match_div').hide();
            
            if (bundleType === 'mix_match') {
                $('#discount_input_hide, #required_qty, #discount_value_input_hide, #Bundle_products_configuration, #availableProducts').removeClass('d-none');
                $('.mix_match_div').show();
            } else if (bundleType === 'bogo_free') {
                $('#Bundle_products_configuration, #availableProducts_get_x_buy_y').removeClass('d-none');
                $('.bogo_free_div').show();
            } else if (bundleType === 'simple') {
                $('#price_input_hide, #discount_input_hide, #discount_value_input_hide, #Bundle_products_configuration, #availableProducts').removeClass('d-none');
            } else if (bundleType === 'bundle') {
                $('#price_input_hide, #discount_input_hide, #discount_value_input_hide, #Bundle_products_configuration, #availableProducts').removeClass('d-none');
                $('.bundle_div').show();
            } else if (bundleType === 'simple x') {
                $('#price_input_hide, #discount_input_hide, #discount_value_input_hide, #actual_price_input_hide').removeClass('d-none');
                $('#Bundle_products_configuration').addClass('d-none');
            } else {
                $('#price_input_hide, #discount_input_hide, #discount_value_input_hide, #Bundle_products_configuration, #availableProducts').removeClass('d-none');
            }
        }
   </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const managementSelection = document.querySelectorAll('#management_selection');
            const voucherCards = document.querySelectorAll('.voucher-card');
            const voucherCards2 = document.querySelectorAll('.voucher-card_2');
             const allimages = document.getElementById('allimages');
             const hidden_voucher_id = document.getElementById('hidden_voucher_id');
            // Move these functions OUTSIDE of DOMContentLoaded to make them globally accessible
            function section_one(loopIndex, primaryId,name) {


                 if (loopIndex === "3" || name === "Flat discount") {
                    window.location.href = "{{ url('admin/Voucher/add-flat-discount') }}";
                }
                 else if (loopIndex === "4" || name === "Gift") {
                    window.location.href = "{{ url('admin/Voucher/add-gift') }}";
                }
                // alert(primaryId);
                getDataFromServer(primaryId);
                  get_product();
                // Set hidden input value
                document.getElementById('hidden_value').value = loopIndex;
                document.getElementById('hidden_name').value = name;

                const managementSelection = document.querySelectorAll('#management_selection');

                     // Get all elements
                const basic_info_main = document.getElementById('basic_info_main');
                const store_category_main = document.getElementById('store_category_main');
                const how_it_work_main = document.getElementById('how_it_work_main');
                const term_condition_main = document.getElementById('term_condition_main');
                const review_submit_main = document.getElementById('review_submit_main');
                const allimages = document.getElementById('allimages');

                const bundle_rule = document.getElementById('bundle_rule');
                const Bundle_products_configuration = document.getElementById('Bundle_products_configuration');

                const Product_voucher_fields_1_3 = document.getElementById('Product_voucher_fields_1_3');
                const product_voucher_price_info_1_3 = document.getElementById('product_voucher_price_info_1_3');

                const food_voucher_fields_1_4 = document.getElementById('food_voucher_fields_1_4');
                const food_voucher_price_info_1_4 = document.getElementById('food_voucher_price_info_1_4');

                const bundel_food_voucher_fields_1_3_1_4 = document.getElementById('bundel_food_voucher_fields_1_3_1_4');
                const bundel_food_voucher_price_info_1_3_1_4 = document.getElementById('bundel_food_voucher_price_info_1_3_1_4');

                managementSelection.forEach(el => {
                    if (loopIndex === "1" || name === "Delivery/Pickup") {
                        submit_voucher_type(loopIndex, primaryId,name);
                        el.classList.remove('d-none');

                              showElements([basic_info_main, store_category_main, how_it_work_main, term_condition_main, review_submit_main,Product_voucher_fields_1_3,product_voucher_price_info_1_3,allimages]);
                            hideElements([bundel_food_voucher_fields_1_3_1_4, bundel_food_voucher_price_info_1_3_1_4, food_voucher_fields_1_4, food_voucher_price_info_1_4]);
                        // Hide discount-specific sections
                        const elementsToHide = [
                            document.getElementById('basic_info'),
                            document.getElementById('store_category'),
                            document.getElementById('price_info'),
                            document.getElementById('voucher_behavior'),
                            document.getElementById('usage_terms'),
                            document.getElementById('attributes'),
                            document.getElementById('tags'),
                            document.getElementById('allimages')
                        ];

                        elementsToHide.forEach(element => {
                            if (element) element.classList.add('d-none');
                        });

                    } else if (loopIndex === "2" || name === "In-Store") {

                        submit_voucher_type(loopIndex, primaryId,name);
                        el.classList.remove('d-none');

                        // Show discount-specific sections
                        const elementsToShow = [
                            document.getElementById('basic_info'),
                            document.getElementById('store_category'),
                            document.getElementById('price_info'),
                            document.getElementById('voucher_behavior'),
                            document.getElementById('usage_terms'),
                            document.getElementById('attributes'),
                            document.getElementById('tags'),

                        ];

                        elementsToShow.forEach(element => {
                            if (element) element.classList.remove('d-none');
                        });
                    }
                });
            }

            // DOMContentLoaded event listener for initialization
            document.addEventListener("DOMContentLoaded", function () {
                const managementSelection = document.querySelectorAll('#management_selection');
                const voucherCards = document.querySelectorAll('.voucher-card');
                const voucherCards2 = document.querySelectorAll('.voucher-card_2');

                // Highlight selected voucher-card
                voucherCards.forEach(card => {
                    card.addEventListener('click', function () {
                        voucherCards.forEach(c => c.classList.remove('selected'));
                        this.classList.add('selected');
                    });
                });

                // Event delegation for dynamically created voucher-card_2 elements
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.voucher-card_2')) {
                        document.querySelectorAll('.voucher-card_2').forEach(card => {
                            card.classList.remove('selected');
                        });
                        e.target.closest('.voucher-card_2').classList.add('selected');
                    }
                });
            });
                 // Highlight selected voucher-card
            voucherCards.forEach(card => {
                card.addEventListener('click', function () {
                    voucherCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
            // Make functions globally accessible
            window.section_one = section_one;
            window.section_second = section_second;
        });
    </script>

    <script>
        $(document).ready(function() {
            getDataFromServer(7);
        });

        function getDataFromServer(voucher_id) {
            // Get saved howto_work value for pre-selection
             <?php
                $rawHowtoWork = $product->how_and_condition_ids ?? '[]';
                $decoded = json_decode($rawHowtoWork, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $savedId = $decoded[0];
                } elseif (is_scalar($decoded)) {
                    $savedId = $decoded;
                } else {
                    $savedId = '';
                }
            ?>
            let savedHowtoWork = '{{ $savedId }}';
            console.log('Saved How To Work ID:', savedHowtoWork);
            
            // Get saved term_and_condition_ids for pre-selection
            <?php
                $rawTermCondition = $product->term_and_condition_ids ?? '[]';
                $decodedTerms = json_decode($rawTermCondition, true);
                $savedTermIds = is_array($decodedTerms) ? $decodedTerms : [];
            ?>
            let savedTermIds = @json($savedTermIds);
            console.log('Saved Term & Condition IDs:', savedTermIds);
            
            $.ajax({
                url: "{{ route('admin.Voucher.get_document') }}",
                type: "GET",
                data: { voucher_id: voucher_id },
                dataType: "json",
                success: function(response) {
                    let workHtml = "";

                   $.each(response.work_management, function(index, item) {

                    // sections already array — no JSON.parse needed
                    let sections = Array.isArray(item.sections) ? item.sections : [];

                    let sectionsHtml = '';
                    $.each(sections, function(sIndex, section) {
                        let stepsHtml = '';
                        $.each(section.steps, function(stepIndex, step) {
                            stepsHtml += `
                                <li class="mb-2">
                                    <i class="fas fa-circle text-muted" style="font-size: 6px; vertical-align: middle;"></i>
                                    <span class="ms-2 text-muted">${step}</span>
                                </li>
                            `;
                        });

                        sectionsHtml += `
                            <div class="mb-3">
                                <h6 class="fw-semibold text-dark mb-2">${section.title}</h6>
                                <ul class="list-unstyled ms-3">
                                    ${stepsHtml}
                                </ul>
                            </div>
                        `;
                    });

                    // Check if this radio button should be pre-selected
                    let isChecked = savedHowtoWork && savedHowtoWork == item.id ? 'checked' : '';
                    
                    workHtml += `
                        <div class="card mb-3 work-item shadow-sm">
                            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 cursor-pointer"
                                onclick="toggleAccordion(${item.id})">
                                
                                <div class="d-flex align-items-center flex-grow-1">
                                    <input type="checkbox" name="howto_work[]" value="${item.id}"
                                        class="form-check-input record-checkbox me-3"
                                        id="record_${item.id}"
                                        data-item-id="${item.id}"
                                        ${isChecked}>
                                    
                                    <label for="record_${item.id}" class="fw-semibold mb-0 flex-grow-1">
                                        ${item.guide_title}
                                    </label>
                                </div>

                                <i class="fas fa-chevron-down text-muted accordion-icon"
                                    id="icon_${item.id}" style="transition: transform 0.3s ease;">
                                </i>
                            </div>

                            <div id="content_${item.id}" class="accordion-content collapse">
                                <div class="card-body bg-light border-top">
                                    ${sectionsHtml || '<p class="text-muted fst-italic mb-0">No sections available</p>'}
                                </div>
                            </div>
                        </div>
                    `;
                });


                    $("#workList").html(workHtml);
                    let usageHtml = "";
                $.each(response.usage_term_management, function (index, term) {
                    // Check if this term is in saved IDs (using == for type coercion)
                    let isTermChecked = savedTermIds.some(id => id == term.id) ? 'checked' : '';
                    
                    usageHtml += `
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border shadow-sm hover-shadow-lg transition-all">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start">
                                        <input
                                            class="form-check-input mt-1 flex-shrink-0"
                                            style="width: 15px; height: 15px; cursor: pointer;"
                                            name="term_and_condition[]"
                                            type="checkbox"
                                            value="${term.id}"
                                            id="term${term.id}"
                                            ${isTermChecked}>
                                        
                                        <label for="term${term.id}" class="form-check-label fw-semibold mb-0 cursor-pointer flex-grow-1 ms-3 mt-1 ml-2" style="cursor: pointer; line-height: 1.5;">
                                            ${term.baseinfor_condition_title}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $("#usageTerms").html(usageHtml);


                },
                error: function(xhr, status, error) {
                console.error("Error:", error);
                alert("Something went wrong!");
                }
            });
        }

        // Toggle accordion function using Bootstrap 5
        function toggleAccordion(id) {
            const content = $(`#content_${id}`);
            const icon = $(`#icon_${id}`);

            // Toggle Bootstrap collapse
            content.collapse('toggle');

            // Rotate icon
            if (icon.hasClass('rotated')) {
                icon.removeClass('rotated').css('transform', 'rotate(0deg)');
            } else {
                icon.addClass('rotated').css('transform', 'rotate(180deg)');
            }
        }

        // Optional: Close all accordions
        function closeAllAccordions() {
            $('.accordion-content').collapse('hide');
            $('.accordion-icon').removeClass('rotated').css('transform', 'rotate(0deg)');
        }

        // Optional: Open all accordions
        function openAllAccordions() {
            $('.accordion-content').collapse('show');
            $('.accordion-icon').addClass('rotated').css('transform', 'rotate(180deg)');
        }



        function bundle(type) {
            // 1. Set the hidden input value
            document.getElementById('hidden_bundel').value = type;

            // 2. IDs of elements to hide
            const ids = [
                'management_selection',
                'basic_info_main',
                'store_category_main',
                'how_it_work_main',
                'term_condition_main',
                'review_submit_main',
                'Product_voucher_fields_1_3',
                'product_voucher_price_info_1_3',
                'food_voucher_fields_1_4',
                'food_voucher_price_info_1_4',
                'bundel_food_voucher_fields_1_3_1_4',
                'bundel_food_voucher_price_info_1_3_1_4',
                'Bundle_products_configuration',
                'allimages'
            ];

            // Add d-none to each element if it's visible
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('d-none')) {
                el.classList.add('d-none');
                }
            });

            // 3. Remove "selected" from ALL voucher-card_2 sections
            document.querySelectorAll('.voucher-card').forEach(card => {
                card.classList.remove('selected');
            });
        }
        // -------------------- Client Change => Load Segments --------------------
        $(document).ready(function () {
            $('.Clients_select_new').on('change', function () {
                let clientId = $(this).val();
                if (!clientId) return;
                // alert(clientId);
                let url = "{{ route('admin.client-side.getSegments', ':id') }}".replace(':id', clientId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (res) {
                        // Clear and refill segment dropdown
                        $('#segment_type').empty().append('<option value="">Select Segment</option>');
                        // Agar res ek array hai to loop karo
                        if (Array.isArray(res) && res.length > 0) {
                            $.each(res, function (index, item) {
                                $('#segment_type').append(
                                    '<option value="' + item.id + '">' + item.name + ' / ' + item.type + '</option>'
                                );
                            });
                        } else {
                            $('#segment_type').append('<option value="">No segments found</option>');
                        }

                        // Refresh Select2
                        $('#segment_type').trigger('change');
                    },
                    error: function () {
                        // alert("Error loading segments!");
                    }
                });

            });
        });

        function submit_voucher_type(loopIndex,id,name) {
            var loopIndex = loopIndex;
            var primary_vouchertype_id = id;

            $.ajax({
                url: "{{ route('admin.Voucher.voucherType.store') }}", // <-- اپنے route کے حساب سے بدلیں
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}", // Laravel CSRF protection کیلئے ضروری
                    voucher_type_id: primary_vouchertype_id,
                    loopIndex: loopIndex
                },
                success: function(response) {
                    console.log("Success:", response);
                    // empty previous content
                    $("#append_all_data").empty();
                    // starting index (4 se start karna hai)
                    let index = 5;
                    // loop through modules
                    response.all_ids.forEach(function(module) {
                        let card = `
                            <div class="col-md-3">
                                <div class="voucher-card_2 border rounded py-4 text-center h-70" onclick="section_second(${index}, ${module.id}, '${module.module_name}')">
                                        <div class="display-4 mb-2">
                                        <img src="${module.thumbnail}" alt="${module.module_name}" style="width:40px; height:auto;" />
                                    </div>

                                    <h6 class="fw-semibold">${module.module_name}</h6>
                                    <small class="text-muted">${module.description ?? ''}</small>
                                </div>
                            </div>

                        `;
                        $("#append_all_data").append(card);

                        index++; // next card ke liye +1
                    });
                },

                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    alert("Something went wrong!");
                }
            });
        }

        $(document).on('click', '.voucher-card_2', function () {
            $('.voucher-card_2').removeClass('selected');
            $(this).addClass('selected');
        });

        function get_product() {
            var category_id = $("#category_id").val();
            var store_id = $("#store_id").val();
            // var _product_name = _product_name;

            if (store_id == "") {
                alert("Please select store");
            } else {
                $.ajax({
                    url: "{{ route('admin.Voucher.get_product') }}",
                    type: "GET",
                    data: {
                        store_id: store_id,
                        category_id: category_id , // optional agar zaroori ho
                        // product_name: _product_name  // optional agar zaroori ho
                    },
                    success: function(response) {
                        console.log(response);
                        $('.all_product_list')
                            .empty()
                            .append('<option value="">{{ translate("Select Product") }}</option>');

                        $.each(response, function(key, product) {
                            $('.all_product_list')
                                .append('<option value="'+ product.id +'">'
                                + product.name + '</option>');
                        });
                    },
                    error: function() {
                        toastr.error("{{ translate('messages.failed_to_load_branches') }}");
                    }
                });
            }
        }

    </script>

    <script>
        "use strict";
        $(document).on('change', '#discount_type', function () {
         let data =  document.getElementById("discount_type");
         if(data.value === 'amount'){
             $('#symble').text("({{ \App\CentralLogics\Helpers::currency_symbol() }})");
            }
            else{
             $('#symble').text("(%)");
         }
         });
        $(document).ready(function() {
            $("#add_new_option_button").click(function(e) {
                $('#empty-variation').hide();
                count++;
                let add_option_view = `
                    <div class="__bg-F8F9FC-card view_new_option mb-2">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <label class="form-check form--check">
                                    <input id="options[` + count + `][required]" name="options[` + count + `][required]" class="form-check-input" type="checkbox">
                                    <span class="form-check-label">{{ translate('Required') }}</span>
                                </label>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm delete_input_button"
                                        title="{{ translate('Delete') }}">
                                        <i class="tio-add-to-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-xl-4 col-lg-6">
                                    <label for="">{{ translate('name') }}</label>
                                    <input required name=options[` + count +
                    `][name] class="form-control new_option_name" type="text" data-count="`+
                      count +`">
                                </div>

                                <div class="col-xl-4 col-lg-6">
                                    <div>
                                        <label class="input-label text-capitalize d-flex align-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                                        </label>
                                        <div class="resturant-type-group px-0">
                                            <label class="form-check form--check mr-2 mr-md-4">
                                                <input class="form-check-input show_min_max" data-count="`+count+`" type="checkbox" value="multi"
                                                name="options[` + count + `][type]" id="type` + count +
                    `" checked
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Multiple Selection') }}
                    </span>
                </label>

                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input hide_min_max" data-count="`+count+`" type="checkbox" value="single"
                    name="options[` + count + `][type]" id="type` + count +
                    `"
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Single Selection') }}
                    </span>
                </label>
            </div>
        </div>
        </div>
        <div class="col-xl-4 col-lg-6">
        <div class="row g-2">
            <div class="col-6">
                <label for="">{{ translate('Min') }}</label>
                                            <input id="min_max1_` + count + `" required  name="options[` + count + `][min]" class="form-control" type="number" min="1">
                                        </div>
                                        <div class="col-6">
                                            <label for="">{{ translate('Max') }}</label>
                                            <input id="min_max2_` + count + `"   required name="options[` + count + `][max]" class="form-control" type="number" min="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="option_price_` + count + `" >
                                <div class="bg-white border rounded p-3 pb-0 mt-3">
                                    <div  id="option_price_view_` + count + `">
                                        <div class="row g-3 add_new_view_row_class mb-3">
                                            <div class="col-md-4 col-sm-6">
                                                <label for="">{{ translate('Option_name') }}</label>
                                                <input class="form-control" required type="text" name="options[` +
                    count +
                    `][values][0][label]" id="">
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="">{{ translate('Additional_price') }}</label>
                                                <input class="form-control" required type="number" min="0" step="0.01" name="options[` +
                    count + `][values][0][optionPrice]" id="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count +
                    `">
                                        <button type="button" class="btn btn--primary btn-outline-primary add_new_row_button" data-count="`+
                    count +`">{{ translate('Add_New_Option') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                $("#add_new_option").append(add_option_view);
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            $('.js-select2-sub_category').each(function() {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            $('.js-select2-category').each(function() {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        function add_new_row_button(data) {
            count = data;
            countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
            let add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Option_name') }}</label>
                        <input class="form-control" required type="text" name="options[` + count + `][values][` +
                countRow + `][label]" id="">
                    </div>
                    <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Additional_price') }}</label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` +
                count +
                `][values][` + countRow + `][optionPrice]" id="">
                    </div>
                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-sm-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm deleteRow"
                                title="{{ translate('Delete') }}">
                                <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                </div>
            </div>`;
            $('#option_price_view_' + data).append(add_new_row_view);

        }
        $('#store_id').on('change', function () {
            let route = '{{url('/')}}/admin/store/get-addons?data[]=0&store_id='+$(this).val();
            let id = 'add_on';
            getRestaurantData(route, id);
        });
        function modulChange(id) {
            $.get({
                url: "{{url('/')}}/admin/business-settings/module/show/"+id,
                dataType: 'json',
                success: function(data) {
                    module_data = data.data;
                    console.log(module_data)
                    stock = module_data.stock;
                    module_type = data.type;
                    if (stock) {
                        $('#stock_input').show();
                    } else {
                        $('#stock_input').hide();
                    }
                    if (module_data.add_on) {
                        $('#addon_input').show();
                    } else {
                        $('#addon_input').hide();
                    }

                    if (module_data.item_available_time) {
                        $('#time_input').show();
                    } else {
                        $('#time_input').hide();
                    }

                    if (module_data.veg_non_veg) {
                        $('#veg_input').show();
                    } else {
                        $('#veg_input').hide();
                    }
                    if (module_data.unit) {
                        $('#unit_input').show();
                    } else {
                        $('#unit_input').hide();
                    }
                    if (module_data.common_condition) {
                        $('#condition_input').show();
                    } else {
                        $('#condition_input').hide();
                    }
                    if (module_data.brand) {
                        $('#brand_input').show();
                    } else {
                        $('#brand_input').hide();
                    }
                    combination_update();
                    if (module_type == 'food') {
                        $('#food_variation_section').show();
                        $('#attribute_section').hide();
                    } else {
                        $('#food_variation_section').hide();
                        $('#attribute_section').show();
                    }
                    if (module_data.organic) {
                        $('#organic').show();
                    } else {
                        $('#organic').hide();
                    }
                    if (module_data.basic) {
                        $('#basic').show();
                    } else {
                        $('#basic').hide();
                    }
                    if (module_data.nutrition) {
                        $('#nutrition').show();
                    } else {
                        $('#nutrition').hide();
                    }
                    if (module_data.allergy) {
                        $('#allergy').show();
                    } else {
                        $('#allergy').hide();
                    }
                },
            });
            module_id = id;
        }

        modulChange({{Config::get('module.current_module_id')}});

        $('#condition_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/common-condition/get-all',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#brand_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/brand/get-all',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#store_id').select2({
            ajax: {
                url: '{{ url('/') }}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id:{{Config::get('module.current_module_id')}},
                    };
                },
                
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#category_id').select2({
           
            ajax: {
                url: '{{ url('/') }}/admin/item/get-categories?parent_id=0',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id:{{Config::get('module.current_module_id')}},
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#sub-categories').select2({
            ajax: {
                url: '{{ url('/') }}/admin/item/get-categories',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        module_id:{{Config::get('module.current_module_id')}},
                        parent_id: parent_category_id,
                        sub_category: true
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#choice_attributes').on('change', function() {
            if (module_id == 0) {
                toastr.error('{{ translate('messages.select_a_module') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                $(this).val("");
                return false;
            }
            $('#customer_choice_options').html(null);
            $('#variant_combination').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                if ($(this).val().length > 50) {
                    toastr.error(
                        '{{ translate('validation.max.string', ['attribute' => translate('messages.variation'), 'max' => '50']) }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    return false;
                }
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name;

            $('#customer_choice_options').append(
                `<div class="__choos-item"><div><input type="hidden" name="choice_no[]" value="${i}"><input type="text" class="form-control d-none" name="choice[]" value="${n}" placeholder="{{ translate('messages.choice_title') }}" readonly> <label class="form-label">${n}</label> </div><div><input type="text" class="form-control combination_update" name="choice_options_${i}[]" placeholder="{{ translate('messages.enter_choice_values') }}" data-role="tagsinput"></div></div>`
            );
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('admin.Voucher.variant-combination') }}",
                data: $('#item_form').serialize() + '&stock=' + stock,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    console.log(data);
                    $('#loading').hide();
                    $('#variant_combination').html(data.view);
                    if (data.length < 1) {
                        $('input[name="current_stock"]').attr("readonly", false);
                    }
                }
            });
        }


        $(document).ready(function() {
            $('#item_form').on('submit', function(e) {
                $('#submitButton').attr('disabled', true);
                e.preventDefault();

                let formData = new FormData(this);
                $.ajax({
                    url: '{{ route('admin.Voucher.update', [$product['id']]) }}',
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(data) {
                        $('#loading').hide();
                        $('#submitButton').attr('disabled', false);

                        if (data.errors && Array.isArray(data.errors)) {
                            data.errors.forEach(function(err) {
                                toastr.error(err.message, { CloseButton: true, ProgressBar: true });
                            });
                            return;
                        }

                        toastr.success("{{ translate('messages.voucher_updated_successfully') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(() => location.href = "{{ route('admin.Voucher.list') }}", 1000);
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        $('#submitButton').attr('disabled', false);

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, err) {
                                toastr.error(err.message);
                            });
                        } else {
                            toastr.error("Something went wrong");
                        }
                    }
                });
            });
        });


    <script>
       let existingImageUrls = @json($imageUrls);
    
       $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'item_images[]',
                maxCount: 5,
                rowHeight: '176px !important',
                groupClassName: 'spartan_item_wrapper min-w-176px max-w-176px',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/upload-img.png') }}",
                    width: '176px'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        "{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
            
            // Load existing images after picker is initialized
            if (existingImageUrls && existingImageUrls.length > 0) {
                existingImageUrls.forEach(function(imageUrl, index) {
                    let imgHtml = `
                        <div class="spartan_item_wrapper min-w-176px max-w-176px existing-img-item" data-index="${index}">
                            <div class="spartan_remove_row"><i class="tio-delete"></i></div>
                            <img src="${imageUrl}" style="width: 176px; height: 176px; object-fit: cover; border-radius: 8px;">
                            <input type="hidden" name="existing_item_images[]" value="${imageUrl.split('/').pop()}">
                        </div>
                    `;
                    $('#coba').append(imgHtml);
                });
                
                // Add click handler for removing existing images
                $(document).on('click', '.existing-img-item .spartan_remove_row', function(e) {
                    e.preventDefault();
                    let imageName = $(this).siblings('input[name="existing_item_images[]"]').val();
                    
                    // Track removed image
                    let currentRemoved = $('#removedItemImageKeys').val();
                    $('#removedItemImageKeys').val(currentRemoved ? currentRemoved + ',' + imageName : imageName);
                    
                    // Remove the preview
                    $(this).closest('.existing-img-item').fadeOut(300, function() {
                        $(this).remove();
                    });
                });
            }
        });
        
        // Add hidden input for tracking removed existing images (if not exists)
        if ($('#removedItemImageKeys').length === 0) {
            $('form').append('<input type="hidden" name="removedItemImageKeys" id="removedItemImageKeys" value="">');
        }

        $('#reset_btn').click(function() {
            $('#module_id').val(null).trigger('change');
            $('#store_id').val(null).trigger('change');
            $('#category_id').val(null).trigger('change');
            $('#sub-categories').val(null).trigger('change');
            $('#unit').val(null).trigger('change');
            $('#veg').val(0).trigger('change');
            $('#add_on').val(null).trigger('change');
            $('#discount_type').val(null).trigger('change');
            $('#choice_attributes').val(null).trigger('change');
            $('#customer_choice_options').empty().trigger('change');
            $('#variant_combination').empty().trigger('change');
            $('#viewer').attr('src', "{{ asset('public/assets/admin/img/upload.png') }}");
            $('#customFileEg1').val(null).trigger('change');
            $("#coba").empty().spartanMultiImagePicker({
                fieldName: 'item_images[]',
                maxCount: 6,
                rowHeight: '176px !important',
                groupClassName: 'spartan_item_wrapper min-w-176px max-w-176px',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/upload-img.png') }}",
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                        "{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function(index, file) {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        })

        function findBranch(storeId) {
            if (!storeId) {
                $('#sub-branch').empty().append('<option value="">{{ translate("messages.select_branch") }}</option>');
                $('#categories').empty().append('<option value="">{{ translate("messages.select_category") }}</option>');
                return;
            }

            
         $.ajax({
                url: "{{ route('admin.Voucher.get_branches') }}",
                type: "GET",
                data: { store_id: storeId },
                success: function(response) {

                    // 🟩 BRANCHES
                    $('#sub-branch').empty().append('<option value="">{{ translate("messages.select_branch") }}</option>');
                    $.each(response.branches, function(key, branch) {
                        $('#sub-branch').append('<option value="'+ branch.id +'"> ' + branch.name + ' ('+ branch.type +')</option>');
                    });



                    // 🟩 CATEGORIES
                    $('#categories').empty().append('<option value="">{{ translate("messages.select_category") }}</option>');
                    if (response.categories && response.categories) {
                        $.each(response.categories, function(key, category) {
                            $('#categories').append('<option value="'+ category.id +'">' + category.name + '</option>');
                        });
                    } else {
                        console.warn("⚠️ No categories found in response");
                    }
                },
                error: function() {
                    toastr.error("{{ translate('messages.failed_to_load_branches') }}");
                }
            });


        }

    </script>

@endpush

<script>
    let removedImageKeys = [];
    $(document).on('click', '.function_remove_img', function() {
        let key = $(this).data('key');
        let photo = $(this).data('photo');
        function_remove_img(key, photo);
    });

    function function_remove_img(key, photo) {
        $('#product_images_' + key).addClass('d-none');
        removedImageKeys.push(photo);
        $('#removedImageKeysInput').val(removedImageKeys.join(','));
    }
</script>
