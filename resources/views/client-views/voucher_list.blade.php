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
                        <div class="col-md-2">
                            <input type="text" name="search" class="form-control" placeholder="{{ translate('messages.Enter username') }}" value="{{ $search ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="store_name" class="form-control" placeholder="{{ translate('messages.Company Name') }}" value="{{ $store_name ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <select name="zone_id" class="form-control js-select2-custom">
                                <option value="" selected disabled>{{ translate('messages.Select City') }}</option>
                                <option value="all">{{ translate('messages.All Cities') }}</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ isset($zone_id) && $zone_id == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="classification" class="form-control js-select2-custom">
                                <option value="" selected disabled>{{ translate('messages.Select Classification') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-control js-select2-custom">
                                <option value="" selected disabled>{{ translate('messages.Select Category') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--secondary location-reload-to-base" data-url="{{ route('all_user.voucher_list') }}">
                                    {{ translate('messages.Reset') }}
                                </button>
                                <button type="submit" class="btn btn--primary">
                                    <i class="tio-filter-list"></i> {{ translate('messages.Filter') }}
                                </button>
                                <!-- <button type="button" class="btn btn-outline-primary">
                                    <i class="tio-download-to"></i> {{ translate('messages.Download') }}
                                </button> -->
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
                                <th class="border-0">{{ translate('messages.Username') }}</th>
                                <th class="border-0">{{ translate('messages.Classification') }}</th>
                                <th class="border-0">{{ translate('messages.Company Name') }}</th>
                                <th class="border-0">{{ translate('messages.City') }}</th>
                                <th class="border-0">{{ translate('messages.major cities') }}</th>
                                <th class="border-0">{{ translate('messages.Description') }}</th>
                                <th class="border-0">{{ translate('messages.Avg. Rating') }}</th>
                                <th class="border-0">{{ translate('messages.Visits') }}</th>
                                <th class="border-0">{{ translate('messages.Edited in') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                            <tr>
                                <td>{{ $order->customer ? ($order->customer->username ?? $order->customer->f_name) : translate('messages.Guest') }}</td>
                                <td>{{ $order->customer && $order->customer->segment ? $order->customer->segment->name : '-' }}</td>
                                <td>{{ $order->store ? $order->store->name : '-' }}</td>
                                <td>{{ $order->zone ? $order->zone->name : '-' }}</td>
                                <td>{{ $order->zone ? $order->zone->name : '-' }}</td>
                                <td>{{ $order->order_note ?? translate('messages.No Description') }}</td>
                                 <td>{{ $order->store ? \App\CentralLogics\StoreLogic::calculate_store_rating($order->store->rating)['rating'] : '-' }}</td>
                                <td>{{ $order->store ? $order->store->order_count : 0 }}</td>
                                <td>{{ $order->created_at }}</td>
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
