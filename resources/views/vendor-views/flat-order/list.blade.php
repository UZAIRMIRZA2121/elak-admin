@extends('layouts.vendor.app')

@section('title', translate('messages.Order List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title text-capitalize">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/order.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate(str_replace('_', ' ', $status)) }} {{ translate('messages.orders') }}
                    {{-- <span class="badge badge-soft-dark ml-2">{{$orders->total()}}</span> --}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper justify-content-end">
                    <form class="search-form min--260">

                        <!-- Search -->
                        <div class="input-group input--group">
                            <input type="search" value="{{ request()?->search ?? null }}" name="search"
                                class="form-control" placeholder="{{ translate('messages.ex_:_search_order_id') }}"
                                aria-label="{{ translate('messages.search') }}">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:"
                            data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                            <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{ translate('messages.options') }}</span>
                            <a id="export-copy" class="dropdown-item" href="javascript:">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/illustrations/copy.svg') }}"
                                    alt="Image Description">
                                {{ translate('messages.copy') }}
                            </a>
                            <a id="export-print" class="dropdown-item" href="javascript:">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/illustrations/print.svg') }}"
                                    alt="Image Description">
                                {{ translate('messages.print') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                            <a id="export-excel" class="dropdown-item" href="javascript:">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="javascript:">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/placeholder-csv-format.svg') }}"
                                    alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>
                            <a id="export-pdf" class="dropdown-item" href="javascript:">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ asset('public/assets/admin/svg/components/pdf.svg') }}"
                                    alt="Image Description">
                                {{ translate('messages.pdf') }}
                            </a>
                        </div>
                    </div>
                    <!-- End Unfold -->

                    <!-- Unfold -->
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white h--40px" href="javascript:"
                            data-hs-unfold-options='{
                                    "target": "#showHideDropdown",
                                    "type": "css-animation"
                                }'>
                            <i class="tio-table mr-1"></i> {{ translate('messages.column') }} <span
                                class="badge badge-soft-dark rounded-circle ml-1"></span>
                        </a>

                        <div id="showHideDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.order') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_order">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_order"
                                                checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.date') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_date">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_date"
                                                checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.customer') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_customer">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_customer"
                                                checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2 text-capitalize">{{ translate('messages.total_amount') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_payment_status">
                                            <input type="checkbox" class="toggle-switch-input"
                                                id="toggleColumn_payment_status" checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="mr-2">{{ translate('messages.order_status') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_order_status">
                                            <input type="checkbox" class="toggle-switch-input"
                                                id="toggleColumn_order_status" checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="mr-2">{{ translate('messages.actions') }}</span>

                                        <!-- Checkbox Switch -->
                                        <label class="toggle-switch toggle-switch-sm" for="toggleColumn_actions">
                                            <input type="checkbox" class="toggle-switch-input" id="toggleColumn_actions"
                                                checked>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <!-- End Checkbox Switch -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Header -->
        <div class="card-body p-0">
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                    "order": [],
                    "orderCellsTop": true,
                    "paging":false
                }'>
                    <thead class="thead-light">
                        <tr>
                            {{-- <th class="border-0">{{ translate('messages.#') }}</th> --}}
                            {{-- <th class="border-0 table-column-pl-0">{{ translate('messages.cart_id') }}</th> --}}
                            <th class="border-0">{{ translate('messages.voucher') }}</th>

                            <th class="border-0">{{ translate('messages.date') }}</th>
                            <th class="border-0">{{ translate('messages.customer_information') }}</th>
                            <th class="border-0">{{ translate('messages.type') }}</th>
                            <th class="border-0">{{ translate('messages.total_amount') }}</th>
                            <th class="border-0">{{ translate('messages.discount_amount') }}</th>
                            <th class="border-0">{{ translate('messages.amount_paid') }}</th>
                            <th class="border-0 text-center">{{ translate('messages.status') }}</th>
                            <th class="border-0 text-center">{{ translate('messages.actions') }}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                        @foreach ($orders as $key => $cart)
                            <tr class="status-{{ $cart->status }} class-all">
                                {{-- <td>{{ $key + 1 }}</td> --}}

                                {{-- <td class="table-column-pl-0">
                                    <a href="#">{{ $cart->id }}</a>
                                </td> --}}
                            <td>
                                    {!! wordwrap(ucwords($cart->item->name), 10, "<br>", true) !!}
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($cart->created_at)->format('d M Y') }}</div>
                                    <div class="d-block text-uppercase">
                                        {{ \Carbon\Carbon::parse($cart->created_at)->format('h:i A') }}
                                    </div>
                                </td>

                                <td>
                                    @if ($cart->is_guest)
                                        @php($customer = json_decode($cart->delivery_address ?? '{}', true))
                                        <strong>{{ $customer['contact_person_name'] ?? 'Guest' }}</strong>
                                        <div>{{ $customer['contact_person_number'] ?? '-' }}</div>
                                    @elseif($cart->user)
                                        <strong>{{ $cart->user->f_name . ' ' . $cart->user->l_name }}</strong>
                                        <div>{{ $cart->user->phone }}</div>
                                    @else
                                        <label
                                            class="badge badge-danger">{{ translate('messages.invalid_customer_data') }}</label>
                                    @endif
                                </td>



                                <td> Instant Discount</td>

                                <td>    {{ \App\CentralLogics\Helpers::format_currency($cart->total_price) }}</td>
                                <td>   {{ \App\CentralLogics\Helpers::format_currency($cart->discount_amount) }} </td>
                                <td>
                                    <div class="text-right mw--85px">
                                        {{ \App\CentralLogics\Helpers::format_currency($cart->price) }}
                                    </div>
                                </td>


                                <td class="text-capitalize text-center">
                                    @if ($cart->status == 'pending')
                                        <span class="badge badge-soft-info">{{ translate('messages.pending') }}</span>
                                    @elseif($cart->status == 'approved')
                                        <span class="badge badge-soft-success">{{ translate('messages.approved') }}</span>
                                    @elseif($cart->status == 'rejected')
                                        <span class="badge badge-soft-danger">{{ translate('messages.rejected') }}</span>
                                    @else
                                        <span class="badge badge-soft-secondary">{{ $cart->status ?? '-' }}</span>
                                    @endif
                                </td>

                                <td>
                                    @if($cart->status == 'pending')
                                        <div class="btn--container justify-content-center">

                                            <!-- View -->
                                            {{-- <a class="btn btn-sm btn--warning btn-outline-warning action-btn"
                                                href="{{ route('vendor.flate.order.list', ['status' => 'all']) }}">
                                                <i class="tio-visible-outlined"></i>

                                            </a> --}}


                                            <!-- Approve -->
                                            <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                                href="{{ route('vendor.flate.order.update-status', ['id' => $cart->id, 'status' => 'approved']) }}">
                                                <i class="tio-done"></i>
                                            </a>

                                            <!-- Reject -->
                                            <a class="btn btn-sm btn--danger btn-outline-danger action-btn"
                                                href="{{ route('vendor.flate.order.update-status', ['id' => $cart->id, 'status' => 'rejected']) }}">
                                                <i class="tio-clear-circle"></i>
                                            </a>

                                        </div>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if (count($orders) === 0)
                    <div class="empty--data">
                        <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                        <h5>{{ translate('no_data_found') }}</h5>
                    </div>
                @endif
            </div>

            <!-- End Table -->
        </div>
        <!-- Footer -->
        <div class="card-footer">
            {{-- {!! $orders->links() !!} --}}
        </div>
        <!-- End Footer -->
    </div>
    <!-- End Card -->

@endsection

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function() {

            let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none',
                        action: function() {
                            window.location.href =
                                '{{ route('vendor.order.export', ['status' => $status, 'file_type' => 'excel', 'type' => 'order', request()->getQueryString()]) }}';
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'd-none',
                        action: function() {
                            window.location.href =
                                '{{ route('vendor.order.export', ['status' => $status, 'file_type' => 'csv', 'type' => 'order', request()->getQueryString()]) }}';
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'd-none'
                    },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                        '<img class="w-7rem mb-3" src="{{ asset('public/assets/admin') }}/svg/illustrations/sorry.svg" alt="Image Description">' +

                        '</div>'
                }
            });

            $('#export-copy').click(function() {
                datatable.button('.buttons-copy').trigger()
            });

            $('#export-excel').click(function() {
                datatable.button('.buttons-excel').trigger()
            });

            $('#export-csv').click(function() {
                datatable.button('.buttons-csv').trigger()
            });

            $('#export-pdf').click(function() {
                datatable.button('.buttons-pdf').trigger()
            });

            $('#export-print').click(function() {
                datatable.button('.buttons-print').trigger()
            });

            $('#toggleColumn_order').change(function(e) {
                datatable.columns(1).visible(e.target.checked)
            })

            $('#toggleColumn_date').change(function(e) {
                datatable.columns(2).visible(e.target.checked)
            })

            $('#toggleColumn_customer').change(function(e) {
                datatable.columns(3).visible(e.target.checked)
            })

            $('#toggleColumn_payment_status').change(function(e) {
                datatable.columns(4).visible(e.target.checked)
            })

            $('#toggleColumn_order_status').change(function(e) {
                datatable.columns(5).visible(e.target.checked)
            })

            $('#toggleColumn_actions').change(function(e) {
                datatable.columns(6).visible(e.target.checked)
            })

        });
    </script>
@endpush
