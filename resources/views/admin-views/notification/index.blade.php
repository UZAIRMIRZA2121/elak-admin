@extends('layouts.admin.app')

@section('title', translate('messages.notification'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/notification.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.notification') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.notification.store') }}" method="post" enctype="multipart/form-data"
                            id="notification">
                            @csrf
                            <input type="hidden" name="tergat" class="form-control" value="customer">
                            <div class="row gy-3">
                                <div class="col-lg-6">
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label">
                                                    {{ translate('messages.clients') }}
                                                </label>

                                                <select name="client_id" id="client_id" class="form-control">
                                                    <option value="">
                                                        {{ translate('messages.select_client') }}
                                                    </option>

                                                    @foreach ($clients as $client)
                                                        <option value="{{ $client->id }}">
                                                            {{ $client->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Segment Dropdown -->
                                        <div class="col-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label">Segments</label>

                                                <select name="segment_id" id="segment_id" class="form-control">
                                                    <option value="">Select Segment</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.zone') }}</label>
                                                <select name="zone" id="zone"
                                                    class="form-control js-select2-custom">
                                                    <option value="all">{{ translate('messages.all') }}</option>
                                                    @foreach ($zones as $zone)
                                                        <option value="{{ $zone['id'] }}">{{ $zone['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.title') }}</label>
                                                <input type="text" name="notification_title" class="form-control"
                                                    placeholder="{{ translate('messages.new_notification') }}" required
                                                    maxlength="191">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.description') }}</label>
                                                <textarea name="description" class="form-control" maxlength="1000" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="input-label d-block">Type</label>

                                            <label class="mr-3">
                                                <input type="radio" name="type" value="normal" checked> Normal
                                            </label>

                                            <label>
                                                <input type="radio" name="type" value="voucher"> Voucher
                                            </label>
                                        </div>

                                    
                                            <div class="col-6" id="voucher_section_store" style="display: none;">
                                                <div class="form-group mb-0">
                                                    <label class="input-label">
                                                        {{ translate('messages.store') }}
                                                    </label>

                                                    <select name="store_id" id="store_id" class="form-control">
                                                        <option value="">{{ translate('messages.select_store') }}
                                                        </option>

                                                        @foreach ($stores as $store)
                                                            <option value="{{ $store->id }}">{{ $store->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Voucher Dropdown -->
                                            <div class="col-6" id="voucher_section_id" style="display: none;">
                                                <div class="form-group mb-0">
                                                    <label class="input-label">Vouchers</label>

                                                    <select name="voucher_id" id="voucher_id" class="form-control" disabled>
                                                        <option value="">Select Voucher</option>
                                                    </select>
                                                </div>
                                            </div>
                                     

                                        <div class="col-12"  id="normal_section_link">
                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.link') }}</label>
                                                <input type="url" name="notification_link" class="form-control"
                                                    placeholder="{{ translate('messages.notification_link') }}" required
                                                    maxlength="191">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" id="normal_section_img">
                                    <div class="h-100 d-flex flex-column">
                                        <label class="d-block text-center mt-auto mb-0">
                                            {{ translate('messages.image') }}
                                            <small class="text-danger">* ( {{ translate('messages.ratio') }} 900x300
                                                )</small>
                                        </label>
                                        <div class="text-center py-3 my-auto">
                                            <img class="img--vertical" id="viewer"
                                                src="{{ asset('public/assets/admin/img/900x400/img1.jpg') }}"
                                                alt="image" />
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" name="image" id="customFileEg1"
                                                class="custom-file-input"
                                                accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                for="customFileEg1">{{ translate('messages.choose_file') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" id="reset_btn"
                                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                        <button type="submit" id="submit"
                                            class="btn btn--primary">{{ translate('messages.send_notification') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">{{ translate('Notification list') }}<span
                                    class="badge badge-soft-dark ml-2">{{ $notifications->total() }}</span></h5>
                            <form class="search-form">
                                <!-- Search -->
                                <div class="input-group input--group min--270">
                                    <input type="search" name="search" class="form-control"
                                        value="{{ request()?->search ?? null }}"
                                        placeholder="{{ translate('messages.search_notification') }}">
                                    <button type="submit" class="btn btn--secondary">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                                <!-- End Search -->
                            </form>
                            @if (request()->get('search'))
                                <button type="reset" class="btn btn--primary ml-2 location-reload-to-base"
                                    data-url="{{ url()->full() }}">{{ translate('messages.reset') }}</button>
                            @endif


                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40"
                                    href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">

                                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>


                                    <a id="export-excel" class="dropdown-item"
                                        href="{{ route('admin.notification.export', ['type' => 'excel', request()->getQueryString()]) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>
                                    <a id="export-csv" class="dropdown-item"
                                        href="{{ route('admin.notification.export', ['type' => 'csv', request()->getQueryString()]) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>

                                </div>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging": false
                               }'>
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">{{ translate('messages.SL') }}</th>
                                    <th class="border-0">{{ translate('messages.title') }}</th>
                                    <th class="border-0">{{ translate('messages.description') }}</th>
                                    <th class="border-0">{{ translate('messages.image') }}</th>
                                    <th class="border-0">{{ translate('messages.zone') }}</th>
                                    <th class="border-0">{{ translate('messages.tergat') }}</th>
                                    <th class="border-0">{{ translate('messages.voucher') }}</th>
                                    <th class="border-0">{{ translate('messages.link') }}</th>
                                    <th class="text-center border-0">{{ translate('messages.status') }}</th>
                                    <th class="text-center border-0">{{ translate('messages.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($notifications as $key => $notification)
                                    <tr>
                                        <td>{{ $key + $notifications->firstItem() }}</td>
                                        <td>
                                            <span title="{{ $notification['title'] }}"
                                                class="d-block font-size-sm text-body">
                                                {{ substr($notification['title'], 0, 25) }}
                                                {{ strlen($notification['title']) > 25 ? '...' : '' }}
                                            </span>
                                        </td>
                                        <td title="{{ $notification['description'] }}">
                                            {{ substr($notification['description'], 0, 25) }}
                                            {{ strlen($notification['description']) > 25 ? '...' : '' }}
                                        </td>
                                        <td>
                                            @if ($notification['image'] != null)
                                                <img class="h--50px onerror-image"
                                                    src="{{ $notification['image_full_url'] }}"
                                                    data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}">
                                            @else
                                                <label
                                                    class="badge badge-soft-warning">{{ translate('No Image') }}</label>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $notification->zone_id == null ? translate('messages.all') : ($notification->zone ? $notification->zone->name : translate('messages.zone_deleted')) }}
                                        </td>
                                        <td class="text-uppercase">
                                            {{ translate($notification->tergat) }}
                                        </td>
                                        <td class="text-uppercase">
                                            {{ translate($notification->voucher->name ?? 'messages.voucher_not_found') }}
                                        </td>
                                        <td>
                                            @if ($notification->notification_link)
                                                <a href="{{ $notification->notification_link }}" target="_blank"
                                                    rel="noopener noreferrer">
                                                    {{ $notification->notification_link }}
                                                </a>
                                            @else
                                                {{ translate('messages.link_not_found') }}
                                            @endif
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm"
                                                for="stocksCheckbox{{ $notification->id }}">
                                                <input type="checkbox"
                                                    data-url="{{ route('admin.notification.status', [$notification['id'], $notification->status ? 0 : 1]) }}"
                                                    class="toggle-switch-input redirect-url"
                                                    id="stocksCheckbox{{ $notification->id }}"
                                                    {{ $notification->status ? 'checked' : '' }} hidden>
                                                <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                {{-- <a class="btn action-btn btn--primary btn-outline-primary"
                                                    href="{{ route('admin.notification.edit', [$notification['id']]) }}"
                                                    title="{{ translate('messages.edit_notification') }}"><i
                                                        class="tio-edit"></i>
                                                </a> --}}
                                                <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                                    href="javascript:" data-id="notification-{{ $notification['id'] }}"
                                                    data-message="{{ translate('Want to delete this notification ?') }}"
                                                    title="{{ translate('messages.delete_notification') }}"><i
                                                        class="tio-delete-outlined"></i>
                                                </a>
                                                <form
                                                    action="{{ route('admin.notification.delete', [$notification['id']]) }}"
                                                    method="post" id="notification-{{ $notification['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if (count($notifications) !== 0)
                        <hr>
                    @endif
                    <div class="page-area">
                        {!! $notifications->links() !!}
                    </div>
                    @if (count($notifications) === 0)
                        <div class="empty--data">
                            <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                            <h5>
                                {{ translate('no_data_found') }}
                            </h5>
                        </div>
                    @endif
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/view-pages/notification.js"></script>
    <script>
        "use strict";
        $('#notification').on('submit', function(e) {

            e.preventDefault();
            var formData = new FormData(this);

            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: '{{ translate('messages.you want to sent notification to') }}' + $('#tergat').val() +
                    '?',
                type: 'info',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: 'primary',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.send') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.post({
                        url: '{{ route('admin.notification.store') }}',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            if (data.errors) {
                                for (var i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                toastr.success('Notifiction sent successfully!', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                                setTimeout(function() {
                                    location.href =
                                        '{{ route('admin.notification.add-new') }}';
                                }, 2000);
                            }
                        }
                    });
                }
            })
        })

        $('#reset_btn').click(function() {
            $('#zone').val('all').trigger('change');
            $('#viewer').attr('src', '{{ asset('public/assets/admin/img/900x400/img1.jpg') }}');
            $('#customFileEg1').val(null);
        })
    </script>

    <script>
        $(document).ready(function() {

            $('#client_id').on('change', function() {

                let client_id = $(this).val();

                // disable + reset
                $('#segment_id')
                    .prop('disabled', true)
                    .html('<option value="">Loading...</option>');

                if (client_id) {

                    $.ajax({
                        url: "{{ route('admin.notification.get-segments') }}",
                        type: 'GET',
                        data: {
                            client_id: client_id
                        },

                        success: function(response) {

                            $('#segment_id').empty();
                            $('#segment_id').append('<option value="">Select Segment</option>');

                            $.each(response, function(key, segment) {
                                $('#segment_id').append(
                                    `<option value="${segment.id}">${segment.name}</option>`
                                );
                            });

                            // enable after success
                            $('#segment_id').prop('disabled', false);
                        },

                        error: function() {
                            // agar error aaye to bhi enable karo
                            $('#segment_id')
                                .prop('disabled', false)
                                .html('<option value="">Failed to load</option>');
                        }
                    });

                } else {
                    $('#segment_id')
                        .prop('disabled', true)
                        .html('<option value="">Select Segment</option>');
                }

            });

        });
    </script>

    <script>
        $(document).ready(function() {

            $('#store_id').on('change', function() {

                let store_id = $(this).val();

                // disable + loading
                $('#voucher_id')
                    .prop('disabled', true)
                    .html('<option value="">Loading...</option>');

                if (store_id) {

                    $.ajax({
                        url: "{{ route('admin.notification.get-store-vouchers') }}",
                        type: 'GET',
                        data: {
                            store_id: store_id
                        },

                        success: function(response) {

                            $('#voucher_id').empty();
                            $('#voucher_id').append('<option value="">Select Voucher</option>');

                            $.each(response, function(key, voucher) {
                                $('#voucher_id').append(
                                    `<option value="${voucher.id}">${voucher.name}</option>`
                                );
                            });

                            $('#voucher_id').prop('disabled', false);
                        },

                        error: function() {
                            $('#voucher_id')
                                .prop('disabled', false)
                                .html('<option value="">Failed to load</option>');
                        }
                    });

                } else {
                    $('#voucher_id')
                        .prop('disabled', true)
                        .html('<option value="">Select Voucher</option>');
                }

            });

        });
    </script>
<script>
$(document).ready(function() {

    function toggleFields(type) {

        if (type === 'normal') {

            // show normal
            $('#normal_section_img').show();
            $('#normal_section_link').show();

            // hide voucher
            $('#voucher_section_store').hide();
            $('#voucher_section_id').hide();

            // required handling
            $('input[name="notification_link"]').prop('required', true);
            $('#store_id, #voucher_id').prop('required', false);

        } else {

            // hide normal
            $('#normal_section_img').hide();
            $('#normal_section_link').hide();

            // show voucher
            $('#voucher_section_store').show();
            $('#voucher_section_id').show();

            // required handling
            $('input[name="notification_link"]').prop('required', false);
            $('#store_id, #voucher_id').prop('required', true);
        }
    }

    // on change
    $('input[name="type"]').on('change', function() {
        toggleFields($(this).val());
    });

    // page load par bhi correct state set karo
    let initialType = $('input[name="type"]:checked').val();
    toggleFields(initialType);

});
</script>
@endpush
