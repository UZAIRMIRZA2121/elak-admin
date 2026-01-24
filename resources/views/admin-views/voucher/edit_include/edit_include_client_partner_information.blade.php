<div class="section-card rounded p-4 mb-4" id="basic_info_main">
    <div class="form-group mb-0 p-2">
        <label class="input-label" for="num_clients">{{ translate('Client Information') }}
            <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.')}}"> *</span>
        </label>
    </div>
    
    @php
        $existingClients = json_decode($product->clients_section ?? '[]', true);
        if (empty($existingClients)) {
            $existingClients = [['client_id' => '', 'app_name' => '', 'app_name_id' => '', 'segment' => []]];
        }
    @endphp
    
    <div id="client_repeater">
        @foreach($existingClients as $index => $clientData)
            <div class="row item-row mb-3" data-row-index="{{ $index }}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="input-label">Client Name {{ $index + 1 }}</label>
                        <select name="clients[{{ $index }}][client_id]" class="form-control client-select" data-row="{{ $index }}">
                            <option value="">Select Client</option>
                            @foreach(\App\Models\Client::all() as $client)
                                <option value="{{ $client->id }}" {{ ($clientData['client_id'] ?? '') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">Client App Name</label>
                        <input type="hidden" name="clients[{{ $index }}][app_name_id]" class="form-control app-name-id-input" value="{{ $clientData['app_name_id'] ?? '' }}">
                        <input type="text" name="clients[{{ $index }}][app_name]" class="form-control app-name-input" placeholder="Client App Name" value="{{ $clientData['app_name'] ?? '' }}" readonly>
                        <small class="text-danger app-name-error" style="display:none;"></small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="input-label">Segment</label>
                        <select name="clients[{{ $index }}][segment][]" class="form-control segment-select" data-placeholder="Select Segment" multiple>
                            <option disabled>Select client first</option>
                            {{-- Segments will be loaded via AJAX --}}
                        </select>
                        <small class="text-danger segment-error" style="display:none;"></small>
                    </div>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-client-row" title="Remove" style="{{ $index === 0 ? 'visibility: hidden;' : '' }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
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
                    <select name="store_id" id="store_id" class="form-control">
                        <option value="">{{ translate('messages.select_store') }}</option>
                        @foreach($stores ?? [] as $store)
                            <option value="{{ $store->id }}" {{ ($product->store_id ?? '') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
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
                    @php
                        $categoryIds = json_decode($product->category_ids ?? '[]', true);
                        
                        // Extract only IDs from objects like {"id":"2","position":1}
                        $allCategoryIds = collect($categoryIds)->pluck('id')->map(function($id) {
                            return is_numeric($id) ? (int)$id : $id;
                        })->toArray();
                        
                        // Filter to only include parent categories (parent_id = 0)
                        // This excludes subcategories that might be stored in category_ids
                        $selectedCategoryIds = App\Models\Category::whereIn('id', $allCategoryIds)
                            ->where('parent_id', 0)
                            ->pluck('id')
                            ->toArray();
                    @endphp
                    <select name="categories[]" id="categories" class="form-control" multiple>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCategoryIds) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Sub Category Dropdown -->
            <div class="col-sm-6 col-lg-4">
                <div class="form-group mb-0">
                    <label class="input-label" for="sub_categories_game">{{ translate('messages.sub_category') }}</label>
                    <select name="sub_categories_game[]" class="form-control" id="sub_categories_game" multiple>
                    </select>
                </div>
            </div>

            <!-- Branches Dropdown -->
            <div class="col-sm-12">
                <div class="form-group mb-0">
                    <label class="input-label" for="sub_branch_id">{{ translate('Branches') }}
                        <span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Branches') }}"></span>
                    </label>
                    <select name="sub_branch_id[]" id="sub-branch" class="form-control" multiple>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// ==================== GLOBAL VARIABLES ====================
let allClientsData = [];
let selectedClientIds = new Set();
let clientRowIndex = 0;
let isInitializing = true; // Flag to prevent clearing subcategories during page load

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
        
        let newOptions = getAvailableClientsHtml(currentValue);
        clientSelect.html(newOptions);
        
        if (currentValue) {
            clientSelect.val(currentValue);
        }
        
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
    
    element.off('select2:select select2:clear change').on('select2:select', function(e) {
        let newValue = $(this).val();
        let oldValue = $(this).data('previous-value');
        
        if (oldValue) {
            selectedClientIds.delete(oldValue);
        }
        
        if (newValue) {
            selectedClientIds.add(newValue);
            $(this).data('previous-value', newValue);
        }
        
        updateAllClientDropdowns();
        handleClientChange($(this));
        checkAndAddNextRow($(this));
        
    }).on('select2:clear', function(e) {
        let oldValue = $(this).data('previous-value');
        
        if (oldValue) {
            selectedClientIds.delete(oldValue);
            $(this).removeData('previous-value');
        }
        
        let currentRow = $(this).closest('.item-row');
        currentRow.find('.app-name-input').val('');
        currentRow.find('.app-name-id-input').val('');
        currentRow.find('.segment-select').html('<option disabled>Select client first</option>').trigger('change');
        
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
    
    let availableClients = allClientsData.filter(client => !selectedClientIds.has(client.id));
    
    if (nextRow.length === 0 && availableClients.length > 0) {
        addClientRow();
    }
}

// ==================== AJAX HANDLER - CLIENT CHANGE ====================
function handleClientChange(selectElement, savedSegments = null) {
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
                segmentSelect.html(options);
                
                // Pre-select saved segments if editing
                if (savedSegments && Array.isArray(savedSegments) && savedSegments.length > 0) {
                    segmentSelect.val(savedSegments);
                }
                
                segmentSelect.trigger('change');
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
    
    let newRow = repeater.find('.item-row').last();
    initClientSelect2(newRow.find('.client-select'));
    initSegmentSelect2(newRow.find('.segment-select'));
    
    return newRow;
}

// ==================== REMOVE CLIENT ROW ====================
$(document).on('click', '.remove-client-row', function() {
    let row = $(this).closest('.item-row');
    let totalRows = $('.item-row').length;
    
    if (totalRows <= 1) {
        if (typeof toastr !== 'undefined') {
            toastr.warning('At least one client is required!');
        } else {
            alert('At least one client is required!');
        }
        return;
    }
    
    let clientSelect = row.find('.client-select');
    let clientId = clientSelect.val();
    if (clientId) {
        selectedClientIds.delete(clientId);
    }
    
    row.fadeOut(300, function() {
        $(this).remove();
        updateRowLabels();
        updateAllClientDropdowns();
    });
});

// ==================== UPDATE ROW LABELS ====================
function updateRowLabels() {
    $('.item-row').each(function(index) {
        $(this).attr('data-row-index', index);
        $(this).find('.input-label').first().text('Client Name ' + (index + 1));
        
        if (index === 0) {
            $(this).find('.remove-client-row').css('visibility', 'hidden');
        } else {
            $(this).find('.remove-client-row').css('visibility', 'visible');
        }
    });
}

// ==================== LOAD SUBCATEGORIES ====================
function multiples_category() {
    var category_ids_all = $('#categories').val();
    
    console.log("🔍 Selected categories:", category_ids_all);

    if (!category_ids_all || category_ids_all.length === 0) {
        console.log("⚠️ No categories selected");
        $('#sub_categories_game').html('<option disabled>Select category first</option>').trigger('change');
        return;
    }

    console.log("🚀 Calling subcategories AJAX...");

    $.ajax({
        url: "{{ route('admin.Voucher.getSubcategories') }}",
        type: "GET",
        data: { category_ids_all: category_ids_all },
        dataType: "json",
        beforeSend: function() {
            console.log("⏳ AJAX started");
            $('#sub_categories_game').html('<option disabled>Loading...</option>').trigger('change');
        },
        success: function(response) {
            console.log("✅ AJAX response:", response);
            
            if (!Array.isArray(response) || response.length === 0) {
                $('#sub_categories_game').html('<option disabled>No subcategories found</option>').trigger('change');
                return;
            }

            let options = '';
            response.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#sub_categories_game').html(options).trigger('change');
            
            if (typeof toastr !== 'undefined') {
                toastr.success('Subcategories loaded successfully!');
            }
        },
        error: function(xhr, status, error) {
            console.error("❌ AJAX Error:", error);
            console.error("Response:", xhr.responseText);
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

    console.log("🏪 Loading categories for store:", storeId);

    $.ajax({
        url: "{{ route('admin.Voucher.getCategoty') }}",
        type: "GET",
        data: { store_id: storeId },
        dataType: "json",
        beforeSend: function() {
            $('#categories').html('<option disabled>Loading...</option>').trigger('change');
            $('#sub_categories_game').html('').trigger('change');
        },
        success: function(response) {
            console.log("✅ Categories loaded:", response);
            
            if (!Array.isArray(response) || response.length === 0) {
                $('#categories').html('<option disabled>No categories found</option>').trigger('change');
                return;
            }

            let options = '';
            response.forEach(function(item) {
                options += `<option value="${item.id}">${item.name}</option>`;
            });

            $('#categories').html(options).trigger('change');
            
            if (typeof toastr !== 'undefined') {
                toastr.success('Categories loaded successfully!');
            }
        },
        error: function(xhr, status, error) {
            console.error("❌ Categories AJAX Error:", error);
            $('#categories').html('<option disabled>Error loading categories</option>').trigger('change');
            
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load categories!');
            }
        }
    });
}

// ==================== DOCUMENT READY ====================
$(document).ready(function() {
    console.log("🚀 Document Ready - Initializing...");
    
    // Initialize client data
    initializeClientData();
    
    // Client rows are already rendered in HTML, just set the counter
    @php
        $existingClientsJS = json_decode($product->clients_section ?? '[]', true);
        $clientCount = count($existingClientsJS);
        if ($clientCount == 0) $clientCount = 1;
    @endphp
    clientRowIndex = {{ $clientCount }};
    console.log('Client rows already rendered in HTML. ClientRowIndex set to:', clientRowIndex);
    
    // Store existing client data for pre-selection
    let existingClientsData = @json($existingClientsJS);
    console.log('Existing clients data:', existingClientsData);
    
    // Initialize Select2 for all existing client rows
    $('.item-row').each(function() {
        initClientSelect2($(this).find('.client-select'));
        initSegmentSelect2($(this).find('.segment-select'));
    });
    
    // Trigger client select change for existing rows to load segments
    setTimeout(function() {
        $('.client-select').each(function(index) {
            let clientId = $(this).val();
            if (clientId) {
                console.log('Loading segments for client:', clientId);
                let savedSegments = existingClientsData[index]?.segment || [];
                console.log('Saved segments for row', index, ':', savedSegments);
                
                // Track this client as selected
                selectedClientIds.add(clientId);
                $(this).data('previous-value', clientId);
                
                // Load segments with pre-selection
                handleClientChange($(this), savedSegments);
            }
        });
    }, 500);
    
    // Load saved subcategories and branches when editing
    @php
        $categoryIds = json_decode($product->category_ids ?? '[]', true);
        $selectedCategoryIds = collect($categoryIds)->pluck('id')->toArray();
        
        // Get saved sub category IDs from the dedicated field
        $subCategoryIds = json_decode($product->sub_category_ids ?? '[]', true);
        if (!is_array($subCategoryIds)) {
            $subCategoryIds = [];
        }
        
        // ALSO check if any subcategories were stored in category_ids (parent_id > 0)
        $subCategoriesFromCategoryIds = App\Models\Category::whereIn('id', $selectedCategoryIds)
            ->where('parent_id', '>', 0)
            ->pluck('id')
            ->toArray();
        
        // Merge both sources of subcategory IDs
        $subCategoryIds = array_merge($subCategoryIds, $subCategoriesFromCategoryIds);
        $subCategoryIds = array_unique($subCategoryIds);
        
        // Get saved branch IDs
        $branchIds = json_decode($product->branch_ids ?? '[]', true);
        if (!is_array($branchIds)) {
            $branchIds = [];
        }
    @endphp
    
    let savedSubCategories = @json($subCategoryIds);
    let savedBranches = @json($branchIds);
    let savedStoreId = '{{ $product->store_id ?? '' }}';
    
    console.log('Saved subcategories:', savedSubCategories);
    console.log('Saved branches:', savedBranches);
    console.log('Saved store ID:', savedStoreId);
    
    // Load subcategories and branches after a delay to ensure categories are loaded
    setTimeout(function() {
        // Subcategories will be loaded AFTER branches are loaded (see loadBranchesWithSelection)
        // This prevents timing conflicts and ensures correct pre-selection
        
        // Load branches if store exists
        if (savedStoreId) {
            console.log('Loading branches for store:', savedStoreId);
            loadBranchesWithSelection(savedStoreId, savedBranches);
        }
        
        // Set flag to false after all initialization is complete
        setTimeout(function() {
            isInitializing = false;
            console.log('✅ Initialization complete - subcategories can now be reloaded on category change');
        }, 1200);
    }, 800);
    
    // ✅ Small delay to ensure DOM is fully loaded
    setTimeout(function() {
        
        // ✅ Initialize Select2 for STORE (with destroy first)
        if ($('#store_id').length) {
            if ($('#store_id').hasClass('select2-hidden-accessible')) {
                $('#store_id').select2('destroy');
            }
            $('#store_id').select2({
                placeholder: "{{ translate('messages.select_store') }}",
                allowClear: true,
                width: '100%'
            });
            
            // Trigger change to update Select2 display with pre-selected value
            $('#store_id').trigger('change.select2');
            console.log("✅ Store Select2 initialized with value:", $('#store_id').val());
        }
        
        // ✅ Initialize Select2 for CATEGORIES
        if ($('#categories').length) {
            if ($('#categories').hasClass('select2-hidden-accessible')) {
                $('#categories').select2('destroy');
            }
            $('#categories').select2({
                placeholder: "{{ translate('messages.select_category') }}",
                allowClear: true,
                width: '100%'
            });
            
            // Trigger change to update Select2 display with pre-selected values
            $('#categories').trigger('change.select2');
            console.log("✅ Categories Select2 initialized with values:", $('#categories').val());
        }
        
        // ✅ Initialize Select2 for SUBCATEGORIES
        if ($('#sub_categories_game').length) {
            if ($('#sub_categories_game').hasClass('select2-hidden-accessible')) {
                $('#sub_categories_game').select2('destroy');
            }
            $('#sub_categories_game').select2({
                placeholder: "{{ translate('messages.select_sub_category') }}",
                allowClear: true,
                width: '100%'
            });
            console.log("✅ Subcategories Select2 initialized");
        }
        
        // ✅ Initialize Select2 for BRANCHES
        if ($('#sub-branch').length) {
            if ($('#sub-branch').hasClass('select2-hidden-accessible')) {
                $('#sub-branch').select2('destroy');
            }
            $('#sub-branch').select2({
                placeholder: "{{ translate('Select Branches') }}",
                allowClear: true,
                width: '100%'
            });
            console.log("✅ Branches Select2 initialized");
        }
        
    }, 200);
    
    // ✅ STORE CHANGE EVENT - FIXED (यही मुख्य बदलाव है)
    $('#store_id').on('select2:select select2:clear', function() {
        let storeId = $(this).val();
        console.log("🏪 Store changed:", storeId);
        
        if (!storeId) {
            $('#categories').html('').trigger('change');
            // $('#sub_categories_game').html('').trigger('change');
            $('#sub-branch').html('').trigger('change');
            return;
        }
        
        if (typeof findBranch == 'function') {
            findBranch(storeId);
        }
        
        // multiples_category_by_store_id(storeId);
    });
    
    // ✅ CATEGORY CHANGE EVENT
    $(document).on('change', '#categories', function(e) {
        console.log("📁 Category changed:", $(this).val());
        
        // Don't reload subcategories during initialization to preserve pre-selected values
        if (!isInitializing) {
            multiples_category();
        } else {
            console.log('⏸ Skipping subcategory reload during initialization');
        }
    });
    
    // ==================== LOAD SUBCATEGORIES WITH PRE-SELECTION ====================
    function loadSubCategoriesWithSelection(categoryIds, selectedSubCats) {
        if (!categoryIds || categoryIds.length === 0) {
            $('#sub_categories_game').html('<option disabled>Select category first</option>').trigger('change');
            return;
        }
        
        $.ajax({
            url: "{{ route('admin.Voucher.getSubcategories') }}",
            type: "GET",
            data: { category_ids_all: categoryIds },
            dataType: "json",
            beforeSend: function() {
                $('#sub_categories_game').html('<option disabled>Loading...</option>').trigger('change');
            },
            success: function(response) {
                console.log("✅ Subcategories loaded:", response);
                
                if (!Array.isArray(response) || response.length === 0) {
                    $('#sub_categories_game').html('<option disabled>No subcategories found</option>').trigger('change');
                    return;
                }

                let options = '';
                response.forEach(function(item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });

                $('#sub_categories_game').html(options);
                
                // Pre-select saved subcategories
                if (selectedSubCats && selectedSubCats.length > 0) {
                    $('#sub_categories_game').val(selectedSubCats);
                    console.log('Pre-selected subcategories:', selectedSubCats);
                }
                
                $('#sub_categories_game').trigger('change');
                
                if (typeof toastr !== 'undefined') {
                    toastr.success('Subcategories loaded successfully!');
                }
            },
            error: function(xhr, status, error) {
                console.error("❌ Subcategories AJAX Error:", error);
                $('#sub_categories_game').html('<option disabled>Error loading subcategories</option>').trigger('change');
            }
        });
    }
    
    // ==================== LOAD BRANCHES WITH PRE-SELECTION ====================
    function loadBranchesWithSelection(storeId, selectedBranches) {
        if (!storeId) {
            $('#sub-branch').html('').trigger('change');
            return;
        }
        
        // Make direct AJAX call instead of using findBranch to avoid clearing categories
        $.ajax({
            url: "{{ route('admin.Voucher.get_branches') }}",
            type: "GET",
            data: { store_id: storeId },
            beforeSend: function() {
                $('#sub-branch').html('<option disabled>Loading branches...</option>').trigger('change');
            },
            success: function(response) {
                console.log('✅ Branches AJAX response:', response);
                
                // Load branches
                let branchOptions = '';
                if (response.branches && response.branches.length > 0) {
                    $.each(response.branches, function(key, branch) {
                        branchOptions += '<option value="' + branch.id + '">' + branch.name + ' (' + branch.type + ')</option>';
                    });
                    $('#sub-branch').html(branchOptions);
                    
                    // Pre-select saved branches
                    if (selectedBranches && selectedBranches.length > 0) {
                        $('#sub-branch').val(selectedBranches);
                        console.log('✅ Pre-selected branches:', selectedBranches);
                    }
                    
                    $('#sub-branch').trigger('change');
                } else {
                    $('#sub-branch').html('<option disabled>No branches available</option>').trigger('change');
                }
                
                // Also load categories from this response
                if (response.categories && response.categories.length > 0) {
                    let categoryOptions = '';
                    $.each(response.categories, function(key, category) {
                        categoryOptions += '<option value="' + category.id + '">' + category.name + '</option>';
                    });
                    $('#categories').html(categoryOptions);
                    
                    // Pre-select saved categories
                    @php
                        $categoryIds = json_decode($product->category_ids ?? '[]', true);
                        $allCategoryIds = collect($categoryIds)->pluck('id')->toArray();
                        $selectedCategoryIds = App\Models\Category::whereIn('id', $allCategoryIds)
                            ->where('parent_id', 0)
                            ->pluck('id')
                            ->toArray();
                    @endphp
                    let savedCategoryIds = @json($selectedCategoryIds);
                    if (savedCategoryIds && savedCategoryIds.length > 0) {
                        $('#categories').val(savedCategoryIds);
                        console.log('✅ Pre-selected categories from branch AJAX:', savedCategoryIds);
                    }
                    
                    // DON'T trigger change here - it will clear subcategories!
                    // Just refresh the Select2 display
                    $('#categories').trigger('change.select2');
                    
                    // Now load subcategories AFTER categories are set
                    setTimeout(function() {
                        let selectedCategories = $('#categories').val();
                        if (selectedCategories && selectedCategories.length > 0) {
                            console.log('📁 Loading subcategories after branches loaded...');
                            
                            @php
                                $subCategoryIds = json_decode($product->sub_category_ids ?? '[]', true);
                                if (!is_array($subCategoryIds)) {
                                    $subCategoryIds = [];
                                }
                                $categoryIds = json_decode($product->category_ids ?? '[]', true);
                                $selectedCategoryIds = collect($categoryIds)->pluck('id')->toArray();
                                $subCategoriesFromCategoryIds = App\Models\Category::whereIn('id', $selectedCategoryIds)
                                    ->where('parent_id', '>', 0)
                                    ->pluck('id')
                                    ->toArray();
                                $subCategoryIds = array_merge($subCategoryIds, $subCategoriesFromCategoryIds);
                                $subCategoryIds = array_unique($subCategoryIds);
                            @endphp
                            
                            let finalSubCats = @json($subCategoryIds);
                            loadSubCategoriesWithSelection(selectedCategories, finalSubCats);
                        }
                    }, 200);
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.success('Branches and categories loaded successfully!');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Branch loading error:', error);
                $('#sub-branch').html('<option disabled>Error loading branches</option>').trigger('change');
                
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to load branches!');
                }
            }
        });
    }
    
    // ✅ SUBCATEGORY CHANGE EVENT
    $(document).on('change', '#sub_categories_game', function(e) {
        console.log("📂 Subcategory changed:", $(this).val());
        
        if (typeof multples_sub_category === 'function') {
            multples_sub_category();
        }
    });
    
    console.log("✅ All events bound successfully!");
});
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