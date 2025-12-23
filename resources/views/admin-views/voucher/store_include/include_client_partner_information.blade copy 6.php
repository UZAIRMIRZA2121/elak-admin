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

                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
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
// ==================== GLOBAL VARIABLES ====================
let allClientsData = []; // Store all clients
let selectedClientIds = new Set(); // Track selected client IDs
let clientRowIndex = 0;

// ==================== INITIALIZE CLIENT DATA ====================
function initializeClientData() {
    allClientsData = [
        @foreach (\App\Models\Client::all() as $item)
        {
            id: '{{ $item->id }}',
            name: '{{ $item->name }}',
            app_name: '{{ $item->app_name ?? "" }}'
        },
        @endforeach
    ];
}

// ==================== GET AVAILABLE CLIENTS ====================
function getAvailableClientsHtml(currentClientId = null) {
    let optionsHtml = '<option value="">Select Client</option>';
    
    allClientsData.forEach(function(client) {
        // Show if: not selected OR is the current row's selected client
        if (!selectedClientIds.has(client.id) || client.id === currentClientId) {
            optionsHtml += `<option value="${client.id}" data-app-name="${client.app_name}">${client.name}</option>`;
        }
    });
    
    return optionsHtml;
}

// ==================== UPDATE ALL DROPDOWNS ====================
function updateAllClientDropdowns() {
    $('.item-row').each(function() {
        let row = $(this);
        let clientSelect = row.find('.client-select');
        let currentValue = clientSelect.val();
        
        // Store current value and update options
        let newOptions = getAvailableClientsHtml(currentValue);
        clientSelect.html(newOptions);
        
        // Restore selected value
        if (currentValue) {
            clientSelect.val(currentValue);
        }
        
        // Re-trigger Select2
        clientSelect.trigger('change.select2');
    });
}

// ==================== INITIALIZE SELECT2 FOR CLIENT ====================
function initClientSelect2(element) {
    if (!element || element.length === 0) return;
    
    if (element.hasClass('select2-hidden-accessible')) {
        element.select2('destroy');
    }
    
    element.select2({
        placeholder: "Select Client Name",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "No client available (all selected)";
            }
        }
    });
    
    // Handle client selection
    element.off('select2:select select2:clear change').on('select2:select', function(e) {
        let newValue = $(this).val();
        let oldValue = $(this).data('previous-value');
        
        // Remove old value from selected set
        if (oldValue) {
            selectedClientIds.delete(oldValue);
        }
        
        // Add new value to selected set
        if (newValue) {
            selectedClientIds.add(newValue);
            $(this).data('previous-value', newValue);
        }
        
        // Update all dropdowns
        updateAllClientDropdowns();
        
        // Load client data
        handleClientChange($(this));
        
        // Check if we need to add a new row
        checkAndAddNextRow($(this));
        
    }).on('select2:clear', function(e) {
        let oldValue = $(this).data('previous-value');
        
        // Remove from selected set
        if (oldValue) {
            selectedClientIds.delete(oldValue);
            $(this).removeData('previous-value');
        }
        
        // Clear the row data
        let currentRow = $(this).closest('.item-row');
        currentRow.find('.app-name-input').val('');
        currentRow.find('.app-name-id-input').val('');
        currentRow.find('.segment-select').html('<option disabled>Select client first</option>').trigger('change');
        
        // Update all dropdowns
        updateAllClientDropdowns();
    });
}

// ==================== INITIALIZE SELECT2 FOR SEGMENT ====================
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

// ==================== CHECK AND ADD NEXT ROW ====================
function checkAndAddNextRow(selectElement) {
    let currentRow = selectElement.closest('.item-row');
    let nextRow = currentRow.next('.item-row');
    
    // Check if there are still available clients
    let availableClients = allClientsData.filter(client => !selectedClientIds.has(client.id));
    
    // If no next row exists AND there are available clients, add one
    if (nextRow.length === 0 && availableClients.length > 0) {
        addClientRow();
    }
}

// ==================== AJAX HANDLER - CLIENT CHANGE ====================
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

// ==================== ADD SINGLE CLIENT ROW ====================
function addClientRow() {
    let repeater = $('#client_repeater');
    let currentIndex = clientRowIndex++;
    
    // Get available clients HTML
    let clientOptions = getAvailableClientsHtml();
    
    let newRowHtml = `
        <div class="row item-row mb-3" data-row-index="${currentIndex}">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="input-label">Client Name ${currentIndex + 1}</label>
                    <select name="clients[${currentIndex}][client_id]" class="form-control client-select" data-row="${currentIndex}">
                        ${clientOptions}
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

// ==================== REMOVE CLIENT ROW ====================
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
    
    // Get the client ID from this row and remove from selected set
    let clientSelect = row.find('.client-select');
    let clientId = clientSelect.val();
    if (clientId) {
        selectedClientIds.delete(clientId);
    }
    
    row.fadeOut(300, function() {
        $(this).remove();
        updateRowLabels();
        updateAllClientDropdowns(); // Update all dropdowns after removal
    });
});

// ==================== UPDATE ROW LABELS ====================
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

// ==================== DOCUMENT READY ====================
$(document).ready(function() {
    // Initialize client data from backend
    initializeClientData();
    
    // Add first row by default
    addClientRow();
});

// ==================== STORE CHANGE - LOAD CATEGORIES & BRANCHES ====================
$(document).on('change', '#store_id', function() {
    let storeId = $(this).val();
    // alert(storeId);
    if (!storeId) {
        $('#categories').html('').trigger('change');
        $('#sub_categories_game').html('').trigger('change');
        $('#sub-branch').html('').trigger('change');
        return;
    }
    
    // Load Branches
    if (typeof findBranch == 'function') {
        findBranch(storeId);
    }
    
    // Load Categories by Store
    multiples_category_by_store_id(storeId);
});

// ==================== CATEGORY CHANGE - LOAD SUBCATEGORIES ====================
$(document).on('change', '#categories', function() {
    multiples_category();
    alert("sdfhvj")
});

// ==================== SUBCATEGORY CHANGE ====================
$(document).on('change', '#sub_categories_game', function() {
    if (typeof multples_sub_category === 'function') {
        multples_sub_category();
    }
});

// ==================== LOAD SUBCATEGORIES ====================
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

// ==================== LOAD CATEGORIES BY STORE ====================
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

.select2-container {
    width: 100% !important;
}
</style>