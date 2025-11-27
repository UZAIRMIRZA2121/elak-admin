<!-- Client Information -->
<div class="section-card rounded p-4 mb-4" id="basic_info_main">
    <h3 class="h5 fw-semibold mb-4">Client Information</h3>
    <div id="client_repeater">

        <div class="row item-row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="input-label">Client Name</label>
                    <select name="clients[0][client_id]" class="form-control client-select">
                        <option value="">Select Client</option>
                        @foreach (\App\Models\Client::all() as $item)
                            <option value="{{ $item->id }}" data-app-name="{{ $item->app_name ?? '' }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <label class="input-label">Client App Name</label>
                    <input type="text" name="clients[0][app_name]" class="form-control app-name-input" placeholder="Client App Name" readonly>
                </div>
            </div>

            <div class="col-md-1">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger remove-btn" style="display:none">X</button>
            </div>
        </div>

    </div>
    <button type="button" class="btn btn-primary mt-2" id="add-more-btn">Add More</button>
    
    <div class="form-group mt-3">
        <label class="input-label" for="segment_type">{{ translate('Segment') }}
            <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Segment') }}"></span>
        </label>
        <select name="segment_type[]" id="segment_type" required class="form-control js-select2-custom" data-placeholder="{{ translate('Select Segment') }}" multiple>
        </select>
    </div>
</div>

<!-- Partner Information -->
<div class="section-card rounded p-4 mb-4" id="store_category_main">
    <h3 class="h5 fw-semibold mb-4">{{ translate('Partner Information') }}</h3>
    <div class="col-md-12">
        <div class="row g-2 align-items-end">
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="store_id">
                        {{ translate('messages.store') }}
                        <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.') }}"> *</span>
                    </label>
                    <select name="store_id" id="store_id" data-placeholder="{{ translate('messages.select_store') }}" class="js-data-example-ajax form-control">
                    </select>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="categories">{{ translate('messages.category') }}
                        <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
                    </label>
                    <select name="categories[]" id="categories" data-placeholder="{{ translate('messages.select_category') }}" class="js-data-example-ajax js-select2-custom form-control js-select2-category" multiple>
                    </select>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="sub_categories_game">{{ translate('messages.sub_category') }}</label>
                    <select name="sub_categories_game[]" class="form-control js-select2-custom js-select2-sub_category" data-placeholder="{{ translate('messages.select_sub_category') }}" id="sub_categories_game" multiple>
                    </select>
                </div>
            </div>
            
            <div class="col-sm-12">
                <div class="form-group mb-0">
                    <label class="input-label" for="sub_branch_id">{{ translate('Branches') }}
                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Branches') }}"></span>
                    </label>
                    <select name="sub_branch_id[]" id="sub-branch" required class="form-control js-select2-custom" data-placeholder="{{ translate('Select Branches') }}" multiple>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let clientIndex = 1;

// Initialize Select2 for a specific element
function initSelect2(element) {
    if (!element || element.length === 0) return;
    
    // Destroy existing Select2 if any
    if (element.hasClass('select2-hidden-accessible')) {
        element.select2('destroy');
    }
    
    // Initialize new Select2
    element.select2({
        placeholder: "Select Client",
        allowClear: true,
        width: '100%'
    });
    
    // Bind Select2 change event immediately after initialization
    element.on('select2:select', function(e) {
        console.log('Select2 select event fired');
        handleClientChange($(this));
    });
    
    // Also bind regular change event as backup
    element.on('change', function(e) {
        if (!e.originalEvent) { // Avoid double firing
            console.log('Regular change event fired');
            handleClientChange($(this));
        }
    });
}

$(document).ready(function() {
    console.log('Page loaded');
    
    // Initialize Select2 on all existing client-select dropdowns
    $('.client-select').each(function() {
        initSelect2($(this));
    });
});

// Handle client change - separate function
function handleClientChange(selectElement) {
    console.log('=== handleClientChange called ===');
    
    let clientId = selectElement.val();
    let currentRow = selectElement.closest('.item-row');
    let appNameInput = currentRow.find('.app-name-input');
    
    console.log('Selected Client ID:', clientId);
    console.log('Current Row:', currentRow.length);
    console.log('App Name Input:', appNameInput.length);
    
    if (!clientId) {
        appNameInput.val('');
        return;
    }

    // AJAX call
    console.log('Making AJAX call for client:', clientId);
    
    $.ajax({
        url: "{{ route('admin.Voucher.getAppName') }}",
        type: "GET",
        data: { client_id: clientId },
        dataType: "json",
        beforeSend: function() {
            console.log('AJAX request started...');
            appNameInput.val('Loading...');
        },
        success: function(response) {
            console.log("=== AJAX Response ===", response);
            
            if (response && response.app_name) {
                appNameInput.val(response.app_name);
                console.log('App name set:', response.app_name);
            } else {
                appNameInput.val('');
                console.log('No app name found in response');
                if (typeof toastr !== 'undefined') {
                    toastr.warning('App name not found');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("=== AJAX Error ===");
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("Response Text:", xhr.responseText);
            console.error("Status Code:", xhr.status);
            appNameInput.val('');
            if (typeof toastr !== 'undefined') {
                toastr.error('Error fetching app name');
            }
        }
    });
}

// Client Select Change - Using change event instead of select2:select
$(document).on('change', '.client-select', function(e) {
    console.log('Document level change event - should not fire normally');
});

// Add More Button Click
$(document).on('click', '#add-more-btn', function() {
    console.log('=== Add more clicked ===');
    
    let repeater = $('#client_repeater');
    
    // Get all client options from first dropdown
    let firstSelect = $('.item-row').first().find('.client-select');
    let optionsHtml = firstSelect.html();
    
    console.log('Creating new row with index:', clientIndex);
    
    // Create new row HTML
    let newRowHtml = `
        <div class="row item-row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="input-label">Client Name</label>
                    <select name="clients[${clientIndex}][client_id]" class="form-control client-select">
                        ${optionsHtml}
                    </select>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <label class="input-label">Client App Name</label>
                    <input type="text" name="clients[${clientIndex}][app_name]" class="form-control app-name-input" placeholder="Client App Name" readonly>
                </div>
            </div>

            <div class="col-md-1">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger remove-btn" style="display:block">X</button>
            </div>
        </div>
    `;
    
    // Append new row
    repeater.append(newRowHtml);
    
    // Get the newly added select element
    let newSelect = repeater.find('.item-row').last().find('.client-select');
    
    console.log('New select element found:', newSelect.length);
    
    // Initialize Select2 on new select
    initSelect2(newSelect);
    
    clientIndex++;
    console.log('Row added successfully, new index:', clientIndex);
});

// Remove Button Click
$(document).on('click', '.remove-btn', function() {
    console.log('Remove clicked');
    
    let row = $(this).closest('.item-row');
    let selectElement = row.find('.client-select');
    
    // Destroy Select2 before removing row
    if (selectElement.hasClass('select2-hidden-accessible')) {
        selectElement.select2('destroy');
    }
    
    row.remove();
    console.log('Row removed successfully');
});

// Store Change
$(document).on('change', '#store_id', function() {
    let storeId = $(this).val();
    if (storeId) {
        if (typeof findBranch === 'function') {
            findBranch(storeId);
        }
        multiples_category_by_store_id();
    }
});

// Category Change
$(document).on('change', '#categories', function() {
    multiples_category();
});

// Subcategory Change
$(document).on('change', '#sub_categories_game', function() {
    if (typeof multples_sub_category === 'function') {
        multples_sub_category();
    }
});

// Get Subcategories by Category
function multiples_category() {
    var category_ids_all = $('#categories').val();

    console.log("Selected categories:", category_ids_all);

    if (!category_ids_all || category_ids_all.length === 0) {
        $('#sub_categories_game').html('<option disabled>Select category first</option>');
        $('#sub_categories_game').trigger('change');
        return;
    }

    $.ajax({
        url: "{{ route('admin.Voucher.getSubcategories') }}",
        type: "GET",
        data: { category_ids_all: category_ids_all },
        traditional: true,
        dataType: "json",
        success: function(response) {
            console.log("Subcategories:", response);

            if (!Array.isArray(response) || response.length === 0) {
                $('#sub_categories_game').html('<option disabled>No subcategories found</option>');
            } else {
                let options = '';
                $.each(response, function(key, item) {
                    options += '<option value="' + item.id + '">' + item.name + '</option>';
                });
                $('#sub_categories_game').html(options);
            }
            
            $('#sub_categories_game').trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            $('#sub_categories_game').html('<option disabled>Error loading</option>');
        }
    });
}

// Get Categories by Store
function multiples_category_by_store_id() {
    var store_id = $('#store_id').val();

    console.log("Selected store:", store_id);

    if (!store_id) {
        $('#categories').html('<option disabled>Select store first</option>');
        $('#categories').trigger('change');
        return;
    }

    $.ajax({
        url: "{{ route('admin.Voucher.getCategoty') }}",
        type: "GET",
        data: { store_id: store_id },
        dataType: "json",
        success: function(response) {
            console.log("Categories:", response);

            if (!Array.isArray(response) || response.length === 0) {
                $('#categories').html('<option disabled>No categories found</option>');
            } else {
                let options = '';
                $.each(response, function(key, item) {
                    options += '<option value="' + item.id + '">' + item.name + '</option>';
                });
                $('#categories').html(options);
            }
            
            $('#categories').trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            $('#categories').html('<option disabled>Error loading</option>');
        }
    });
}
</script>