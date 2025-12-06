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

<!-- Partner Information one-->
<div class="section-card rounded p-4 mb-4" id="store_category_main">
    <h3 class="h5 fw-semibold mb-4"> {{ translate('Partner Information') }}</h3>
    {{-- Store & Category Info --}}
    <div class="col-md-12">
        <div class="row g-2 align-items-end">
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="store_id">
                        {{ translate('messages.store') }}
                        <span class="form-label-secondary text-danger"
                            data-toggle="tooltip" data-placement="right"
                            data-original-title="{{ translate('messages.Required.') }}"> *
                        </span>
                    </label>
                    <select name="store_id" id="store_id"
                        data-placeholder="{{ translate('messages.select_store') }}"
                        class="js-data-example-ajax form-control"
                        onchange="findBranch(this.value)">
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label"
                        for="categories">{{ translate('messages.category') }}<span class="form-label-secondary text-danger"
                        data-toggle="tooltip" data-placement="right"
                        data-original-title="{{ translate('messages.Required.')}}"> *
                        </span></label>
                    <select name="categories[]" id="categories" onchange="multiples_category()" data-placeholder="{{ translate('messages.select_category') }}"
                        class="js-data-example-ajax  js-select2-custom form-control js-select2-category" multiple>
                    </select>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label"  for="sub_categories_game">{{ translate('messages.sub_category') }}</label>
                    <select name="sub_categories_game[]" onchange="multples_sub_category()" class=" form-control js-select2-custom js-select2-sub_category" data-placeholder="{{ translate('messages.select_sub_category') }}"
                        id="sub_categories_game" multiple>
                    </select>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group mb-0">
                    <label class="input-label" for="sub_branch_id">{{ translate('Branches') }}<span class="form-label-secondary" data-toggle="tooltip"data-placement="right" data-original-title="{{ translate('Branches') }}"></span> </label>
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

// Initialize Select2 for client dropdown
function initClientSelect2(element) {
    if (!element || element.length === 0) {
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
    
    // Bind change event
    element.off('select2:select').on('select2:select', function(e) {
        handleClientChange($(this));
    });
    
    element.off('change').on('change', function(e) {
        handleClientChange($(this));
    });
}

// Initialize Select2 for segment dropdown
function initSegmentSelect2(element) {
    if (!element || element.length === 0) {
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
}

// MAIN AJAX HANDLER
function handleClientChange(selectElement) {
    
    let clientId = selectElement.val();
    let currentRow = selectElement.closest('.item-row');
    let appNameInput = currentRow.find('.app-name-input');
    let appNameIdInput = currentRow.find('.app-name-id-input');
    let segmentSelect = currentRow.find('.segment-select');
    let appNameError = currentRow.find('.app-name-error');
    let segmentError = currentRow.find('.segment-error');
    
    // Reset if no client selected
    if (!clientId || clientId === '') {
        appNameInput.val('').removeClass('is-invalid');
        appNameIdInput.val('');
        appNameError.hide();
        segmentSelect.html('<option disabled>Select client first</option>').trigger('change');
        segmentError.hide();
        return;
    }
    
    // AJAX call
    $.ajax({
        url: "{{ route('admin.Voucher.getAppName') }}",
        type: "GET",
        data: { client_id: clientId },
        dataType: "json",
        beforeSend: function() {
            appNameInput.val('Loading...').removeClass('is-invalid');
            appNameIdInput.val('');
            appNameError.hide();
            segmentSelect.html('<option disabled>Loading...</option>').trigger('change');
            segmentError.hide();
        },
        success: function(response) {
            // Handle App Name
            if (response && response.app_name && response.app_name.trim() !== '') {
                // Hidden field میں client_id ڈالیں (app_name_id کی جگہ)
                appNameIdInput.val(clientId);
                
                // Name کو visible field میں ڈالیں
                appNameInput.val(response.app_name).removeClass('is-invalid');
                appNameError.hide();
            } else {
                appNameInput.val('Not Found').addClass('is-invalid');
                appNameIdInput.val('');
                appNameError.text('App name not found for this client').show();
            }
            
            // Handle Segments
            if (response && response.segments && Array.isArray(response.segments) && response.segments.length > 0) {
                let options = '';
                $.each(response.segments, function(key, item) {
                    options += '<option value="' + item.id + '">' + item.name + '</option>';
                });
                segmentSelect.html(options).trigger('change');
                segmentError.hide();
                
                if (typeof toastr !== 'undefined') {
                    toastr.success('Client data loaded successfully!');
                }
            } else {
                segmentSelect.html('<option disabled>No segments available</option>').trigger('change');
                segmentError.text('No segments found for this client').show();
                
                if (typeof toastr !== 'undefined') {
                    toastr.warning('No segments available for this client');
                }
            }
        },
        error: function(xhr, status, error) {
            appNameInput.val('Error').addClass('is-invalid');
            appNameIdInput.val('');
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
    let repeater = $('#client_repeater');
    
    // Clear existing rows
    repeater.empty();
    
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
                        <input type="hidden" name="clients[${i}][app_name_id]" class="form-control app-name-id-input">
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
    }
    
    // Initialize Select2 on ALL new dropdowns
    $('.client-select').each(function(index) {
        initClientSelect2($(this));
    });
    
    $('.segment-select').each(function(index) {
        initSegmentSelect2($(this));
    });
}

// Document ready
$(document).ready(function() {
    // Store client options HTML
    clientOptionsHtml = `
        <option value="">Select Client</option>
        @foreach (\App\Models\Client::all() as $item)
            <option value="{{ $item->id }}" data-app-name="{{ $item->app_name ?? '' }}">{{ $item->name }}</option>
        @endforeach
    `;
});

// Number input change handler
$(document).on('input change keyup', '#num_clients', function() {
    let numClients = parseInt($(this).val());
    
    // If empty, clear repeater
    if ($(this).val() === '' || isNaN(numClients)) {
        $('#client_repeater').empty();
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

function multiples_category() {
    var category_ids_all = $('#categories').val();

    console.log("Selected category IDs:", category_ids_all);

    if (!category_ids_all || category_ids_all.length === 0) {
        alert("Please select at least one category!");
        return;
    }

    $.ajax({
        url: "{{ route('admin.Voucher.getSubcategories') }}",
        type: "GET",
        data: { category_ids_all: category_ids_all },
        traditional: false,
        dataType: "json",
        success: function(response) {
            console.log("Subcategories Response:", response);

            if (!Array.isArray(response) || response.length === 0) {
                $('#sub_categories_game').html('<option disabled>No subcategories found</option>');
                return;
            }

            // Build option list dynamically
            let options = '';
            response.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            // Put options in the select box
            $('#sub_categories_game').html(options);
            $('#sub_categories_game').trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}

function multiples_category_by_store_id() {
    var store_id = $('#store_id').val();

    console.log("Selected Store IDs:", store_id);

    if (!store_id || store_id.length === 0) {
        alert("Please select at least one Store!");
        return;
    }

    $.ajax({
        url: "{{ route('admin.Voucher.getCategoty') }}",
        type: "GET",
        data: { store_id: store_id },
        traditional: false,
        dataType: "json",
        success: function(response) {
            console.log("Categoy Response:", response);

            if (!Array.isArray(response) || response.length === 0) {
                $('#sub_categories_game').html('<option disabled>No Categoy found</option>');
                return;
            }

            // Build option list dynamically
            let options = '';
            response.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            // Put options in the select box
            $('#sub_categories_game').html(options);
            $('#sub_categories_game').trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}
</script>