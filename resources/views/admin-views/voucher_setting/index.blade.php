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
         <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Left: Heading -->
            <h1 class="page-header-title d-flex align-items-center">
                <img src="{{ asset('public/assets/admin/img/condition.png') }}" class="me-2" style="width:26px; height:26px;" alt="">
                List Holiday & Occasion
            </h1>

            <!-- Right: Button -->
            <a href="{{ route('admin.VoucherSetting.add-new') }}" class="btn btn-primary">
                {{ translate('Add New') }}
            </a>
        </div>


        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        @php($defaultLang = str_replace('_', '-', app()->getLocale()))
        <!-- End Page Header -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                Voucher Setting<span class="badge badge-soft-dark ml-2" id="itemCount"></span>
                            </h5>
                            <form  class="search-form">
                                <!-- Search -->

                                <div class="input-group input--group">
                                    <input id="datatableSearch_" value="{{ request()?->search ?? null }}" type="search" name="search" class="form-control"
                                            placeholder="Ex: Voucher Setting" aria-label="Search" >
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
                            @if(request()->get('search'))
                            <button type="reset" class="btn btn--primary ml-2 location-reload-to-base" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                            @endif

                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                            <tr class="text-center">
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">Validity Period</th>
                                <th class="border-0">Specific Days of Week</th>
                                <th class="border-0">Holidays Occasions</th>
                                <th class="border-0">Age Restriction</th>
                                <th class="border-0">Group Size Requirement</th>
                                <th class="border-0">Usage Limit Per User</th>
                                <th class="border-0">Usage Limit Per Store</th>
                                <th class="border-0">Offer Validity After Purchase</th>
                                <th class="border-0">General Restrictions</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Action</th>
                            </tr>

                            </thead>

                            <tbody id="set-rows">
                         @foreach($VoucherSetting as $key => $voucher)
                            <tr>
                                {{-- Serial No --}}
                                <td class="text-center">
                                    <span class="mr-3">
                                        {{ $VoucherSetting->firstItem() + $key }}
                                    </span>
                                </td>

                                {{-- Client Name --}}
                                <td class="text-center">
                                    <span  class="font-size-sm text-body mr-3">
                                       @php(
                                            $period = json_decode($voucher->validity_period, true) )

                                         <table style="margin:auto; border:1px solid #ddd; border-collapse: collapse;">
                                            <tr>
                                                <th style="border:1px solid #ddd; padding:5px;">Start</th>
                                                <td style="border:1px solid #ddd; padding:5px;">{{ $period['start'] }}</td>
                                            </tr>
                                            <tr>
                                                <th style="border:1px solid #ddd; padding:5px;">End</th>
                                                <td style="border:1px solid #ddd; padding:5px;">{{ $period['end'] }}</td>
                                            </tr>
                                        </table>

                                    </span>
                                </td>

                                {{-- Client Created At --}}
                                <td class="text-center">
                                    @php(
                                        $days = json_decode($voucher->specific_days_of_week, true)
                                    )
                                    <table class="table table-bordered text-center" style="font-size: 12px">
                                        <thead>
                                            <tr>
                                                <th>Day</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($days as $day => $times)
                                                <tr>
                                                    <td>{{ ucfirst($day) }}</td>
                                                    <td>{{ $times['start'] }}</td>
                                                    <td>{{ $times['end'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>


                                  @php($HolidayOccasions = json_decode($voucher->holidays_occasions, true) ?? []
                                  )
                              @php($HolidayOccasion_all = \App\Models\HolidayOccasion::whereIn('id', $HolidayOccasions)->get())

                               <td class="text-center">
                                @if($HolidayOccasion_all->isNotEmpty())
                                    @foreach($HolidayOccasion_all as $item)
                                        {{ $item->name }}<br>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                                  <td class="text-center">
                                   {{ $voucher->age_restriction}}
                                </td>

                                <td class="text-center">
                                   {{ $voucher->group_size_requirement}}
                                </td>
                               @php( $user_limit = json_decode($voucher->usage_limit_per_user, true)  )
                               @php( $store_limit = json_decode($voucher->usage_limit_per_store, true) )

                                <td class="text-center">
                                    {{ $user_limit[0] }} <br>
                                    <small>{{ $user_limit[1] }}</small>
                                </td>
                                <td class="text-center">
                                    {{ $store_limit[0] }} <br>
                                    <small>{{ $store_limit[1] }}</small>
                                </td>

                                <td class="text-center">
                                   {{ $voucher->offer_validity_after_purchase}}
                                </td>
                            @php(

                                $generalRestrictionIds = json_decode($voucher->general_restrictions, true) ?? [] // null ho to empty array
                            )
                                 @php(
                                    $GeneralRestriction = \App\Models\GeneralRestriction::whereIn('id', $generalRestrictionIds)->get()
                                  )

                            <td class="text-center">
                                @if($GeneralRestriction->isNotEmpty())
                                    @foreach($GeneralRestriction as $restriction)
                                        {{ $restriction->name }} <br>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>


                                    {{-- Status Toggle (Active/Inactive) --}}
                                      <td class="text-center">
                                    <label class="toggle-switch toggle-switch-sm" for="status-{{ $voucher->id }}">
                                        <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                            {{ $voucher->status == 'active' ? 'checked' : '' }}
                                            data-id="status-{{ $voucher->id }}"
                                            data-type="status"
                                            id="status-{{ $voucher->id }}">
                                        <span class="toggle-switch-label mx-auto">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                    <form action="{{ route('admin.VoucherSetting.status', [$voucher->id]) }}"
                                        method="post" id="status-{{ $voucher->id }}_form">
                                        @csrf
                                    </form>
                                </td>

                                {{-- Action Buttons --}}
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary"
                                        href="{{ route('admin.VoucherSetting.edit', [$voucher->id]) }}"
                                        title="Edit">
                                        <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                        href="javascript:"
                                        data-id="client-{{ $voucher->id }}"
                                        data-message="Want to delete this client ?"
                                        title="Delete">
                                        <i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{ route('admin.VoucherSetting.delete', [$voucher->id]) }}"
                                            method="post" id="client-{{ $voucher->id }}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                            </tbody>
                        </table>
                    </div>
                    @if(count($VoucherSetting) !== 0)
                    <hr>
                    @endif
                    <div class="page-area">
                        {!! $VoucherSetting->links() !!}
                    </div>
                    @if(count($VoucherSetting) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                </div>
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
        selectedDays = processedDays;

        // Holidays Preview
        let holidays = [];
        if ($('#excludeNewYear').is(':checked')) holidays.push('New Year');
        if ($('#excludeRamadan').is(':checked')) holidays.push('Ramadan');
        if ($('#excludeReligious').is(':checked')) holidays.push('Religious Holidays');
        if ($('#excludeNational').is(':checked')) holidays.push('National Holidays');
        if ($('#excludeChristmas').is(':checked')) holidays.push('Christmas Period');
        if ($('#excludeEid').is(':checked')) holidays.push('Eid Holidays');

        if (holidays.length > 0) {
            $('#previewHolidays').show();
            $('#previewHolidaysText').html(holidays.map(h => `<div style="margin-bottom: 5px;">${h}</div>`).join(''));
            timeActive++;
        } else {
            $('#previewHolidays').hide();
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
        let restrictions = [];
        if ($('#noLoyalty').is(':checked')) restrictions.push('Does not apply to loyalty points');
        if ($('#requiresAccount').is(':checked')) restrictions.push('Requires registered account');
        if ($('#noCashback').is(':checked')) restrictions.push('Does not apply to cashback');
        if ($('#noOtherOffers').is(':checked')) restrictions.push('Cannot be combined with other offers');
        if ($('#noDiscountCodes').is(':checked')) restrictions.push('Cannot be combined with other discount codes');

        if (restrictions.length > 0) {
            $('#previewRestrictions').show();
            $('#previewRestrictionsText').html(restrictions.join(', '));
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
            selectedDays = [];
            $('.start-time, .end-time').val('');
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
