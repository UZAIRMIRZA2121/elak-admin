@extends('layouts.admin.app')

@section('title',"Holiday & Occasion  List")

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet">

<style>
    .preview-panel {
        position: sticky;
        top: 20px;
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .preview-header {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }

    .preview-section {
        margin-bottom: 20px;
    }

    .preview-label {
        font-weight: 600;
        color: #007bff;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .preview-value {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        padding-left: 10px;
    }

    .preview-badge {
        display: inline-block;
        padding: 4px 10px;
        background: #007bff;
        color: white;
        border-radius: 12px;
        font-size: 12px;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .condition-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .condition-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        cursor: pointer;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .condition-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }

    .active-badge {
        background: #007bff;
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .condition-body {
        padding-top: 15px;
    }

    .day-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .day-btn {
        padding: 8px 16px;
        border: 2px solid #ddd;
        background: white;
        color: #666;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
    }

    .day-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .holiday-checkbox {
        margin-bottom: 10px;
    }

    .holiday-checkbox input[type="checkbox"] {
        margin-right: 8px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .holiday-checkbox label {
        cursor: pointer;
        font-size: 14px;
        color: #333;
    }

    .usage-row {
        display: flex;
        gap: 15px;
        align-items: end;
        margin-bottom: 15px;
    }

    .usage-row .form-group {
        flex: 1;
        margin-bottom: 0;
    }

    .times-label {
        padding: 8px 12px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #666;
        font-size: 14px;
    }
</style>

<style>
.badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    margin: 0.25rem;
}
.badge button {
    background: none;
    border: none;
    color: inherit;
    margin-left: 0.5rem;
    cursor: pointer;
    padding: 0;
}
.badge button:hover {
    opacity: 0.8;
}
.bg_secondary_bandle{
        color: black!important;
        background: #D5DBE2!important;
}
#validityPeriodTags{
    color: white!important;
}
</style>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/condition.png')}}" class="w--26" alt="">
                </span>
                <span>
                   Manage Settings
                </span>
            </h1>
        </div>
        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <form action="{{route('admin.VoucherSetting.store')}}" method="post" id="conditionsForm">
                        @csrf
                        <div class="row">

                            <!-- Holidays & Occasions Section -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Holidays & Occasions</h5>
                                        <small class="text-muted">Add and remove holidays available in conditions</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Arabic Name</label>
                                                <input type="text" class="form-control" id="holiday_name_ar"  name="holiday_name_ar" placeholder="Example: Ramadan">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">English Name</label>
                                                <input type="text" class="form-control" name="holiday_name_en" id="holiday_name_en" placeholder="Example: Ramadan">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Start Date (Optional)</label>
                                                <input type="date" name="holiday_start_date" class="form-control" id="holiday_start_date">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">End Date (Optional)</label>
                                                <input type="date" name="holiday_end_date" class="form-control" id="holiday_end_date">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary"  id="addHolidayBtn">
                                                    <i class="fas fa-plus"></i> Add Holiday
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Current Holidays (<span id="holidayCount">0</span>)</label>
                                            <div id="holidayTags" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- General Restrictions Section -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>General Restrictions</h5>
                                        <small class="text-muted">Add and remove available general restrictions</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Arabic Name</label>
                                                <input type="text" class="form-control" name="restriction_name_ar" id="restriction_name_ar" placeholder="Example: Cannot combine with other offers">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">English Name</label>
                                                <input type="text" class="form-control" name="restriction_name_en" id="restriction_name_en" placeholder="Example: Cannot combine with other offers">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary" name="addRestrictionBtn" id="addRestrictionBtn">
                                                    <i class="fas fa-plus"></i> Add Restriction
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Current Restrictions (<span id="restrictionCount">0</span>)</label>
                                            <div id="restrictionTags" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Blackout Dates Section -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Custom Blackout Dates</h5>
                                        <small class="text-muted">Add and remove specific dates to exclude</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Date</label>
                                                <input type="date" class="form-control" name="blackout_date" id="blackout_date">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Description</label>
                                                <input type="text" class="form-control" name="blackout_description" id="blackout_description" placeholder="Example: Company Anniversary">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary" name="addBlackoutBtn" id="addBlackoutBtn">
                                                    <i class="fas fa-plus"></i> Add Blackout Date
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Current Blackout Dates (<span id="blackoutCount">0</span>)</label>
                                            <div id="blackoutTags" class="d-flex flex-wrap gap-2">
                                                <p class="text-muted" id="noBlackoutMsg">No blackout dates. Add a new date above.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Age Restrictions Section -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Age Restrictions</h5>
                                        <small class="text-muted">Add and remove age restrictions available in conditions</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Arabic Name</label>
                                                <input type="text" class="form-control" name="age_restriction_name_ar" id="age_restriction_name_ar" placeholder="Example: 18+ Only">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">English Name</label>
                                                <input type="text" class="form-control" name="age_restriction_name_en" id="age_restriction_name_en" placeholder="Example: 18+ Only">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary" name="addAgeRestrictionBtn" id="addAgeRestrictionBtn">
                                                    <i class="fas fa-plus"></i> Add Age Restriction
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Current Age Restrictions (<span id="ageRestrictionCount">0</span>)</label>
                                            <div id="ageRestrictionTags" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group Size Requirements Section -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Group Size Requirements</h5>
                                        <small class="text-muted">Add and remove group size requirements available in conditions</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Arabic Name</label>
                                                <input type="text" class="form-control" name="group_size_name_ar" id="group_size_name_ar" placeholder="Example: Minimum 4 people">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">English Name</label>
                                                <input type="text" class="form-control" name="group_size_name_en" id="group_size_name_en" placeholder="Example: Minimum 4 people">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary" name="addGroupSizeBtn" id="addGroupSizeBtn">
                                                    <i class="fas fa-plus"></i> Add Group Size Requirement
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Current Group Size Requirements (<span id="groupSizeCount">0</span>)</label>
                                            <div id="groupSizeTags" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Periods Section -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Usage Periods</h5>
                                        <small class="text-muted">Add and remove usage periods available in conditions</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Arabic Name</label>
                                                <input type="text" class="form-control" name="usage_period_name_ar" id="usage_period_name_ar" placeholder="Example: Per Day">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">English Name</label>
                                                <input type="text" class="form-control" name="usage_period_name_en" id="usage_period_name_en" placeholder="Example: Per Day">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary" name="addUsagePeriodBtn" id="addUsagePeriodBtn">
                                                    <i class="fas fa-plus"></i> Add Usage Period
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Current Usage Periods (<span id="usagePeriodCount">0</span>)</label>
                                            <div id="usagePeriodTags" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Offer Validity Periods Section -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Offer Validity Periods</h5>
                                        <small class="text-muted">Add and remove offer validity periods after purchase</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Arabic Name</label>
                                                <input type="text" class="form-control" name="validity_period_name_ar" id="validity_period_name_ar" placeholder="Example: 1 Month">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">English Name</label>
                                                <input type="text" class="form-control" name="validity_period_name_en" id="validity_period_name_en" placeholder="Example: 1 Month">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary" name="addValidityPeriodBtn" id="addValidityPeriodBtn">
                                                    <i class="fas fa-plus"></i> Add Validity Period
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label text-muted">Current Validity Periods (<span id="validityPeriodCount">0</span>)</label>
                                            <div id="validityPeriodTags" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="btn--container justify-content-end mt-5">
                    <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                </div>

        </div>

    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/segments-index.js"></script>

 <script src="{{asset('public/assets/admin')}}/js/view-pages/client-side-index.js"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
    $(function () {
        $('#type').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: $('#type').data('placeholder'),
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>

<script>
$(document).ready(function() {
    // Data storage
    let conditionsData = {
        holidays: [],
        restrictions: [],
        blackoutDates: [],
        ageRestrictions: [],
        groupSizeRequirements: [],
        usagePeriods: [],
        validityPeriods: []
    };

    // Load existing data from server on page load
    loadAllData();

    function loadAllData() {
        $.ajax({
            url: '/admin/VoucherSetting/conditions-get-all',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    conditionsData = response.data;
                    renderAllTags();
                }
            },
            error: function(xhr) {
                console.error('Error loading data:', xhr);
                toastr.error('Error loading data');
            }
        });
    }

    // Render all tags
    function renderAllTags() {
        renderHolidays();
        renderRestrictions();
        renderBlackoutDates();
        renderAgeRestrictions();
        renderGroupSizes();
        renderUsagePeriods();
        renderValidityPeriods();
    }

    // Generate unique ID
    function generateId() {
        return Date.now() + Math.random().toString(36).substr(2, 9);
    }

    // Create tag HTML with X button
    function createTag(text, id) {
        return `
            <span class="badge bg_secondary_bandle">
                ${text}
                <button type="button" class="delete-tag" data-id="${id}">
                    ×
                </button>
            </span>
        `;
    }

    // ============= HOLIDAYS =============
    $('#addHolidayBtn').click(function() {
        const nameAr = $('#holiday_name_ar').val().trim();
        const nameEn = $('#holiday_name_en').val().trim();
        const startDate = $('#holiday_start_date').val();
        const endDate = $('#holiday_end_date').val();

        if (!nameAr || !nameEn) {
            toastr.error('Please fill Arabic and English names');
            return;
        }

        const holiday = {
            name_ar: nameAr,
            name_en: nameEn,
            start_date: startDate,
            end_date: endDate,
            type: 'holiday'
        };

        saveCondition(holiday, function(savedData) {
            holiday.id = savedData.id;
            conditionsData.holidays.push(holiday);
            renderHolidays();
            clearHolidayForm();
        });
    });

    function renderHolidays() {
        const container = $('#holidayTags');
        container.empty();

        if (conditionsData.holidays.length === 0) {
            container.html('<p class="text-muted">No holidays added yet.</p>');
        } else {
            conditionsData.holidays.forEach(function(holiday) {
                const tag = createTag(holiday.name_en, holiday.id);
                container.append(tag);
            });
        }

        $('#holidayCount').text(conditionsData.holidays.length);
    }

    function clearHolidayForm() {
        $('#holiday_name_ar, #holiday_name_en, #holiday_start_date, #holiday_end_date').val('');
    }

    // Delete holiday
    $(document).on('click', '#holidayTags .delete-tag', function() {
        const id = $(this).data('id');
        deleteCondition(id, 'holiday', function() {
            conditionsData.holidays = conditionsData.holidays.filter(h => h.id != id);
            renderHolidays();
        });
    });

    // ============= RESTRICTIONS =============
    $('#addRestrictionBtn').click(function() {
        const nameAr = $('#restriction_name_ar').val().trim();
        const nameEn = $('#restriction_name_en').val().trim();

        if (!nameAr || !nameEn) {
            toastr.error('Please fill Arabic and English names');
            return;
        }

        const restriction = {
            name_ar: nameAr,
            name_en: nameEn,
            type: 'restriction'
        };

        saveCondition(restriction, function(savedData) {
            restriction.id = savedData.id;
            conditionsData.restrictions.push(restriction);
            renderRestrictions();
            $('#restriction_name_ar, #restriction_name_en').val('');
        });
    });

    function renderRestrictions() {
        const container = $('#restrictionTags');
        container.empty();

        if (conditionsData.restrictions.length === 0) {
            container.html('<p class="text-muted">No restrictions added yet.</p>');
        } else {
            conditionsData.restrictions.forEach(function(restriction) {
                const tag = createTag(restriction.name_en, restriction.id);
                container.append(tag);
            });
        }

        $('#restrictionCount').text(conditionsData.restrictions.length);
    }

    $(document).on('click', '#restrictionTags .delete-tag', function() {
        const id = $(this).data('id');
        deleteCondition(id, 'restriction', function() {
            conditionsData.restrictions = conditionsData.restrictions.filter(r => r.id != id);
            renderRestrictions();
        });
    });

    // ============= BLACKOUT DATES =============
    $('#addBlackoutBtn').click(function() {
        const date = $('#blackout_date').val();
        const description = $('#blackout_description').val().trim();

        if (!date || !description) {
            toastr.error('Please fill both date and description');
            return;
        }

        const blackout = {
            date: date,
            description: description,
            type: 'blackout_date'
        };

        saveCondition(blackout, function(savedData) {
            blackout.id = savedData.id;
            conditionsData.blackoutDates.push(blackout);
            renderBlackoutDates();
            $('#blackout_date, #blackout_description').val('');
        });
    });

    function renderBlackoutDates() {
        const container = $('#blackoutTags');
        container.empty();

        if (conditionsData.blackoutDates.length === 0) {
            container.html('<p class="text-muted">No blackout dates added yet.</p>');
        } else {
            conditionsData.blackoutDates.forEach(function(blackout) {
                const tag = createTag(`${blackout.date} - ${blackout.description}`, blackout.id);
                container.append(tag);
            });
        }

        $('#blackoutCount').text(conditionsData.blackoutDates.length);
    }

    $(document).on('click', '#blackoutTags .delete-tag', function() {
        const id = $(this).data('id');
        deleteCondition(id, 'blackout_date', function() {
            conditionsData.blackoutDates = conditionsData.blackoutDates.filter(b => b.id != id);
            renderBlackoutDates();
        });
    });

    // ============= AGE RESTRICTIONS =============
    $('#addAgeRestrictionBtn').click(function() {
        const nameAr = $('#age_restriction_name_ar').val().trim();
        const nameEn = $('#age_restriction_name_en').val().trim();

        if (!nameAr || !nameEn) {
            toastr.error('Please fill Arabic and English names');
            return;
        }

        const ageRestriction = {
            name_ar: nameAr,
            name_en: nameEn,
            type: 'age_restriction'
        };

        saveCondition(ageRestriction, function(savedData) {
            ageRestriction.id = savedData.id;
            conditionsData.ageRestrictions.push(ageRestriction);
            renderAgeRestrictions();
            $('#age_restriction_name_ar, #age_restriction_name_en').val('');
        });
    });

    function renderAgeRestrictions() {
        const container = $('#ageRestrictionTags');
        container.empty();

        if (conditionsData.ageRestrictions.length === 0) {
            container.html('<p class="text-muted">No age restrictions added yet.</p>');
        } else {
            conditionsData.ageRestrictions.forEach(function(age) {
                const tag = createTag(age.name_en, age.id);
                container.append(tag);
            });
        }

        $('#ageRestrictionCount').text(conditionsData.ageRestrictions.length);
    }

    $(document).on('click', '#ageRestrictionTags .delete-tag', function() {
        const id = $(this).data('id');
        deleteCondition(id, 'age_restriction', function() {
            conditionsData.ageRestrictions = conditionsData.ageRestrictions.filter(a => a.id != id);
            renderAgeRestrictions();
        });
    });

    // ============= GROUP SIZE =============
    $('#addGroupSizeBtn').click(function() {
        const nameAr = $('#group_size_name_ar').val().trim();
        const nameEn = $('#group_size_name_en').val().trim();

        if (!nameAr || !nameEn) {
            toastr.error('Please fill Arabic and English names');
            return;
        }

        const groupSize = {
            name_ar: nameAr,
            name_en: nameEn,
            type: 'group_size_requirement'
        };

        saveCondition(groupSize, function(savedData) {
            groupSize.id = savedData.id;
            conditionsData.groupSizeRequirements.push(groupSize);
            renderGroupSizes();
            $('#group_size_name_ar, #group_size_name_en').val('');
        });
    });

    function renderGroupSizes() {
        const container = $('#groupSizeTags');
        container.empty();

        if (conditionsData.groupSizeRequirements.length === 0) {
            container.html('<p class="text-muted">No group size requirements added yet.</p>');
        } else {
            conditionsData.groupSizeRequirements.forEach(function(group) {
                const tag = createTag(group.name_en, group.id);
                container.append(tag);
            });
        }

        $('#groupSizeCount').text(conditionsData.groupSizeRequirements.length);
    }

    $(document).on('click', '#groupSizeTags .delete-tag', function() {
        const id = $(this).data('id');
        deleteCondition(id, 'group_size_requirement', function() {
            conditionsData.groupSizeRequirements = conditionsData.groupSizeRequirements.filter(g => g.id != id);
            renderGroupSizes();
        });
    });

    // ============= USAGE PERIODS =============
    $('#addUsagePeriodBtn').click(function() {
        const nameAr = $('#usage_period_name_ar').val().trim();
        const nameEn = $('#usage_period_name_en').val().trim();

        if (!nameAr || !nameEn) {
            toastr.error('Please fill Arabic and English names');
            return;
        }

        const usagePeriod = {
            name_ar: nameAr,
            name_en: nameEn,
            type: 'usage_period'
        };

        saveCondition(usagePeriod, function(savedData) {
            usagePeriod.id = savedData.id;
            conditionsData.usagePeriods.push(usagePeriod);
            renderUsagePeriods();
            $('#usage_period_name_ar, #usage_period_name_en').val('');
        });
    });

    function renderUsagePeriods() {
        const container = $('#usagePeriodTags');
        container.empty();

        if (conditionsData.usagePeriods.length === 0) {
            container.html('<p class="text-muted">No usage periods added yet.</p>');
        } else {
            conditionsData.usagePeriods.forEach(function(period) {
                const tag = createTag(period.name_en, period.id);
                container.append(tag);
            });
        }

        $('#usagePeriodCount').text(conditionsData.usagePeriods.length);
    }

    $(document).on('click', '#usagePeriodTags .delete-tag', function() {
        const id = $(this).data('id');
        deleteCondition(id, 'usage_period', function() {
            conditionsData.usagePeriods = conditionsData.usagePeriods.filter(u => u.id != id);
            renderUsagePeriods();
        });
    });

    // ============= VALIDITY PERIODS =============
    $('#addValidityPeriodBtn').click(function() {
        const nameAr = $('#validity_period_name_ar').val().trim();
        const nameEn = $('#validity_period_name_en').val().trim();

        if (!nameAr || !nameEn) {
            toastr.error('Please fill Arabic and English names');
            return;
        }

        const validityPeriod = {
            name_ar: nameAr,
            name_en: nameEn,
            type: 'validity_period'
        };

        saveCondition(validityPeriod, function(savedData) {
            validityPeriod.id = savedData.id;
            conditionsData.validityPeriods.push(validityPeriod);
            renderValidityPeriods();
            $('#validity_period_name_ar, #validity_period_name_en').val('');
        });
    });

    function renderValidityPeriods() {
        const container = $('#validityPeriodTags');
        container.empty();

        if (conditionsData.validityPeriods.length === 0) {
            container.html('<p class="text-muted">No validity periods added yet.</p>');
        } else {
            conditionsData.validityPeriods.forEach(function(period) {
                const tag = createTag(period.name_en, period.id);
                container.append(tag);
            });
        }

        $('#validityPeriodCount').text(conditionsData.validityPeriods.length);
    }

    $(document).on('click', '#validityPeriodTags .delete-tag', function() {
        const id = $(this).data('id');
        deleteCondition(id, 'validity_period', function() {
            conditionsData.validityPeriods = conditionsData.validityPeriods.filter(v => v.id != id);
            renderValidityPeriods();
        });
    });

    // ============= AJAX FUNCTIONS =============
    function saveCondition(data, callback) {
        $.ajax({
            url: '/admin/VoucherSetting/conditions-store',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Added successfully');
                    if (callback) callback(response.data);
                }
            },
                error: function(xhr) {
                // Check if validation error
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    // Get all validation errors
                    const errors = xhr.responseJSON.errors;

                    // Show each error message
                    Object.keys(errors).forEach(function(field) {
                        errors[field].forEach(function(message) {
                            toastr.error(message);
                        });
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    // Show general error message
                    toastr.error(xhr.responseJSON.message);
                } else {
                    // Fallback error message
                    toastr.error('Error adding item');
                }

                console.error(xhr);
            }
        });
    }

    function deleteCondition(id, type, callback) {
        if (!confirm('Are you sure you want to delete this?')) {
            return;
        }

        $.ajax({
            url: '/admin/VoucherSetting/conditions-delete',
            method: 'POST',
            data: { id: id, type: type },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Deleted successfully');
                    if (callback) callback();
                }
            },
            error: function(xhr) {
                toastr.error('Error deleting item');
                console.error(xhr);
            }
        });
    }
});
</script>
@endpush
