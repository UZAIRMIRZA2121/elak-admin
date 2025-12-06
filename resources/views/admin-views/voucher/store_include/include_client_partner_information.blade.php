<!-- Client Information -->
<div class="section-card rounded p-4 mb-4" id="basic_info_main">
     <div class="form-group mb-0 p-2">
        <label class="input-label" for="num_clients">{{ translate('Client Information') }}
            <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
        </label>
    </div>
    
    <!-- Number of Clients Input -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="form-group">
                <label class="input-label fw-bold">Number of Clients</label>
                <input type="number" id="num_clients" class="form-control" placeholder="Enter number between 1-20" min="1" max="20" value="">
                <small class="text-muted">Enter a number between 1-20</small>
            </div>
        </div>
    </div>

    <div id="client_repeater">
        <!-- Rows will be generated here when you enter a number -->
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
// Client options HTML
let clientOptionsHtml = '';

// Initialize Select2 for client dropdown WITH PROPER EVENT BINDING
function initClientSelect2(element) {
    if (!element || element.length === 0) {
        console.warn('No element to initialize');
        return;
    }
    
    // Destroy existing Select2 if present
    if (element.hasClass('select2-hidden-accessible')) {
        element.select2('destroy');
    }
    
    // Initialize Select2
    element.select2({
        placeholder: "Select Client",
        allowClear: true,
        width: '100%'
    });
    
    // CRITICAL: Bind change event AFTER Select2 initialization
    element.off('select2:select').on('select2:select', function(e) {
        console.log('✓ Select2:select event fired');
        console.log('Selected ID:', e.params.data.id);
        handleClientChange($(this));
    });
    
    element.off('change.myCustom').on('change.myCustom', function(e) {
        console.log('✓ Change event fired');
        handleClientChange($(this));
    });
    
    console.log('✓ Select2 initialized with events for:', element.attr('name'));
}

// Initialize Select2 for segment dropdown
function initSegmentSelect2(element) {
    if (!element || element.length === 0) {
        console.warn('No segment element to initialize');
        return;
    }
    
    if (element.hasClass('select2-hidden-accessible')) {
        element.select2('destroy');
    }
    
    element.select2({
        placeholder: "Select Segment",
        allowClear: true,
        width: '100%'
    });
    
    console.log('✓ Segment Select2 initialized:', element.attr('name'));
}

// MAIN AJAX HANDLER - Load app name AND segments
function handleClientChange(selectElement) {
    console.log('');
    console.log('========================================');
    console.log('=== handleClientChange CALLED ===');
    console.log('========================================');
    
    let clientId = selectElement.val();
    let currentRow = selectElement.closest('.item-row');
    let appNameInput = currentRow.find('.app-name-input');
    let segmentSelect = currentRow.find('.segment-select');
    let appNameError = currentRow.find('.app-name-error');
    let segmentError = currentRow.find('.segment-error');
    
    console.log('Client ID:', clientId);
    console.log('Row found:', currentRow.length);
    console.log('App Name Input found:', appNameInput.length);
    console.log('Segment Select found:', segmentSelect.length);
    
    // Reset if no client selected
    if (!clientId || clientId === '') {
        console.log('No client selected, resetting fields');
        appNameInput.val('').removeClass('is-invalid');
        appNameError.hide();
        segmentSelect.html('<option disabled>Select client first</option>').trigger('change');
        segmentError.hide();
        return;
    }

    console.log('Making AJAX call to fetch data...');
    
    // AJAX call
    $.ajax({
        url: "{{ route('admin.Voucher.getAppName') }}",
        type: "GET",
        data: { client_id: clientId },
        dataType: "json",
        beforeSend: function() {
            console.log('AJAX beforeSend...');
            appNameInput.val('Loading...').removeClass('is-invalid');
            appNameError.hide();
            segmentSelect.html('<option disabled>Loading...</option>').trigger('change');
            segmentError.hide();
        },
        success: function(response) {
            console.log('');
            console.log('========================================');
            console.log('=== AJAX SUCCESS ===');
            console.log('========================================');
            console.log('Full Response:', response);
            console.log('App Name:', response.app_name);
            console.log('Segments:', response.segments);
            
            // Handle App Name
            if (response && response.app_name && response.app_name.trim() !== '') {
                appNameInput.val(response.app_name).removeClass('is-invalid');
                appNameError.hide();
                console.log('✓ App name set successfully:', response.app_name);
            } else {
                appNameInput.val('Not Found').addClass('is-invalid');
                appNameError.text('App name not found for this client').show();
                console.log('✗ App name NOT found in response');
            }
            
            // Handle Segments
            if (response && response.segments && Array.isArray(response.segments) && response.segments.length > 0) {
                let options = '';
                $.each(response.segments, function(key, item) {
                    options += '<option value="' + item.id + '">' + item.name + '</option>';
                    console.log('  - Segment:', item.name, '(ID:', item.id + ')');
                });
                segmentSelect.html(options).trigger('change');
                segmentError.hide();
                console.log('✓ Total segments loaded:', response.segments.length);
                
                if (typeof toastr !== 'undefined') {
                    toastr.success('Client data loaded successfully!');
                }
            } else {
                segmentSelect.html('<option disabled>No segments available</option>').trigger('change');
                segmentError.text('No segments found for this client').show();
                console.log('✗ No segments found in response');
                
                if (typeof toastr !== 'undefined') {
                    toastr.warning('No segments available for this client');
                }
            }
            
            console.log('========================================');
        },
        error: function(xhr, status, error) {
            console.log('');
            console.log('========================================');
            console.log('=== AJAX ERROR ===');
            console.log('========================================');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Status Code:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.log('========================================');
            
            appNameInput.val('Error').addClass('is-invalid');
            appNameError.text('Error loading data. Please try again.').show();
            
            segmentSelect.html('<option disabled>Error loading</option>').trigger('change');
            segmentError.text('Error loading segments. Please try again.').show();
            
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load client data!');
            }
        }
    });
}

// Generate client rows
function generateClientRows(numRows) {
    console.log('');
    console.log('========================================');
    console.log('=== Generating', numRows, 'rows ===');
    console.log('========================================');
    
    let repeater = $('#client_repeater');
    
    // Clear existing rows
    repeater.empty();
    console.log('Cleared existing rows');
    
    // Generate new rows
    for (let i = 0; i < numRows; i++) {
        let newRowHtml = `
            <div class="row item-row mb-3" data-row-index="${i}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="input-label">Client Name ${i + 1}</label>
                        <select name="clients[${i}][client_id]" class="form-control client-select" data-row="${i}">
                            ${clientOptionsHtml}
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">Client App Name</label>
                        <input type="text" name="clients[${i}][app_name]" class="form-control app-name-input" placeholder="Client App Name" readonly>
                        <small class="text-danger app-name-error" style="display:none;"></small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="input-label">Segment</label>
                        <select name="clients[${i}][segment][]" class="form-control segment-select" data-placeholder="Select Segment" multiple>
                            <option disabled>Select client first</option>
                        </select>
                        <small class="text-danger segment-error" style="display:none;"></small>
                    </div>
                </div>

                <div class="col-md-1">
                    <label>&nbsp;</label>
                </div>
            </div>
        `;
        
        repeater.append(newRowHtml);
        console.log('Row', (i + 1), 'HTML added');
    }
    
    // Initialize Select2 on ALL new dropdowns
    console.log('Initializing Select2 on all rows...');
    $('.client-select').each(function(index) {
        console.log('Initializing client select', (index + 1));
        initClientSelect2($(this));
    });
    
    $('.segment-select').each(function(index) {
        console.log('Initializing segment select', (index + 1));
        initSegmentSelect2($(this));
    });
    
    console.log('✓ All', numRows, 'rows generated and initialized');
    console.log('========================================');
}

// Document ready
$(document).ready(function() {
    console.log('');
    console.log('========================================');
    console.log('=== PAGE LOADED ===');
    console.log('========================================');
    
    // Store client options HTML
    clientOptionsHtml = `
        <option value="">Select Client</option>
        @foreach (\App\Models\Client::all() as $item)
            <option value="{{ $item->id }}" data-app-name="{{ $item->app_name ?? '' }}">{{ $item->name }}</option>
        @endforeach
    `;
    
    console.log('✓ Client options HTML stored');
    console.log('✓ Waiting for user to enter number of clients');
    console.log('========================================');
});

// Number input change handler
$(document).on('input change keyup', '#num_clients', function() {
    let numClients = parseInt($(this).val());
    
    console.log('Number input changed:', $(this).val());
    
    // If empty, clear repeater
    if ($(this).val() === '' || isNaN(numClients)) {
        $('#client_repeater').empty();
        console.log('Input empty, cleared repeater');
        return;
    }
    
    // Validation
    if (numClients < 1) {
        numClients = 1;
        $(this).val(1);
    }
    
    if (numClients > 20) {
        numClients = 20;
        $(this).val(20);
        if (typeof toastr !== 'undefined') {
            toastr.warning('Maximum 20 clients allowed');
        }
    }
    
    // Generate rows
    generateClientRows(numClients);
});

// Store Change
$(document).on('change', '#store_id', function() {
    let storeId = $(this).val();
    alert(storeId);
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