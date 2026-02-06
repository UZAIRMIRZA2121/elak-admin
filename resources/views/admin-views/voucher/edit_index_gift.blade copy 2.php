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

/* Custom toggle style */
.form-switch .form-check-input {
  width: 2.5em;
  height: 1.3em;
  background-color: #ccc;
  border-radius: 1em;
  position: relative;
  appearance: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.form-switch .form-check-input::before {
  content: "";
  position: absolute;
  top: 2px;
  left: 3px;
  width: 1em;
  height: 1em;
  background: #fff;
  border-radius: 50%;
  transition: transform 0.3s ease;
}

.form-switch .form-check-input:checked {
  background-color: #0d6efd;
}

.form-switch .form-check-input:checked::before {
  transform: translateX(1.2em);
}

.template-card {
  cursor: pointer;
  transition: all 0.25s ease-in-out;
  background-color: #fff;
}

.template-card:hover {
  border-color: #0d6efd;
  background-color: #f8f9fa;
}

.template-card.selected {
  border: 2px solid #0d6efd;
  background-color: #e7f1ff;
  box-shadow: 0 0 5px rgba(13,110,253,0.3);
}

/* Normal button look */
.type-option {
    border: 2px solid #007bff;
    color: #007bff;
    background-color: white;
    transition: all 0.2s ease-in-out;
}

/* When active (radio checked) */
.type-option.active {
    background-color: #007bff !important;
    color: white !important;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}

/* Optional: little hover feedback */
.type-option:hover {
    background-color: #e6f0ff;
}
</style>

@section('content')
  <link rel="stylesheet" href="{{asset('public/assets/admin/css/voucher.css')}}">
  <link rel="stylesheet" href="{{asset('assets/admin/css/voucher.css')}}">
     <!-- Page Header -->
     <div class="container-fluid px-4 py-3">
          @include("admin-views.voucher.edit_include.edit_include_heading")
        <div class="bg-white shadow rounded-lg p-4">


            {{-- Step 1: Select Voucher Type and Step 2: Select Management Type  --}}
             @include("admin-views.voucher.edit_include.edit_include_client_voucher_management")

            <form action="javascript:" method="post" id="item_form" enctype="multipart/form-data">
                 <input type="hidden" name="hidden_value" id="hidden_value" value="1"/>
            <input type="hidden" name="hidden_bundel" id="hidden_bundel" value="simple"/>
            <input type="hidden" name="hidden_name" id="hidden_name" value="Gift"/>
                @csrf
                @php
                    $language = \App\Models\BusinessSetting::where('key', 'language')->first();
                    $language = $language->value ?? null;
                    $defaultLang = str_replace('_', '-', app()->getLocale());
                    
                    // Initialize variables for Gift Voucher
                    $selected_occasions = is_array($product->occasions_id) ? $product->occasions_id : json_decode($product->occasions_id ?? '[]', true);
                    $howAndConditionIds = is_array($product->how_and_condition_ids) ? $product->how_and_condition_ids : json_decode($product->how_and_condition_ids ?? '[]', true);
                    $savedHowToWorkId = $howAndConditionIds[0] ?? '';

                    // Recipient Info
                    $recipientSettings = is_array($product->recipient_info_form_fields) ? $product->recipient_info_form_fields : json_decode($product->recipient_info_form_fields ?? '[]', true);
                    $savedFormFields = $recipientSettings['form_fields'] ?? [];
                    $savedRequiredFields = $recipientSettings['required_fields'] ?? [];

                    // Message Template
                    $savedMessageTemplate = is_array($product->message_template_style) ? $product->message_template_style : json_decode($product->message_template_style ?? 'null', true);
                    
                    // Delivery Options

                    $raw = $product->getRawOriginal('delivery_options');

                    // double decode
                    $savedDeliveryOptions = json_decode(json_decode($raw, true), true);

                    if (!is_array($savedDeliveryOptions)) {
                        $savedDeliveryOptions = [];
                    }

                    // Amount Config
                    $savedAmountType = $product->amount_type ?? 'fixed';
                    $savedEnableCustom = $product->enable_custom_amount ?? 0;
                    $savedFixedAmounts = is_array($product->fixed_amount_options) ? $product->fixed_amount_options : json_decode($product->fixed_amount_options ?? '[]', true);
                    $savedMinMax = is_array($product->min_max_amount) ? $product->min_max_amount : json_decode($product->min_max_amount ?? '[]', true); // [0]=>min, [1]=>max

                    // Bonus Config
                    $savedBonusTiers = is_array($product->bonus_configuration) ? $product->bonus_configuration : json_decode($product->bonus_configuration ?? '[]', true);
                    if (empty($savedBonusTiers)) {
                        $savedBonusTiers = [["min_amount" => "0", "max_amount" => "", "bonus_percentage" => ""]];
                    }
                @endphp
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
                                <input type="text" name="voucher_title" class="form-control" placeholder="Voucher Title" value="{{ $product->name ?? '' }}" required>
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
                                <textarea type="text" name="description" class="form-control min-h-90px ckeditor" required>{{ $product->description ?? '' }}</textarea>
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


                     <div class="section-card rounded p-4 mb-4">
                        <div class="col-12 mt-3">
                            <p class="text-muted mb-3">Select occasions for this gift card</p>
                            <div class="form-group mb-0">
                                <label class="input-label">{{ translate('Occasions') }}</label>
                                @php
                                    // $selected_occasions initialized at top
                                @endphp
                                <div class="d-flex flex-wrap">
                                    @foreach (\App\Models\GiftOccasions::all() as $item)
                                        <div class="form-check me-3 mb-2">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="occasions_id[]"
                                                value="{{ $item->id }}"
                                                {{ in_array($item->id, $selected_occasions) ? 'checked' : '' }}
                                                id="occasion_{{ $item->id }}">
                                            <label class="form-check-label" for="occasion_{{ $item->id }}">
                                                {{ $item->title }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Recipient Info Form Fields-->
                    <!-- <div class="section-card rounded p-4 mb-4">
                        <h3 class="h5 fw-semibold mb-4">Recipient Info Form Fields</h3>
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <p class="text-muted mb-4">Select which fields will appear on the recipient info form</p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40%">Field Name</th>
                                                <th width="30%" class="text-center">Show Field</th>
                                                <th width="30%" class="text-center">Mark as Required</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $fields = [
                                                    'sender_name' => ['label' => 'Sender Name', 'desc' => 'Who is sending the gift card', 'default' => false],
                                                    'sender_email' => ['label' => 'Sender Email', 'desc' => "Sender's email address", 'default' => false],
                                                    'recipient_name' => ['label' => 'Recipient Name', 'desc' => 'Who will receive the gift card', 'default' => true],
                                                    'recipient_email' => ['label' => 'Recipient Email', 'desc' => 'Where to send the gift card', 'default' => true],
                                                    'recipient_phone' => ['label' => 'Recipient Phone', 'desc' => 'Phone number for WhatsApp delivery', 'default' => false],
                                                    'message' => ['label' => 'Personal Message', 'desc' => 'Custom message from sender', 'default' => false],
                                                    'delivery_date' => ['label' => 'Scheduled Delivery Date', 'desc' => 'When to send the gift card', 'default' => false],
                                                    'recipient_address' => ['label' => 'Recipient Address', 'desc' => 'Physical address (for physical cards)', 'default' => false],
                                                ];
                                            @endphp

                                            @foreach($fields as $key => $field)
                                            @php
                                                $isShowChecked = in_array($key, $savedFormFields);
                                                // If initializing new record or no saved data, logic might fallback to default, but here we assume edit page edits existing.
                                                // If savedFormFields is empty, maybe we should use defaults? 
                                                // But usually empty means user unchecked all. So strict check is better.
                                                // However, for pre-existing records that didn't have this column, this might be issue. 
                                                // Assuming $product exists, distinct logic applies.
                                                
                                                $isRequiredChecked = in_array($key, $savedRequiredFields);
                                                $isDisabled = !$isShowChecked ? 'disabled' : '';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $field['label'] }}</strong>
                                                    <small class="d-block text-muted">{{ $field['desc'] }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input field-toggle" type="checkbox" 
                                                            id="field_{{ $key }}" 
                                                            name="form_fields[]" 
                                                            value="{{ $key }}" 
                                                            data-target="req_{{ $key }}"
                                                            {{ $isShowChecked ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input" type="checkbox" 
                                                            id="req_{{ $key }}" 
                                                            name="required_fields[]" 
                                                            value="{{ $key }}" 
                                                            {{ $isDisabled }}
                                                            {{ $isRequiredChecked ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class=" mt-3 mb-0 p-2 " style="background:#005555;color:white">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> Enable "Show Field" first, then you can mark it as required. Recipient Name and Email are enabled by default.
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Message Template Style-->
                    <!-- <div class="section-card rounded p-4 mb-4">
                        <h3 class="h5 fw-semibold mb-4">Message Template Style</h3>
                        <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <p class="text-muted mb-3">Select one message template style for this gift card</p>
                                <div class="row g-3">
                                    @foreach (\App\Models\MessageTemplate::all() as $item)
                                        @php(
                                        $id = 'template_' . $item->id
                                        )
                                        <div class="col-md-6">
                                        <input type="radio" 
                                            class="btn-check template-radio" 
                                            name="message_template_style" 
                                            id="{{ $id }}" 
                                            value="{{ $item->id }}"
                                            {{ $savedMessageTemplate == $item->id ? 'checked' : '' }}>
                                        <label class="template-card border rounded p-3 w-100 d-block {{ $savedMessageTemplate == $item->id ? 'selected' : '' }}" for="{{ $id }}">
                                            <img src="{{ asset($item->icon) }}" alt="" style="width: 25px; height: 25px;">
                                            <strong class="ms-2">{{ $item->title }}</strong>
                                            <small class="d-block text-muted mt-1">{{ $item->sub_title }}</small>
                                        </label>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- Delivery Options-->
                    <!-- <div class="section-card rounded p-4 mb-4">
                        <h3 class="h5 fw-semibold mb-4">Delivery Options</h3>
                        {{-- tags --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <p class="text-muted mb-3">Select how gift cards will be delivered to recipients</p>

                            <div class="row">
                                @foreach (\App\Models\DeliveryOption::all() as $item)
                                    @php(
                                        $id = 'delivery_' . $item->id
                                        )
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-primary h-100">
                                            <div class="card-body">
                                                <div class="form-check form-switch">
                                                    <input
                                                        class="form-check-input delivery-option"
                                                        type="checkbox"
                                                        id="{{ $id }}"
                                                        name="delivery_options[]"
                                                        value="{{ $item->id }}"
                                                        {{ in_array($item->id , $savedDeliveryOptions) ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label" for="{{ $id }}">
                                                        <h6 class="mb-1">
                                                        <img src="{{ asset($item->icon) }}" alt="" style="width: 25px; height: 25px;">
                                                        {{ $item->title }}
                                                        </h6>
                                                        <small class="text-muted">{{ $item->sub_title }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>


                            <div class=" mt-3 mb-0 p-2 " style="background:#005555;color:white">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> At least one delivery option must be selected. Email is enabled by default.
                                </div>
                            </div>
                        </div>
                    </div> -->

                    {{-- Amount Configuration --}}
                    <div class="section-card rounded p-4 mb-4">
                        <h3 class="h5 fw-semibold mb-4"> Amount Configuration</h3>
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Amount Type <span class="text-danger">*</span></label>
                                <div class="row g-2" id="amountTypeGroup">
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="type" id="type_fixed" value="fixed" {{ $savedAmountType == 'fixed' ? 'checked' : '' }}>
                                            <label class="btn border type-option " for="type_fixed">
                                                <i class="fas fa-list"></i> Fixed Amounts
                                            </label>
                                        </div>

                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="type" id="type_range" value="range" {{ $savedAmountType == 'range' ? 'checked' : '' }}>
                                            <label class="btn border type-option " for="type_range">
                                                <i class="fas fa-arrows-alt-h"></i> Range
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input" type="checkbox" id="enable_custom_amount" name="enable_custom_amount" value="1" {{ $savedEnableCustom ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_custom_amount">
                                            <strong>Enable Custom Amount</strong>
                                            <small class="d-block text-muted">Allow customers to enter their own amount</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Fixed Amounts Section -->
                                <div id="fixedAmountsSection" style="display: none;">
                                    <label class="form-label">Fixed Amount Options <span class="text-danger">*</span></label>
                                    <div id="fixedAmountsContainer">
                                        @if(!empty($savedFixedAmounts) && count($savedFixedAmounts) > 0)
                                            @foreach($savedFixedAmounts as $index => $amount)
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" name="fixed_amounts[]" step="0.01" min="0" placeholder="25.00" value="{{ $amount }}">
                                                    <button type="button" class="btn btn-danger remove-amount" {{ count($savedFixedAmounts) == 1 ? 'style="display: none;"' : '' }}>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="fixed_amounts[]" step="0.01" min="0" placeholder="25.00">
                                                <button type="button" class="btn btn-danger remove-amount" style="display: none;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary border" id="addAmountBtn">
                                        <i class="fas fa-plus"></i> Add Another Amount
                                    </button>
                                </div>

                                <!-- Range Amounts Section -->
                                <div id="rangeAmountsSection" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="min_amount" class="form-label">Minimum Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('min_amount') is-invalid @enderror"
                                                    id="min_amount" name="min_max_amount[]" step="0.01" min="0" value="{{ $savedMinMax[0] ?? '' }}" placeholder="10.00">
                                            </div>
                                            @error('min_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="max_amount" class="form-label">Maximum Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('max_amount') is-invalid @enderror"
                                                    id="max_amount" name="min_max_amount[]" step="0.01" min="0" value="{{ $savedMinMax[1] ?? '' }}" placeholder="1000.00">
                                            </div>
                                            @error('max_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Bonus Configuration --}}
                    <div class="section-card rounded p-4 mb-4">
                        <h3 class="h5 fw-semibold mb-4"> Bonus Configuration</h3>
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <input type="hidden" name="bonus_enabled" value="1">
                                <input type="hidden" name="bonus_type" value="percentage">

                                <p class="text-muted mb-3">Set bonus percentage based on top-up amount ranges</p>

                                <div id="bonusTiersContainer">
                                    @foreach($savedBonusTiers as $index => $tier)
                                        <div class="bonus-tier-item border rounded p-3 mb-3">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">Min Amount ($) <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="bonus_tiers[{{ $index }}][min_amount]" step="0.01" min="0" placeholder="0" value="{{ $tier['min_amount'] ?? '' }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Max Amount ($) <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="bonus_tiers[{{ $index }}][max_amount]" step="0.01" min="0" placeholder="100" value="{{ $tier['max_amount'] ?? '' }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Bonus (%) <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" name="bonus_tiers[{{ $index }}][bonus_percentage]" step="0.01" min="0" placeholder="5" value="{{ $tier['bonus_percentage'] ?? '' }}" required>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger remove-bonus-tier" {{ count($savedBonusTiers) == 1 ? 'style="display: none;"' : '' }}>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-secondary" id="addBonusTierBtn">
                                    <i class="fas fa-plus"></i> Add Another Tier
                                </button>

                                <div class=" mt-3 mb-0 p-2"  style="background:#005555;color:white">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Example:</strong> $0-$100 = 5% bonus, $101-$500 = 10% bonus, $501+ = 15% bonus
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
        // Add bonus tier functionality
        const addBonusTierBtn = document.getElementById('addBonusTierBtn');
        const bonusTiersContainer = document.getElementById('bonusTiersContainer');

        // Enter key prevention moved to the bottom submit handler section

        let bonusTierIndex = 1;

        addBonusTierBtn.addEventListener('click', function() {
            const newTier = document.createElement('div');
            newTier.className = 'bonus-tier-item border rounded p-3 mb-3';
            newTier.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Min Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="bonus_tiers[${bonusTierIndex}][min_amount]" step="0.01" min="0" placeholder="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Max Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="bonus_tiers[${bonusTierIndex}][max_amount]" step="0.01" min="0" placeholder="100" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bonus (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="bonus_tiers[${bonusTierIndex}][bonus_percentage]" step="0.01" min="0" placeholder="5" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-bonus-tier">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            bonusTiersContainer.appendChild(newTier);
            bonusTierIndex++;

            newTier.querySelector('.remove-bonus-tier').addEventListener('click', function() {
                newTier.remove();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.type-option');
            const radios = document.querySelectorAll('input[name="type"]');

            function updateActive() {
                options.forEach(opt => opt.classList.remove('active'));
                const checked = document.querySelector('input[name="type"]:checked');
                if (checked) {
                    const label = document.querySelector(`label[for="${checked.id}"]`);
                    if (label) label.classList.add('active');
                }
            }

            radios.forEach(radio => {
                radio.addEventListener('change', updateActive);
            });

            // Initialize on load (for old() / pre-selected value)
            updateActive();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.querySelectorAll('input[name="type"]');
            const fixedSection = document.getElementById('fixedAmountsSection');
            const rangeSection = document.getElementById('rangeAmountsSection');
            const addAmountBtn = document.getElementById('addAmountBtn');
            const fixedContainer = document.getElementById('fixedAmountsContainer');

            // 🔹 Toggle between Fixed and Range sections
            function toggleSections() {
                const selectedType = document.querySelector('input[name="type"]:checked')?.value;
                fixedSection.style.display = selectedType === 'fixed' ? 'block' : 'none';
                rangeSection.style.display = selectedType === 'range' ? 'block' : 'none';

                // Update required attributes
                const fixedInputs = fixedSection.querySelectorAll('input[name="fixed_amounts[]"]');
                const rangeInputs = rangeSection.querySelectorAll('input[name="min_max_amount[]"]');

                if (selectedType === 'fixed') {
                    fixedInputs.forEach(input => input.required = true);
                    rangeInputs.forEach(input => input.required = false);
                } else {
                    fixedInputs.forEach(input => input.required = false);
                    rangeInputs.forEach(input => input.required = true);
                }
            }

            typeSelect.forEach(radio => {
                radio.addEventListener('change', toggleSections);
            });
            toggleSections(); // Initial setup

            // 🔹 Add new fixed amount input
            addAmountBtn.addEventListener('click', function() {
                const selectedType = document.querySelector('input[name="type"]:checked')?.value;
                const isRequired = selectedType === 'fixed' ? 'required' : '';
                const newField = document.createElement('div');
                newField.className = 'input-group mb-2';
                newField.innerHTML = `
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" name="fixed_amounts[]" step="0.01" min="0" placeholder="25.00" ${isRequired}>
                    <button type="button" class="btn btn-danger remove-amount">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                fixedContainer.appendChild(newField);

                // Remove field
                newField.querySelector('.remove-amount').addEventListener('click', function() {
                    newField.remove();
                });
            });

            // 🔹 Make first fixed input's remove button visible only after adding more
            const observer = new MutationObserver(() => {
                const allRemoveBtns = fixedContainer.querySelectorAll('.remove-amount');
                allRemoveBtns.forEach((btn, i) => {
                    btn.style.display = (i === 0 && allRemoveBtns.length === 1) ? 'none' : 'inline-block';
                });
            });
            observer.observe(fixedContainer, { childList: true, subtree: false });
        });
    // Form field toggle functionality
        document.querySelectorAll('.field-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const targetId = this.getAttribute('data-target');
                const requiredToggle = document.getElementById(targetId);

                if (this.checked) {
                    // Enable the "Mark as Required" toggle when field is shown
                    requiredToggle.disabled = false;
                } else {
                    // Disable and uncheck the "Mark as Required" toggle when field is hidden
                    requiredToggle.disabled = true;
                    requiredToggle.checked = false;
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.template-radio');

        radios.forEach(radio => {
            radio.addEventListener('change', function () {
            document.querySelectorAll('.template-card').forEach(card => {
                card.classList.remove('selected');
            });
            const label = document.querySelector('label[for="' + this.id + '"]');
            label.classList.add('selected');
            });
        });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const options = document.querySelectorAll('.delivery-option');

            options.forEach(opt => {
                opt.addEventListener('change', function() {
                    const checkedOptions = document.querySelectorAll('.delivery-option:checked');

                    // agar user last checked ko uncheck kar raha hai → prevent it
                    if (checkedOptions.length === 0) {
                        this.checked = true;
                        alert('At least one delivery option must remain selected.');
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const managementSelection = document.querySelectorAll('#management_selection');
            const voucherCards = document.querySelectorAll('.voucher-card');
            const voucherCards2 = document.querySelectorAll('.voucher-card_2');
             const allimages = document.getElementById('allimages');
            // Move these functions OUTSIDE of DOMContentLoaded to make them globally accessible
            function section_one(loopIndex, primaryId,name) {

                 if (loopIndex === "1" || name === "Delivery/Pickup") {
                    window.location.href = "{{ url('admin/Voucher/add-new') }}";
                } else

                if (loopIndex === "2" || name === "In-Store") {
                    window.location.href = "{{ url('admin/Voucher/add-new-store') }}";
                } else if (loopIndex === "3" || name === "Flat discount") {
                    window.location.href = "{{ url('admin/Voucher/add-flat-discount') }}";
                }
                //  else if (loopIndex === "4" || name === "Gift") {
                //     window.location.href = "{{ url('admin/Voucher/add-gift') }}";
                // }


                //   if (loopIndex === "1" || name === "Delivery/Pickup") {
                //     window.location.href = "{{ url('admin/Voucher/add-new') }}";
                // } else if (loopIndex === "2" || name === "Flat discount") {
                //     window.location.href = "{{ url('admin/Voucher/add-new') }}";
                // }


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
        getDataFromServer(8)

    
  function getDataFromServer(voucher_id) {
            // alert(storeId)
            
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
                    // Check if this radio button should be pre-selected
                    console.log('Comparing saved:', savedHowtoWork, 'with item:', item.id);
                    let isChecked = (savedHowtoWork == item.id) ? 'checked' : '';
                    
                    workHtml += `
                        <div class="card mb-3 work-item shadow-sm">
                            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 cursor-pointer"
                                onclick="toggleAccordion(${item.id})">
                                
                                <div class="d-flex align-items-center flex-grow-1">
                                    <input type="radio" name="howto_work[]" value="${item.id}"
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

                $.each(response.usage_term_management, function (index, term) {
                    usageHtml += `
                        <div class="usage-item border rounded-lg mb-4 p-4 col-6">
                            <div class="flex items-center gap-2 mb-2">
                                <input
                                    class="form-check-input step-checkbox"
                                    name="term_and_condition[]"
                                    type="checkbox"
                                    value="${term.id}"
                                    id="term${term.id}">

                                <label for="term${term.id}" class="font-bold text-lg cursor-pointer m-0">
                                    ${term.baseinfor_condition_title}
                                </label>
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
                        $('#segment_type').empty().append('<option value="">Select Product</option>');
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
            // 1. Enter Key Prevention (Aggressive)
            $(document).on('keydown', '#item_form', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    if (e.target.tagName !== 'TEXTAREA' && !$(e.target).hasClass('ckeditor')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }
            });

            $('#item_form').on('submit', function(e) {
                e.preventDefault();

                // 2. Browser standard validation (Title, etc.)
                if (!this.checkValidity()) {
                    this.reportValidity();
                    return false;
                }

                // 3. Description Validation (CKEditor)
                let description = "";
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.description) {
                    description = CKEDITOR.instances.description.getData();
                } else {
                    description = $('textarea[name="description"]').val();
                }

                if (!description || description.trim() === "") {
                    toastr.error("{{ translate('messages.description_field_is_required') }}");
                    return false;
                }

                // 4. Thumbnail Validation
                let hasThumbnail = false;
                let viewerSrc = $('#viewer').attr('src');
                if (viewerSrc && !viewerSrc.includes('upload-img.png') && !viewerSrc.includes('upload.png')) {
                    hasThumbnail = true; 
                }
                if ($('#customFileEg1')[0] && $('#customFileEg1')[0].files && $('#customFileEg1')[0].files.length > 0) {
                    hasThumbnail = true;
                }
                if (!hasThumbnail) {
                    toastr.error("{{ translate('messages.item_thumbnail_is_required') }}");
                    return false;
                }

                // 5. Item Images Validation
                let hasItemImages = false;
                // Check existing images that are not hidden
                if ($('div[id^="product_images_"]').not('.d-none').length > 0) {
                    hasItemImages = true;
                }
                // Check Spartan added images (newly picked but not yet submitted)
                if ($('.spartan_item_wrapper img').filter(function(){
                    let src = $(this).attr('src');
                    return src && !src.includes('upload-img.png');
                }).length > 0) {
                    hasItemImages = true;
                }
                // Also check file inputs specifically
                if ($('input[name="item_images[]"]').filter(function(){ return this.files && this.files.length > 0 }).length > 0) {
                    hasItemImages = true;
                }

                if (!hasItemImages) {
                    toastr.error("{{ translate('messages.item_images_are_required') }}");
                    return false;
                }

                // If all validations pass, THEN show loader
                $('#submitButton').attr('disabled', true);
                $('#loading').show();

                let formData = new FormData(this);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
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
                    success: function(data) {
                        $('#loading').hide();
                        $('#submitButton').attr('disabled', false);

                        if (data.errors && Array.isArray(data.errors)) {
                            data.errors.forEach(function(err) {
                                toastr.error(err.message, {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            });
                            return;
                        }

                        toastr.success("{{ translate('messages.voucher_updated_successfully') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function() {
                            location.href = "{{ route('admin.Voucher.list') }}";
                        }, 1000);
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        $('#submitButton').attr('disabled', false);
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            if (Array.isArray(errors)) {
                                errors.forEach(function(err) {
                                    toastr.error(err.message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                });
                            } else {
                                $.each(errors, function(key, err) {
                                    toastr.error(err.message || err, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                });
                            }
                        } else {
                            toastr.error("{{ translate('messages.failed_to_update_voucher') }}", {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    }
                });
            });
        });

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
        });

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
          //   findBranch
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

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
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
@endpush
