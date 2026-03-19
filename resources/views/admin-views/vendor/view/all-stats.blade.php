@extends('layouts.admin.app')

@section('title', $store->name . "'s " . translate('messages.items'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/admin/css/croppie.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        @include('admin-views.vendor.view.partials._header', ['store' => $store])


        <!-- Page Heading -->

        <div class="tab-content">
            <div class="tab-pane fade show active" id="product">

                <div class="col-12 mb-3">

                    <div class="row g-3 text-capitalize">

                        <!-- Collected Cash -->
                        <div class="col-md-4">
                            <div class="card h-100 card--bg-1">
                                <div
                                    class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                    <h5 class="cash--subtitle text-white">
                                        {{ translate('messages.collected_cash_by_store') }}
                                    </h5>

                                    <div class="d-flex align-items-center justify-content-center mt-3">
                                        <div class="cash-icon mr-3">
                                            <img src="{{ asset('public/assets/admin/img/cash.png') }}" alt="img">
                                        </div>

                                        <h2 class="cash--title text-white">
                                            {{ \App\CentralLogics\Helpers::format_currency($totals['collected_cash']) }}
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="row g-3">

                                <!-- Pending Withdraw -->
                                <div class="col-sm-6">
                                    <div class="resturant-card card--bg-2">
                                        <h4 class="title">
                                            {{ \App\CentralLogics\Helpers::format_currency($totals['pending_withdraw']) }}
                                        </h4>
                                        <div class="subtitle">{{ translate('messages.pending_withdraw') }}</div>
                                    </div>
                                </div>

                                <!-- Total Withdrawn -->
                                <div class="col-sm-6">
                                    <div class="resturant-card card--bg-3">
                                        <h4 class="title">
                                            {{ \App\CentralLogics\Helpers::format_currency($totals['total_withdrawn']) }}
                                        </h4>
                                        <div class="subtitle">{{ translate('messages.total_withdrawal_amount') }}</div>
                                    </div>
                                </div>

                                <!-- Balance -->
                                <div class="col-sm-6">
                                    <div class="resturant-card card--bg-4">
                                        <h4 class="title">
                                            {{ \App\CentralLogics\Helpers::format_currency($totals['balance'] > 0 ? $totals['balance'] : 0) }}
                                        </h4>
                                        <div class="subtitle">{{ translate('messages.withdraw_able_balance') }}</div>
                                    </div>
                                </div>

                                <!-- Total Earning -->
                                <div class="col-sm-6">
                                    <div class="resturant-card card--bg-1">
                                        <h4 class="title">
                                            {{ \App\CentralLogics\Helpers::format_currency($totals['total_earning']) }}
                                        </h4>
                                        <div class="subtitle">{{ translate('messages.total_earning') }}</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>


                </div>



                <div class="card">
                    <div class="card-header border-0 py-2">
                        <div class="search--button-wrapper">
                            <h3 class="card-title"> {{ translate('messages.stores') }} <span
                                    class="badge badge-soft-dark ml-2"><span
                                        class="total_items">{{ $branches->count() }}</span></span>
                            </h3>

                            <form class="search-form">
                                <input type="hidden" name="store_id" value="{{ $store->id }}">
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch" name="search" value="{{ request()?->search ?? null }}"
                                        type="search" class="form-control h--40px"
                                        placeholder="{{ translate('Search by name...') }}"
                                        aria-label="{{ translate('messages.search_here') }}">
                                    <button type="submit" class="btn btn--secondary h--40px"><i
                                            class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>

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


                                </div>
                            </div>
                            <!-- End Unfold -->

                        </div>
                    </div>
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
                                    <th>{{ translate('messages.sr') }}</th>
                                    <th>{{ translate('messages.name') }}</th>
                                    <th>{{ translate('messages.total_earning') }}</th>
                                    <th>{{ translate('messages.total_withdrawn') }}</th>
                                    <th>{{ translate('messages.pending_withdraw') }}</th>
                                    <th>{{ translate('messages.collected_cash') }}</th>
                                    <th>{{ translate('messages.balance') }}</th>
                                    <th>{{ translate('messages.action') }}</th>
                                </tr>
                            </thead>

                            <tbody id="setrows">
                                @foreach ($branches as $key => $branch)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            {{ $branch->name }}

                                            @if (empty($branch->parent_id))
                                                <span class="badge badge-success">Main</span>
                                            @else
                                                <span class="badge badge-danger">Sub-Branch</span>
                                            @endif
                                        </td>
                                        @php
                                            $wallet = $branch->vendor->wallet ?? null;
                                        @endphp

                                        <td>{{ \App\CentralLogics\Helpers::format_currency($wallet->total_earning ?? 0) }}
                                        </td>
                                        <td>{{ \App\CentralLogics\Helpers::format_currency($wallet->total_withdrawn ?? 0) }}
                                        </td>
                                        <td>{{ \App\CentralLogics\Helpers::format_currency($wallet->pending_withdraw ?? 0) }}
                                        </td>
                                        <td>{{ \App\CentralLogics\Helpers::format_currency($wallet->collected_cash ?? 0) }}
                                        </td>
                                        <td>{{ \App\CentralLogics\Helpers::format_currency($wallet && $wallet->balance > 0 ? $wallet->balance : 0) }}
                                        </td>

                                        <td>
                                            <a class="btn action-btn btn--primary btn-outline-primary" href="#"
                                                title="{{ translate('messages.view_transaction') }}"> <i class="tio-visible"></i>
                                            </a>
                                        </td>


                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>

                    <div class="page-area">

                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection

@push('script_2')
@endpush
