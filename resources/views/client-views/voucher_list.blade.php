@extends('layouts.client.app')
@section('title', translate('Voucher List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/people.png')}}" class="w--26" alt="">
                </span>
                <span>
                     {{ translate('messages.Voucher List') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{translate('messages.ref_code')}}</label>
                                <input type="text" name="ref_code" value="{{ $ref_code }}" class="form-control" placeholder="{{translate('messages.Ex:')}} S123">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{translate('messages.segment')}}</label>
                                <select name="segment_id" class="form-control js-select2-custom">
                                    <option value="all">{{translate('messages.all')}}</option>
                                    @foreach($segments as $segment)
                                        <option value="{{ $segment->id }}" {{ $segment_id == $segment->id ? 'selected' : '' }}>{{ $segment->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{translate('messages.partner')}}</label>
                                <select name="partner_id" class="form-control js-select2-custom">
                                    <option value="all">{{translate('messages.all')}}</option>
                                    @foreach($partners as $partner)
                                        <option value="{{ $partner->id }}" {{ $partner_id == $partner->id ? 'selected' : '' }}>{{ $partner->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{translate('messages.zone')}}</label>
                                <select name="zone_id_filter" class="form-control js-select2-custom">
                                    <option value="all">{{translate('messages.all')}}</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ $zone_id_filter == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{translate('messages.category')}}</label>
                                <select name="category_id" class="form-control js-select2-custom">
                                    <option value="all">{{translate('messages.all')}}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">&nbsp;</label>
                                <div class="btn--container justify-content-end">
                                    <button type="submit" class="btn btn--primary">{{translate('messages.filter')}}</button>
                                    <a href="{{ route('all_user.voucher_list') }}" class="btn btn--secondary">{{translate('messages.clear')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ translate('messages.sl') }}</th>
                                <th class="border-0">{{ translate('messages.ref_code') }}</th>
                                <th class="border-0">{{ translate('messages.segment') }}</th>
                                <th class="border-0">{{ translate('messages.partner') }}</th>
                                <th class="border-0">{{ translate('messages.city') }}</th>
                                <th class="border-0">{{ translate('messages.major_city') }}</th>
                                <th class="border-0">{{ translate('messages.voucher_desc') }}</th>
                                <th class="border-0">{{ translate('messages.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $key => $order)
                            <tr>
                                <td>{{ $key + $orders->firstItem() }}</td>
                                <td>
                                    @if ($order->customer)
                                        <strong>{{ $order->customer['ref_code'] }}</strong>
                                    @else
                                        <label class="badge badge-danger">{{ translate('messages.invalid_customer_data') }}</label>
                                    @endif
                                </td>
                                <td>{{ $order->customer && $order->customer->segment ? $order->customer->segment->name : '-' }}</td>
                                <td>{{ $order->store ? $order->store->name : '-' }}</td>
                                <td>{{ $order->zone ? $order->zone->name : '-' }}</td>
                                <td>{{ $order->zone ? $order->zone->display_name ?? $order->zone->name : '-' }}</td>
                                <td>{{ $order->voucher()->item->description ?? '-' }}</td>
                                <td>
                                    {{ \App\CentralLogics\Helpers::date_format($order->created_at) }}
                                    <span class="d-block text-uppercase">
                                        {{ \App\CentralLogics\Helpers::time_format($order->created_at) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($orders) !== 0)
            <div class="card-footer">
                {!! $orders->withQueryString()->links() !!}
            </div>
            @endif
            @if(count($orders) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>{{translate('no_data_found')}}</h5>
            </div>
            @endif
        </div>
    </div>
@endsection
