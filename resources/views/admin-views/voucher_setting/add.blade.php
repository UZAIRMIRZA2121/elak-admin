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
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/condition.png')}}" class="w--26" alt="">
                </span>
                <span>
                   Add  Holiday & Occasion  
                </span>
            </h1>
        </div>
        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
        <!-- add setting  0= add , 1=update,edit -->
        <?php if($check_data == 0){ ?>
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <form action="{{route('admin.VoucherSetting.store')}}" method="post" id="conditionsForm">
                        @csrf
                         <input type="hidden" name="item_id" value="{{ request()->route('id') }}">

                        <div class="row">
                            <!-- LEFT SIDE - FORM -->
                            <div class="col-lg-7">
                                    <div class="condition-header" >
                                        <div class="condition-title">
                                            <span>Voucher Name:</span> {{ $items->name}} ,
                                            <span>Voucher Type:</span> {{ $items->voucher_ids}}
                                        </div>
                                        <div class="condition-title">
                                            <span>Add Setting</span>
                                        </div>
                                    </div>

                                <!-- TIME CONDITIONS -->
                                <div class="condition-card">
                                    <div class="condition-header" data-bs-toggle="collapse" data-bs-target="#timeConditions">
                                        <div class="condition-title">
                                            <i class="tio-time" style="font-size: 20px;"></i>
                                            <span>Time Conditions</span>
                                            <span class="active-badge" id="timeActiveCount">0 Active</span>
                                        </div>
                                        <i class="tio-chevron-down"></i>
                                    </div>
                                    <div class="collapse show condition-body" id="timeConditions">

                                        <!-- Validity Period -->
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="validityPeriod" name="validity_period[active]">
                                                <label class="custom-control-label" for="validityPeriod">Validity Period</label>
                                            </div>
                                        </div>

                                        <div class="row validity-dates" style="display:none;">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Start Date</label>
                                                    <input type="date" class="form-control" name="validity_period[start]" id="startDate">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>End Date</label>
                                                    <input type="date" class="form-control" name="validity_period[end]" id="endDate">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Specific Days -->
                                        <div class="form-group mt-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" checked class="custom-control-input" id="specificDays" name="specific_days">
                                                <label class="custom-control-label" for="specificDays">Specific Days of Week</label>
                                            </div>
                                        </div>

                                          {{-- <div class="day-time-table" id="dayTimeTable" >
                                            <table class="table table-bordered">
                                                <thead style="background: #f8f9fa;">
                                                    <tr>
                                                        <th style="width: 15%;">DAY</th>
                                                        <th style="width: 35%;">START TIME</th>
                                                        <th style="width: 35%;">END TIME</th>
                                                        <th style="width: 15%;">ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr data-day="Monday">
                                                        <td>Monday</td>
                                                        <td>
                                                          <input type="time" class="form-control start-time"
                                                                name="working_hours[monday][start]"
                                                                value="{{ $specificDays['monday']['start'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                          <input type="time" class="form-control end-time"
                                                                name="working_hours[monday][end]"
                                                                value="{{ $specificDays['monday']['end'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Tuesday">
                                                        <td>Tuesday</td>
                                                        <td>
                                                             <input type="time" class="form-control start-time"
                                                                name="working_hours[tuesday][start]"
                                                                value="{{ $specificDays['tuesday']['start'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                             <input type="time" class="form-control end-time"
                                                                name="working_hours[tuesday][end]"
                                                                value="{{ $specificDays['tuesday']['end'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Wednesday">
                                                        <td>Wednesday</td>
                                                        <td>
                                                             <input type="time" class="form-control start-time"
                                                                name="working_hours[wednesday][start]"
                                                                value="{{ $specificDays['wednesday']['start'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                               <input type="time" class="form-control end-time"
                                                                name="working_hours[wednesday][end]"
                                                                value="{{ $specificDays['wednesday']['end'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Thursday">
                                                        <td>Thursday</td>
                                                        <td>
                                                              <input type="time" class="form-control start-time"
                                                                name="working_hours[thursday][start]"
                                                                value="{{ $specificDays['thursday']['start'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                                 <input type="time" class="form-control end-time"
                                                                name="working_hours[thursday][end]"
                                                                value="{{ $specificDays['thursday']['end'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Friday">
                                                        <td>Friday</td>
                                                        <td>
                                                               <input type="time" class="form-control start-time"
                                                                name="working_hours[friday][start]"
                                                                value="{{ $specificDays['friday']['start'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                                 <input type="time" class="form-control end-time"
                                                                name="working_hours[friday][end]"
                                                                value="{{ $specificDays['friday']['end'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Saturday">
                                                        <td>Saturday</td>
                                                        <td>
                                                                 <input type="time" class="form-control start-time"
                                                                name="working_hours[saturday][start]"
                                                                value="{{ $specificDays['saturday']['start'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                             <input type="time" class="form-control end-time"
                                                                name="working_hours[saturday][end]"
                                                                value="{{ $specificDays['saturday']['end'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Sunday">
                                                        <td>Sunday</td>
                                                        <td>
                                                              <input type="time" class="form-control start-time"
                                                                name="working_hours[sunday][start]"
                                                                value="{{ $specificDays['sunday']['start'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                              <input type="time" class="form-control end-time"
                                                                name="working_hours[sunday][end]"
                                                                value="{{ $specificDays['sunday']['end'] ?? '' }}">

                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div> --}}

                                        <div class="day-time-table" id="dayTimeTable" >
                                            <table class="table table-bordered">
                                                <thead style="background: #f8f9fa;">
                                                    <tr>
                                                        <th style="width: 15%;">DAY</th>
                                                        <th style="width: 35%;">START TIME</th>
                                                        <th style="width: 35%;">END TIME</th>
                                                        <th style="width: 15%;">ACTION</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr data-day="Monday">
                                                        <td>Monday</td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['monday']['start'] ?? '' }}" class="form-control start-time" name="working_hours[monday][start]">
                                                        </td>
                                                        <td>
                                                            <input type="time"   value="{{ $working_hours['monday']['end'] ?? '' }}" class="form-control end-time" name="working_hours[monday][end]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Tuesday">
                                                        <td>Tuesday</td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['tuesday']['start'] ?? '' }}" class="form-control start-time" name="working_hours[tuesday][start]">
                                                        </td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['tuesday']['end'] ?? '' }}" class="form-control end-time" name="working_hours[tuesday][end]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Wednesday">
                                                        <td>Wednesday</td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['wednesday']['start'] ?? '' }}" class="form-control start-time" name="working_hours[wednesday][start]">
                                                        </td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['wednesday']['end'] ?? '' }}"  class="form-control end-time" name="working_hours[wednesday][end]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Thursday">
                                                        <td>Thursday</td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['thursday']['start'] ?? '' }}" class="form-control start-time" name="working_hours[thursday][start]">
                                                        </td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['thursday']['end'] ?? '' }}" class="form-control end-time" name="working_hours[thursday][end]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Friday">
                                                        <td>Friday</td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['friday']['start'] ?? '' }}" class="form-control start-time" name="working_hours[friday][start]">
                                                        </td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['friday']['end'] ?? '' }}" class="form-control end-time" name="working_hours[friday][end]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Saturday">
                                                        <td>Saturday</td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['saturday']['start'] ?? '' }}" class="form-control start-time" name="working_hours[saturday][start]">
                                                        </td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['saturday']['end'] ?? '' }}" class="form-control end-time" name="working_hours[saturday][end]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>

                                                    <tr data-day="Sunday">
                                                        <td>Sunday</td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['sunday']['start'] ?? '' }}" class="form-control start-time" name="working_hours[sunday][start]">
                                                        </td>
                                                        <td>
                                                            <input type="time" value="{{ $working_hours['sunday']['end'] ?? '' }}" class="form-control end-time" name="working_hours[sunday][end]">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                        </td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>

                                        <!-- Holidays & Occasions -->
                                        <div class="form-group mt-4">
                                            <label style="font-weight: 600;">🎄 Holidays & Occasions</label>
                                            <p style="font-size: 13px; color: #666;">Holiday Restrictions</p>

                                                @foreach ($HolidayOccasion as $item)
                                                    <div class="holiday-checkbox">
                                                        <input type="checkbox" id="excludeNational_{{ $item->id}}" name="exclude_national[]" value="{{ $item->id}}">
                                                        <label for="excludeNational_{{ $item->id}}">  {{ $item->name_en}}</label>
                                                    </div>
                                                @endforeach

                                        </div>
                                        <!-- Custom Blackout Dates -->
                                        <div class="form-group mt-4">
                                            <label style="font-weight: 600;">🎄 Custom Blackout Dates</label>
                                            <p style="font-size: 13px; color: #666;">Custom Blackout Dates</p>

                                                @foreach ($CustomBlackoutData as $item)
                                                    <div class="holiday-checkbox">
                                                        <input type="checkbox" id="custom_blackout_dates_{{ $item->id}}" name="custom_blackout_dates[]" value="{{ $item->id}}">
                                                        <label for="custom_blackout_dates_{{ $item->id}}">  {{ $item->description}}</label>
                                                    </div>
                                                @endforeach

                                        </div>
                                    </div>
                                </div>

                                <!-- GENERAL RESTRICTIONS -->
                                <div class="condition-card">
                                    <div class="condition-header" data-bs-toggle="collapse" data-bs-target="#generalRestrictions">
                                        <div class="condition-title">
                                            <i class="tio-shield-outlined" style="font-size: 20px;"></i>
                                            <span>General Restrictions</span>
                                            <span class="active-badge" id="restrictionActiveCount">0 Active</span>
                                        </div>
                                        <i class="tio-chevron-down"></i>
                                    </div>
                                    <div class="collapse show condition-body" id="generalRestrictions">

                                        <!-- Age Restriction -->

                                          <div class="form-group">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="age_restriction">{{ translate('Age Restriction') }}
                                            </label>
                                            <!-- Dropdown: Only Percent & Fixed -->
                                            <select name="age_restriction[]" id="ageRestriction"  class="form-control js-select2-custom" multiple>
                                              <option value="">No requirement</option>
                                            @foreach ($AgeRestrictin as $item)
                                                <option value="{{ $item->id}}"> {{ $item->name_en}}</option>
                                                 @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-group mb-0">
                                            <label class="input-label"
                                                for="group_size">{{ translate('Group Size Requirement') }}
                                            </label>
                                            <!-- Dropdown: Only Percent & Fixed -->
                                            <select name="group_size[]" id="groupSize"  class="form-control js-select2-custom" multiple>
                                                   <option value="">No requirement</option>
                                                      @foreach ($GroupSizeRequirement as $item)
                                                <option value="{{ $item->id}}"> {{ $item->name_en}}</option>
                                                 @endforeach
                                            </select>
                                        </div>
                                    </div>
                                  
                                       <div class="form-group">
                                            <label>Usage Limit per User</label>
                                            <div class="usage-row">

                                                <div class="form-group">
                                                    <input type="number"
                                                        class="form-control"
                                                        name="user_limit[value]"
                                                        value="{{ $userLimit['value'] ?? '' }}"
                                                        placeholder="Number of times"
                                                        min="1">
                                                </div>

                                                <span class="times-label">times</span>

                                                <div class="form-group">
                                                    <select class="form-control" name="user_limit[period]">
                                                        <option value="">Select period</option>

                                                        @foreach ($UsagePeriod as $period)
                                                            <option value="{{ $period->name_en }}"
                                                                {{ ($userLimit['period'] ?? '') == $period->name_en ? 'selected' : '' }}>
                                                                {{ $period->name_en }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Usage Limit per Store</label>
                                            <div class="usage-row">

                                                <div class="form-group">
                                                    <input type="number"
                                                        class="form-control"
                                                        name="store_limit[value]"
                                                        value="{{ $storeLimit['value'] ?? '' }}"
                                                        placeholder="Number of times"
                                                        min="1">
                                                </div>

                                                <span class="times-label">times</span>

                                                <div class="form-group">
                                                    <select class="form-control" name="store_limit[period]">
                                                        <option value="">Select period</option>

                                                        @foreach ($UsagePeriod as $period)
                                                            <option value="{{ $period->name_en }}"
                                                                {{ ($storeLimit['period'] ?? '') == $period->name_en ? 'selected' : '' }}>
                                                                {{ $period->name_en }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>

                                            </div>
                                        </div>



                                    <div class="form-group">
                                        <label>Offer Validity After Purchase</label>
                                        <div class="usage-row">

                                            <div class="form-group">
                                                <input type="number"
                                                    class="form-control"
                                                    name="validity_after[value]"
                                                    value="{{ $validity_after['value'] ?? '' }}"
                                                    placeholder="Number of times"
                                                    min="1">
                                            </div>

                                            <span class="times-label">times</span>

                                            <div class="form-group">
                                                <select class="form-control" name="validity_after[period]">
                                                    <option value="">Select period</option>

                                                    @foreach ($OfferValidatyPeroid as $period)
                                                        <option value="{{ $period->name_en }}"
                                                            {{ ($validity_after['period'] ?? '') == $period->name_en ? 'selected' : '' }}>
                                                            {{ $period->name_en }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    
                                        <!-- Offer Validity -->
                                        <!-- <div class="form-group">
                                            <label>Offer Validity After Purchase</label>
                                            <select class="form-control" name="validity_after" id="validityAfter">
                                                <option value="">No time limit</option>
                                                 @foreach ($OfferValidatyPeroid as $item)
                                                <option value="{{ $item->id}}"> {{ $item->name_en}}</option>
                                                @endforeach
                                            </select>
                                        </div> -->

                                        <!-- General Restrictions Checkboxes -->
                                        <div class="form-group">
                                            <label style="font-weight: 600;">General Restrictions</label>

                                            @foreach ($GeneralRestriction as $item)
                                                <div class="holiday-checkbox">
                                                    <input type="checkbox" id="noOtherOffers_{{ $item->id}}" name="no_other_offers[]" value="{{ $item->id}}">
                                                    <label for="noOtherOffers_{{ $item->id}}">  {{ $item->name_en}}</label>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT SIDE - PREVIEW -->
                            <div class="col-lg-5">
                                <div class="preview-panel">
                                    <div class="preview-header">Active Conditions Preview</div>

                                    <!-- Available On -->
                                    <div class="preview-section" id="previewDays" style="display:none;">
                                        <div class="preview-label">Available on:</div>
                                        <div class="preview-value" id="previewDaysText">-</div>
                                    </div>

                                    <!-- Validity Period -->
                                    <div class="preview-section" id="previewValidityPeriod" style="display:none;">
                                        <div class="preview-label">Validity period:</div>
                                        <div class="preview-value" id="previewValidityPeriodText">-</div>
                                    </div>

                                    <!-- Holiday Restrictions -->
                                    <div class="preview-section" id="previewHolidays" style="display:none;">
                                        <div class="preview-label">Holiday restrictions:</div>
                                        <div class="preview-value" id="previewHolidaysText">-</div>
                                    </div>
                                    <!-- previewCustomBlackout -->
                                    <div class="preview-section" id="previewCustomBlackout" style="display:none;">
                                        <div class="preview-label">Holiday restrictions:</div>
                                        <div class="preview-value" id="previewCustomBlackoutText">-</div>
                                    </div>

                                    <!-- Age Restriction -->
                                    <div class="preview-section" id="previewAge" style="display:none;">
                                        <div class="preview-label">Age restriction:</div>
                                        <div class="preview-value" id="previewAgeText">-</div>
                                    </div>

                                    <!-- Group Size -->
                                    <div class="preview-section" id="previewGroup" style="display:none;">
                                        <div class="preview-label">Group size required:</div>
                                        <div class="preview-value" id="previewGroupText">-</div>
                                    </div>

                                    <!-- Validity -->
                                    <div class="preview-section" id="previewValidity" style="display:none;">
                                        <div class="preview-label">Validity after purchase:</div>
                                        <div class="preview-value" id="previewValidityText">-</div>
                                    </div>

                                    <!-- Limit per User -->
                                    <div class="preview-section" id="previewUserLimit" style="display:none;">
                                        <div class="preview-label">Limit per user:</div>
                                        <div class="preview-value" id="previewUserLimitText">-</div>
                                    </div>

                                    <!-- Limit per Store -->
                                    <div class="preview-section" id="previewStoreLimit" style="display:none;">
                                        <div class="preview-label">Limit per store:</div>
                                        <div class="preview-value" id="previewStoreLimitText">-</div>
                                    </div>

                                    <!-- General Restrictions -->
                                    <div class="preview-section" id="previewRestrictions" style="display:none;">
                                        <div class="preview-label">General restrictions:</div>
                                        <div class="preview-value" id="previewRestrictionsText">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-5">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                        </div>
                    </form>
                  </div>
        </div>
            <?php }else{ ?>  
                <!-- update voucher setting -->
             
                <div class="row g-3">
                    <div class="col-12">
                        <div class="card">
                        <div class="card-body">
                            <form action="{{route('admin.VoucherSetting.store')}}" method="post" id="conditionsForm">
                                @csrf
                                    <!-- <input type="hidden" name="item_id" value="{{ request()->route('id') }}"> -->
                                    <input type="hidden" name="item_id" value="{{ $items->id }}">
                                <div class="condition-header" >
                                <div class="condition-title">
                                    <span>Voucher Name:</span> {{ $items->name}} ,
                                    <span>Voucher Type:</span> {{ $items->voucher_ids}}
                                </div>
                                <div class="condition-title">
                                    <span>edit Setting</span>
                                </div>
                            </div>
                            <div class="row">
                                    <!-- LEFT SIDE - FORM -->
                                    <div class="col-lg-7">

                                        <!-- TIME CONDITIONS -->
                                        <div class="condition-card">
                                            <div class="condition-header" data-bs-toggle="collapse" data-bs-target="#timeConditions">
                                                <div class="condition-title">
                                                    <i class="tio-time" style="font-size: 20px;"></i>
                                                    <span>Time Conditions</span>
                                                    <span class="active-badge" id="timeActiveCount">0 Active</span>
                                                </div>
                                                <i class="tio-chevron-down"></i>
                                            </div>
                                            <div class="collapse show condition-body" id="timeConditions">

                                                <!-- Validity Period -->
                                            <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="validityPeriod"
                                                            name="validity_period[active]"
                                                            {{ isset($validityPeriod['active']) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="validityPeriod">Validity Period</label>
                                                    </div>
                                                </div>
                                                <div class="row validity-dates" >
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Start Date</label>
                                                    <input type="date" class="form-control" name="validity_period[start]"
                                                            id="startDate"
                                                            value="{{ $validityPeriod['start'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>End Date</label>
                                                        <input type="date" class="form-control" name="validity_period[end]"
                                                            id="endDate"
                                                            value="{{ $validityPeriod['end'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Specific Days -->
                                                <div class="form-group mt-3">
                                                    <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="specificDays"
                                                            name="specific_days"
                                                            {{ !empty($specificDays) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="specificDays">Specific Days of Week</label>
                                                    </div>
                                                </div>

                                            <div class="day-time-table" id="dayTimeTable" >
                                                    <table class="table table-bordered">
                                                        <thead style="background: #f8f9fa;">
                                                            <tr>
                                                                <th style="width: 15%;">DAY</th>
                                                                <th style="width: 35%;">START TIME</th>
                                                                <th style="width: 35%;">END TIME</th>
                                                                <th style="width: 15%;">ACTION</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr data-day="Monday">
                                                                <td>Monday</td>
                                                                <td>
                                                                <input type="time" class="form-control start-time"
                                                                        name="working_hours[monday][start]"
                                                                        value="{{ $specificDays['monday']['start'] ?? '' }}">
                                                                </td>
                                                                <td>
                                                                <input type="time" class="form-control end-time"
                                                                        name="working_hours[monday][end]"
                                                                        value="{{ $specificDays['monday']['end'] ?? '' }}">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                                </td>
                                                            </tr>

                                                            <tr data-day="Tuesday">
                                                                <td>Tuesday</td>
                                                                <td>
                                                                    <input type="time" class="form-control start-time"
                                                                        name="working_hours[tuesday][start]"
                                                                        value="{{ $specificDays['tuesday']['start'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control end-time"
                                                                        name="working_hours[tuesday][end]"
                                                                        value="{{ $specificDays['tuesday']['end'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                                </td>
                                                            </tr>

                                                            <tr data-day="Wednesday">
                                                                <td>Wednesday</td>
                                                                <td>
                                                                    <input type="time" class="form-control start-time"
                                                                        name="working_hours[wednesday][start]"
                                                                        value="{{ $specificDays['wednesday']['start'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control end-time"
                                                                        name="working_hours[wednesday][end]"
                                                                        value="{{ $specificDays['wednesday']['end'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                                </td>
                                                            </tr>

                                                            <tr data-day="Thursday">
                                                                <td>Thursday</td>
                                                                <td>
                                                                    <input type="time" class="form-control start-time"
                                                                        name="working_hours[thursday][start]"
                                                                        value="{{ $specificDays['thursday']['start'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                        <input type="time" class="form-control end-time"
                                                                        name="working_hours[thursday][end]"
                                                                        value="{{ $specificDays['thursday']['end'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                                </td>
                                                            </tr>

                                                            <tr data-day="Friday">
                                                                <td>Friday</td>
                                                                <td>
                                                                    <input type="time" class="form-control start-time"
                                                                        name="working_hours[friday][start]"
                                                                        value="{{ $specificDays['friday']['start'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                        <input type="time" class="form-control end-time"
                                                                        name="working_hours[friday][end]"
                                                                        value="{{ $specificDays['friday']['end'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                                </td>
                                                            </tr>

                                                            <tr data-day="Saturday">
                                                                <td>Saturday</td>
                                                                <td>
                                                                        <input type="time" class="form-control start-time"
                                                                        name="working_hours[saturday][start]"
                                                                        value="{{ $specificDays['saturday']['start'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control end-time"
                                                                        name="working_hours[saturday][end]"
                                                                        value="{{ $specificDays['saturday']['end'] ?? '' }}">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                                </td>
                                                            </tr>

                                                            <tr data-day="Sunday">
                                                                <td>Sunday</td>
                                                                <td>
                                                                    <input type="time" class="form-control start-time"
                                                                        name="working_hours[sunday][start]"
                                                                        value="{{ $specificDays['sunday']['start'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <input type="time" class="form-control end-time"
                                                                        name="working_hours[sunday][end]"
                                                                        value="{{ $specificDays['sunday']['end'] ?? '' }}">

                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm reset-day">Reset</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>

                                                    </table>
                                                </div>

                                                <!-- Holidays & Occasions -->
                                                <div class="form-group mt-4">
                                                    <label style="font-weight: 600;">🎄 Holidays & Occasions</label>
                                                    <p style="font-size: 13px; color: #666;">Holiday Restrictions</p>
                                                    @foreach ($HolidayOccasion as $item)
                                                    <div class="holiday-checkbox">
                                                        <input type="checkbox" id="excludeNational_{{ $item->id}}"
                                                            name="exclude_national[]"
                                                            value="{{ $item->id}}"
                                                            {{ in_array($item->id, $holidays ?? []) ? 'checked' : '' }}>
                                                        <label for="excludeNational_{{ $item->id}}">{{ $item->name_en}}</label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                              <?php   $custom_blackout_dates = $custom_blackout_dates?->pluck('id')->toArray() ?? []; ?>


                                                <!-- Custom Blackout Dates -->
                                                <div class="form-group mt-4">
                                                    <label style="font-weight: 600;">🎄 Custom Blackout Dates</label>
                                                    <p style="font-size: 13px; color: #666;">Custom Blackout Dates</p>

                                               @foreach ($CustomBlackoutData as $item)
                                             <div class="holiday-checkbox">
                                                <input type="checkbox" id="custom_blackout_dates_{{ $item->id}}"
                                                    name="custom_blackout_dates[]"
                                                    value="{{ $item->id}}"
                                                    {{ in_array($item->id, $custom_blackout_dates ?? []) ? 'checked' : '' }}>
                                                <label for="custom_blackout_dates_{{ $item->id}}">{{ $item->description}}</label>
                                            </div>
                                            @endforeach

                                                </div>
                                            </div>
                                        </div>

                                        <!-- GENERAL RESTRICTIONS -->
                                        <div class="condition-card">
                                            <div class="condition-header" data-bs-toggle="collapse" data-bs-target="#generalRestrictions">
                                                <div class="condition-title">
                                                    <i class="tio-shield-outlined" style="font-size: 20px;"></i>
                                                    <span>General Restrictions</span>
                                                    <span class="active-badge" id="restrictionActiveCount">0 Active</span>
                                                </div>
                                                <i class="tio-chevron-down"></i>
                                            </div>
                                            <div class="collapse show condition-body" id="generalRestrictions">

                                           <!-- Age Restriction -->
                                            <?php 

                                                $ageRestrictions = json_decode($VoucherSetting->age_restriction, true) ?? [];
                                            $group_size_requirement = json_decode($VoucherSetting->group_size_requirement, true) ?? [];
                                                ?>
                                        

                                        <div class="form-group">
                                                <label class="input-label" for="age_restriction">{{ translate('Age Restriction') }}</label>
                                                <select name="age_restriction[]" id="ageRestriction_{{ $setting->id ?? '' }}"  
                                                        class="form-control js-select2-custom" multiple>
                                                    @foreach ($AgeRestrictin as $itemn)
                                                        <option value="{{ $itemn->id }}" 
                                                            {{ in_array($itemn->id, $ageRestrictions) ? 'selected' : '' }}>
                                                            {{ $itemn->name_en }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        <div class="form-group">
                                            <label class="input-label" for="group_size">{{ translate('Group Size Requirement') }}</label>
                                            <select name="group_size[]" id="groupSize" class="form-control js-select2-custom" multiple>
                                                @foreach ($GroupSizeRequirement as $group)
                                                    <option value="{{ $group->id }}"
                                                        {{ in_array($group->id, $group_size_requirement)  ? 'selected' : '' }}>
                                                        {{ $group->name_en }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Usage Limit per User</label>
                                            <div class="usage-row">

                                                <div class="form-group">
                                                    <input type="number"
                                                        class="form-control"
                                                        name="user_limit[value]"
                                                        value="{{ $userLimit['value'] ?? '' }}"
                                                        placeholder="Number of times"
                                                        min="1">
                                                </div>

                                                <span class="times-label">times</span>

                                                <div class="form-group">
                                                    <select class="form-control" name="user_limit[period]">
                                                        <option value="">Select period</option>

                                                        @foreach ($UsagePeriod as $period)
                                                            <option value="{{ $period->name_en }}"
                                                                {{ ($userLimit['period'] ?? '') == $period->name_en ? 'selected' : '' }}>
                                                                {{ $period->name_en }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Usage Limit per Store</label>
                                            <div class="usage-row">

                                                <div class="form-group">
                                                    <input type="number"
                                                        class="form-control"
                                                        name="store_limit[value]"
                                                        value="{{ $storeLimit['value'] ?? '' }}"
                                                        placeholder="Number of times"
                                                        min="1">
                                                </div>

                                                <span class="times-label">times</span>

                                                <div class="form-group">
                                                    <select class="form-control" name="store_limit[period]">
                                                        <option value="">Select period</option>

                                                        @foreach ($UsagePeriod as $period)
                                                            <option value="{{ $period->name_en }}"
                                                                {{ ($storeLimit['period'] ?? '') == $period->name_en ? 'selected' : '' }}>
                                                                {{ $period->name_en }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                           <div class="form-group">
                                            <label>Offer Validity After Purchase</label>
                                            <div class="usage-row">

                                                <div class="form-group">
                                                    <input type="number"
                                                        class="form-control"
                                                        name="validity_after[value]"
                                                        value="{{ $offer_validity_after_purchase['value'] ?? '' }}"
                                                        placeholder="Number of times"
                                                        min="1">
                                                </div>

                                                <span class="times-label">times</span>

                                                <div class="form-group">
                                                    <select class="form-control" name="validity_after[period]">
                                                        <option value="">Select period</option>

                                                        @foreach ($OfferValidatyPeroid as $period)
                                                            <option value="{{ $period->name_en }}"
                                                                {{ ($offer_validity_after_purchase['period'] ?? '') == $period->name_en ? 'selected' : '' }}>
                                                                {{ $period->name_en }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                </div>

                                            </div>
                                        </div>


                                        <!-- Offer Validity -->
                                        <!-- <div class="form-group">
                                            <label>Offer Validity After Purchase</label>
                                            <select class="form-control" name="validity_after" id="validityAfter">
                                                <option value="">No time limit</option>
                                                @foreach ($OfferValidatyPeroid as $offer)
                                                        <option value="{{ $offer->id }}"
                                                            {{ $VoucherSetting->offer_validity_after_purchase == $offer->id ? 'selected' : '' }}>
                                                            {{ $offer->name_en }}
                                                        </option>
                                                    @endforeach
                                            </select>
                                        </div> -->
                                        <!-- General Restrictions Checkboxes -->
                                        <div class="form-group">
                                            <label style="font-weight: 600;">General Restrictions</label>
                                            @foreach ($GeneralRestriction as $item)
                                            <div class="holiday-checkbox">
                                                <input type="checkbox" id="noOtherOffers_{{ $item->id}}"
                                                    name="no_other_offers[]"
                                                    value="{{ $item->id}}"
                                                    {{ in_array($item->id, $generalRestrictions ?? []) ? 'checked' : '' }}>
                                                <label for="noOtherOffers_{{ $item->id}}">{{ $item->name_en}}</label>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT SIDE - PREVIEW -->
                            <div class="col-lg-5">
                                <div class="preview-panel">
                                    <div class="preview-header">Active Conditions Preview</div>

                                    <!-- Available On -->
                                    <div class="preview-section" id="previewDays" style="display:none;">
                                        <div class="preview-label">Available on:</div>
                                        <div class="preview-value" id="previewDaysText">-</div>
                                    </div>

                                    <!-- Validity Period -->
                                    <div class="preview-section" id="previewValidityPeriod" style="display:none;">
                                        <div class="preview-label">Validity period:</div>
                                        <div class="preview-value" id="previewValidityPeriodText">-</div>
                                    </div>

                                    <!-- Holiday Restrictions -->
                                    <div class="preview-section" id="previewHolidays" style="display:none;">
                                        <div class="preview-label">Holiday restrictions:</div>
                                        <div class="preview-value" id="previewHolidaysText">-</div>
                                    </div>

                                    <!-- previewCustomBlackout -->
                                    <div class="preview-section" id="previewCustomBlackout" style="display:none;">
                                        <div class="preview-label">Holiday restrictions:</div>
                                        <div class="preview-value" id="previewCustomBlackoutText">-</div>
                                    </div>
                                    <!-- Age Restriction -->
                                    <div class="preview-section" id="previewAge" style="display:none;">
                                        <div class="preview-label">Age restriction:</div>
                                        <div class="preview-value" id="previewAgeText">-</div>
                                    </div>

                                    <!-- Group Size -->
                                    <div class="preview-section" id="previewGroup" style="display:none;">
                                        <div class="preview-label">Group size required:</div>
                                        <div class="preview-value" id="previewGroupText">-</div>
                                    </div>

                                    <!-- Validity -->
                                    <div class="preview-section" id="previewValidity" style="display:none;">
                                        <div class="preview-label">Validity after purchase:</div>
                                        <div class="preview-value" id="previewValidityText">-</div>
                                    </div>

                                    <!-- Limit per User -->
                                    <div class="preview-section" id="previewUserLimit" style="display:none;">
                                        <div class="preview-label">Limit per user:</div>
                                        <div class="preview-value" id="previewUserLimitText">-</div>
                                    </div>

                                    <!-- Limit per Store -->
                                    <div class="preview-section" id="previewStoreLimit" style="display:none;">
                                        <div class="preview-label">Limit per store:</div>
                                        <div class="preview-value" id="previewStoreLimitText">-</div>
                                    </div>

                                    <!-- General Restrictions -->
                                    <div class="preview-section" id="previewRestrictions" style="display:none;">
                                        <div class="preview-label">General restrictions:</div>
                                        <div class="preview-value" id="previewRestrictionsText">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn--container justify-content-end mt-5">
                            <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('Update')}}</button>
                        </div>
                    </form>
                </div>
        </div>
            <?php } ?>
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
    let selectedDays = [];

    // Format time to 12-hour format
    function formatTime(time24) {
        if (!time24) return '';
        let [hours, minutes] = time24.split(':');
        hours = parseInt(hours);
        let period = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes} ${period}`;
    }

    // Update Preview Function
    function updatePreview() {
        let timeActive = 0;
        let restrictionActive = 0;

        // Days Preview with Times
        let daysWithTimes = [];
        let processedDays = []; // Track processed days to avoid duplicates

        $('tr[data-day]').each(function() {
            let day = $(this).data('day');
            let startTime = $(this).find('.start-time').val();
            let endTime = $(this).find('.end-time').val();

            // Only add if not already processed and has time values
            if (!processedDays.includes(day) && (startTime || endTime)) {
                let timeDisplay = '';
                if (startTime && endTime) {
                    timeDisplay = ` (${formatTime(startTime)} - ${formatTime(endTime)})`;
                } else if (startTime) {
                    timeDisplay = ` (from ${formatTime(startTime)})`;
                } else if (endTime) {
                    timeDisplay = ` (until ${formatTime(endTime)})`;
                }
                daysWithTimes.push(`<div style="margin-bottom: 5px;">${day}${timeDisplay}</div>`);
                processedDays.push(day);
            }
        });

        if (daysWithTimes.length > 0) {
            $('#previewDays').show();
            $('#previewDaysText').html(daysWithTimes.join(''));
            timeActive++;
        } else {
            $('#previewDays').hide();
        }

        // Update selected days array
       // Holidays Preview
        let allHolidays = @json($HolidayOccasion);

        // Get checked checkboxes
        let checkedHolidays = [];
        $('input[name="exclude_national[]"]:checked').each(function () {
            checkedHolidays.push(parseInt($(this).val())); // push checked IDs
        });

        // Filter holiday objects by checked IDs and get their names
        let holidays = allHolidays
            .filter(item => checkedHolidays.includes(item.id))
            .map(item => item.name_en);

        // Show/hide preview
        if (holidays.length > 0) {
            $('#previewHolidays').show();
            $('#previewHolidaysText').html(
                holidays.map(h => `<div style="margin-bottom: 5px;">${h}</div>`).join('')
            );
            timeActive++;
        } else {
            $('#previewHolidays').hide();
        }
       // Holidays Preview
        let CustomBlackoutData_data = @json($CustomBlackoutData);

        // Get checked checkboxes
        let CustomBlackoutData_filter = [];
        $('input[name="custom_blackout_dates[]"]:checked').each(function () {
            CustomBlackoutData_filter.push(parseInt($(this).val())); // push checked IDs
        });

        // Filter holiday objects by checked IDs and get their names
        let CustomBlackoutData_length = CustomBlackoutData_data
            .filter(item => CustomBlackoutData_filter.includes(item.id))
            .map(item => item.description);

        // Show/hide preview
        if (CustomBlackoutData_length.length > 0) {
            $('#previewCustomBlackout').show();
            $('#previewCustomBlackoutText').html(
                CustomBlackoutData_length.map(h => `<div style="margin-bottom: 5px;">${h}</div>`).join('')
            );
            timeActive++;
        } else {
            $('#previewCustomBlackout').hide();
        }


        // Age Restriction Preview
        let ageRestriction = $('#ageRestriction').val();
        if (ageRestriction) {
            $('#previewAge').show();
            $('#previewAgeText').text($('#ageRestriction option:selected').text());
            restrictionActive++;
        } else {
            $('#previewAge').hide();
        }

        // Group Size
        let groupSize = $('#groupSize').val();
        if (groupSize) {
            $('#previewGroup').show();
            $('#previewGroupText').text($('#groupSize option:selected').text());
            restrictionActive++;
        } else {
            $('#previewGroup').hide();
        }

        // Validity
        let validity = $('#validityAfter').val();
        if (validity) {
            $('#previewValidity').show();
            $('#previewValidityText').text($('#validityAfter option:selected').text());
            restrictionActive++;
        } else {
            $('#previewValidity').hide();
        }

        // User Limit
        let userLimit = $('#userLimit').val();
        let userPeriod = $('#userPeriod').val();
        if (userLimit && userPeriod) {
            $('#previewUserLimit').show();
            $('#previewUserLimitText').text(userLimit + ' ' + $('#userPeriod option:selected').text());
            restrictionActive++;
        } else {
            $('#previewUserLimit').hide();
        }

        // Store Limit
        let storeLimit = $('#storeLimit').val();
        let storePeriod = $('#storePeriod').val();
        if (storeLimit && storePeriod) {
            $('#previewStoreLimit').show();
            $('#previewStoreLimitText').text(storeLimit + ' ' + $('#storePeriod option:selected').text());
            restrictionActive++;
        } else {
            $('#previewStoreLimit').hide();
        }

        // General Restrictions
        // General Restrictions Preview
        let allRestrictions = @json($GeneralRestriction);

        // Get checked checkboxes
        let checkedRestrictions = [];
        $('input[name="no_other_offers[]"]:checked').each(function () {
            checkedRestrictions.push(parseInt($(this).val())); // checkbox IDs
        });

        // Filter restriction objects by checked IDs and get their names
        let restrictions = allRestrictions
            .filter(item => checkedRestrictions.includes(item.id))
            .map(item => item.name_en);

        // Show/hide preview
        if (restrictions.length > 0) {
            $('#previewRestrictions').show();
            $('#previewRestrictionsText').html(
                restrictions.map(r => `<div style="margin-bottom: 5px;">${r}</div>`).join('')
            );
            restrictionActive++;
        } else {
            $('#previewRestrictions').hide();
        }


        // Update Active Counts
        $('#timeActiveCount').text(timeActive + ' Active');
        $('#restrictionActiveCount').text(restrictionActive + ' Active');
    }

    // Validity Period Toggle
    $('#validityPeriod').change(function() {
        $('.validity-dates').toggle(this.checked);
        updatePreview();
    });

    // Validity Period Dates Change
    $('#startDate, #endDate').change(function() {
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();

        if (startDate || endDate) {
            $('#previewValidityPeriod').show();
            let dateText = '';
            if (startDate && endDate) {
                dateText = `From ${formatDate(startDate)} to ${formatDate(endDate)}`;
            } else if (startDate) {
                dateText = `From ${formatDate(startDate)}`;
            } else if (endDate) {
                dateText = `Until ${formatDate(endDate)}`;
            }
            $('#previewValidityPeriodText').text(dateText);
        } else {
            $('#previewValidityPeriod').hide();
        }
        updatePreview();
    });

    // Format date function
    function formatDate(dateStr) {
        if (!dateStr) return '';
        let date = new Date(dateStr);
        let options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    // Specific Days Toggle
    $('#specificDays').change(function() {
        $('#dayTimeTable').toggle(this.checked);
        if (!this.checked) {
            // selectedDays = [];
            // $('.start-time, .end-time').val('');
        }
        updatePreview();
    });

    // Reset Individual Day
    $('.reset-day').click(function() {
        let row = $(this).closest('tr');
        row.find('.start-time, .end-time').val('');
        updatePreview();
    });

    // Time Input Changes
    $('.start-time, .end-time').on('change input', function() {
        updatePreview();
    });

    // All Change Events
    $('input[type="checkbox"], select, input[type="number"]').change(updatePreview);
    $('input[type="date"]').change(updatePreview);

    // Initial Preview
    updatePreview();
});
</script>
@endpush
