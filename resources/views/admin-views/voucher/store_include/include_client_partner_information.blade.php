<div class="section-card rounded p-4 mb-4" id="basic_info_main">
    <div class="form-group mb-0 p-2">
        <label class="input-label" for="num_clients">{{ translate('Client Information') }}
            <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
        </label>
    </div>
    
    <div id="client_repeater">
        <!-- Client rows will be generated here dynamically -->
    </div>
</div>

<!-- Partner Information Section -->
<div class="section-card rounded p-4 mb-4" id="store_category_main">
    <h3 class="h5 fw-semibold mb-4">{{ translate('Partner Information') }}</h3>
    
    <div class="col-md-12">
        <div class="row g-2 align-items-end">
            <!-- Store Dropdown -->
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="store_id">
                        {{ translate('messages.store') }}
                        <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.') }}"> *</span>
                    </label>
                    <select name="store_id" id="store_id" data-placeholder="{{ translate('messages.select_store') }}" class="js-data-example-ajax form-control">
                        <option value="">{{ translate('messages.select_store') }}</option>
                        @foreach($stores ?? [] as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Category Dropdown -->
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="categories">{{ translate('messages.category') }}
                        <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
                    </label>
                    <select name="categories[]" id="categories" data-placeholder="{{ translate('messages.select_category') }}" class="js-data-example-ajax js-select2-custom form-control js-select2-category" multiple>
                    </select>
                </div>
            </div>

            <!-- Sub Category Dropdown -->
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="sub_categories_game">{{ translate('messages.sub_category') }}</label>
                    <select name="sub_categories_game[]" class="form-control js-select2-custom js-select2-sub_category" data-placeholder="{{ translate('messages.select_sub_category') }}" id="sub_categories_game" multiple>
                    </select>
                </div>
            </div>

            <!-- Branches Dropdown -->
            <div class="col-sm-12">
                <div class="form-group mb-0">
                    <label class="input-label" for="sub_branch_id">{{ translate('Branches') }}
                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Branches') }}"></span>
                    </label>
                    <select name="sub_branch_id[]" id="sub-branch" class="form-control js-select2-custom" data-placeholder="{{ translate('Select Branches') }}" multiple>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Global Variables
let clientOptionsHtml = '';
let clientRowIndex = 0;

// Initialize Select2 for client dropdown
function initClientSelect2(element) {
    if (!element || element.length === 0) return;
    
    if (element.hasClass('select2-hidden-accessible')) {
        element.select2('destroy');
    }
    
    element.select2({
        placeholder: "Select Client",
        allowClear: true,
        width: '100%'
    });
    
    // Handle client selection to auto-add next row
    element.off('select2:select change').on('select2:select change', function(e) {
        let selectedValue = $(this).val();
        
        // First handle the client data loading
        handleClientChange($(this));
        
        // Then check if we need to add a new row
        if (selectedValue && selectedValue !== '') {
            checkAndAddNextRow($(this));
        }
    });
}

// Initialize Select2 for segment dropdown
function initSegmentSelect2(element) {
    if (!element || element.length === 0) return;
    
    if (element.hasClass('select2-hidden-accessible')) {
        element.select2('destroy');
    }
    
    element.select2({
        placeholder: "Select Segment",
        allowClear: true,
        width: '100%'
    });
}

// Check if next row should be added
function checkAndAddNextRow(selectElement) {
    let currentRow = selectElement.closest('.item-row');
    let nextRow = currentRow.next('.item-row');
    
    // If no next row exists, add one
    if (nextRow.length === 0) {
        addClientRow();
    }
}

// MAIN AJAX HANDLER - Client Change
function handleClientChange(selectElement) {
    let clientId = selectElement.val();
    let currentRow = selectElement.closest('.item-row');
    let appNameInput = currentRow.find('.app-name-input');
    let appNameIdInput = currentRow.find('.app-name-id-input');
    let segmentSelect = currentRow.find('.segment-select');
    let appNameError = currentRow.find('.app-name-error');
    let segmentError = currentRow.find('.segment-error');
    
    if (!clientId || clientId === '') {
        appNameInput.val('').removeClass('is-invalid');
        appNameIdInput.val('');
        appNameError.hide();
        segmentSelect.html('<option disabled>Select client first</option>').trigger('change');
        segmentError.hide();
        return;
    }
    
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
            if (response && response.app_name && response.app_name.trim() !== '') {
                appNameIdInput.val(clientId);
                appNameInput.val(response.app_name).removeClass('is-invalid');
                appNameError.hide();
            } else {
                appNameInput.val('Not Found').addClass('is-invalid');
                appNameIdInput.val('');
                appNameError.text('App name not found for this client').show();
            }
            
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

// Add Single Client Row
function addClientRow() {
    let repeater = $('#client_repeater');
    let currentIndex = clientRowIndex++;
    
    let newRowHtml = `
        <div class="row item-row mb-3" data-row-index="${currentIndex}">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="input-label">Client Name ${currentIndex + 1}</label>
                    <select name="clients[${currentIndex}][client_id]" class="form-control client-select" data-row="${currentIndex}">
                        ${clientOptionsHtml}
                    </select>
                </div>
            </div>
                
            <div class="col-md-3">
                <div class="form-group">
                    <label class="input-label">Client App Name</label>
                    <input type="hidden" name="clients[${currentIndex}][app_name_id]" class="form-control app-name-id-input">
                    <input type="text" name="clients[${currentIndex}][app_name]" class="form-control app-name-input" placeholder="Client App Name" readonly>
                    <small class="text-danger app-name-error" style="display:none;"></small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="input-label">Segment</label>
                    <select name="clients[${currentIndex}][segment][]" class="form-control segment-select" data-placeholder="Select Segment" multiple>
                        <option disabled>Select client first</option>
                    </select>
                    <small class="text-danger segment-error" style="display:none;"></small>
                </div>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-client-row" title="Remove" style="${currentIndex === 0 ? 'visibility: hidden;' : ''}">
                    <i class="tio-delete"></i>
                </button>
            </div>
        </div>
    `;
    
    repeater.append(newRowHtml);
    
    // Initialize Select2 on new row
    let newRow = repeater.find('.item-row').last();
    initClientSelect2(newRow.find('.client-select'));
    initSegmentSelect2(newRow.find('.segment-select'));
    
    return newRow;
}

// Remove Client Row
$(document).on('click', '.remove-client-row', function() {
    let row = $(this).closest('.item-row');
    let totalRows = $('.item-row').length;
    
    // Don't allow removing if only 1 row exists
    if (totalRows <= 1) {
        if (typeof toastr !== 'undefined') {
            toastr.warning('At least one client is required!');
        } else {
            alert('At least one client is required!');
        }
        return;
    }
    
    row.fadeOut(300, function() {
        $(this).remove();
        updateRowLabels();
    });
});

// Update Row Labels after deletion
function updateRowLabels() {
    $('.item-row').each(function(index) {
        $(this).attr('data-row-index', index);
        $(this).find('.input-label').first().text('Client Name ' + (index + 1));
        
        // Hide delete button for first row only
        if (index === 0) {
            $(this).find('.remove-client-row').css('visibility', 'hidden');
        } else {
            $(this).find('.remove-client-row').css('visibility', 'visible');
        }
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
    
    // Add first row by default
    addClientRow();
});

// Store Change - Load Categories & Branches
$(document).on('change', '#store_id', function() {
    let storeId = $(this).val();
    
    if (!storeId) {
        $('#categories').html('').trigger('change');
        $('#sub_categories_game').html('').trigger('change');
        $('#sub-branch').html('').trigger('change');
        return;
    }
    
    // Load Branches
    if (typeof findBranch === 'function') {
        findBranch(storeId);
    }
    
    // Load Categories by Store
    multiples_category_by_store_id(storeId);
});

// Category Change - Load Subcategories
$(document).on('change', '#categories', function() {
    multiples_category();
});

// Subcategory Change
$(document).on('change', '#sub_categories_game', function() {
    if (typeof multples_sub_category === 'function') {
        multples_sub_category();
    }
});

// Load Subcategories based on selected categories
function multiples_category() {
    var category_ids_all = $('#categories').val();

    if (!category_ids_all || category_ids_all.length === 0) {
        $('#sub_categories_game').html('<option disabled>Select category first</option>').trigger('change');
        return;
    }

    $.ajax({
        url: "{{ route('admin.Voucher.getSubcategories') }}",
        type: "GET",
        data: { category_ids_all: category_ids_all },
        dataType: "json",
        beforeSend: function() {
            $('#sub_categories_game').html('<option disabled>Loading...</option>').trigger('change');
        },
        success: function(response) {
            if (!Array.isArray(response) || response.length === 0) {
                $('#sub_categories_game').html('<option disabled>No subcategories found</option>').trigger('change');
                return;
            }

            let options = '';
            response.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#sub_categories_game').html(options).trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("Subcategories AJAX Error:", error);
            $('#sub_categories_game').html('<option disabled>Error loading subcategories</option>').trigger('change');
            
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load subcategories!');
            }
        }
    });
}

// Load Categories based on selected store
function multiples_category_by_store_id(storeId) {
    if (!storeId) {
        $('#categories').html('').trigger('change');
        return;
    }

    $.ajax({
        url: "{{ route('admin.Voucher.getCategoty') }}",
        type: "GET",
        data: { store_id: storeId },
        dataType: "json",
        beforeSend: function() {
            $('#categories').html('<option disabled>Loading...</option>').trigger('change');
        },
        success: function(response) {
            if (!Array.isArray(response) || response.length === 0) {
                $('#categories').html('<option disabled>No categories found</option>').trigger('change');
                return;
            }

            let options = '';
            response.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#categories').html(options).trigger('change');
        },
        error: function(xhr, status, error) {
            console.error("Categories AJAX Error:", error);
            $('#categories').html('<option disabled>Error loading categories</option>').trigger('change');
            
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load categories!');
            }
        }
    });
}
</script>

<style>
.remove-client-row {
    margin-bottom: 1rem;
}

.item-row {
    border-bottom: 1px solid #e5e5e5;
    padding-bottom: 1rem;
}

.item-row:last-child {
    border-bottom: none;
}
</style>