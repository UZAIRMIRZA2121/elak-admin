@extends('layouts.client.app')
@section('title',"Client Dashboard")

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
                     {{ translate('messages.CUSTOMER USERS') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select name="group" class="form-control js-select2-custom">
                            <option value="" selected disabled>{{ translate('messages.Select Group') }}</option>
                            <option value="all">{{ translate('messages.All Groups') }}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                       <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group">
                                <input type="search" name="search" class="form-control" placeholder="{{ translate('messages.Enter the name') }}" value="{{ $search ?? '' }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn--primary">
                                        <i class="tio-filter-list"></i> {{ translate('messages.Filter') }}
                                    </button>
                                    <button type="reset" class="btn btn--secondary location-reload-to-base" data-url="{{ route('all_user.user_data') }}">
                                        {{ translate('messages.Reset') }}
                                    </button>
                                </div>
                            </div>
                       </form>
                    </div>
                    <!-- <div class="col-md-3">
                        <div class="btn--container justify-content-end">
                            <button class="btn btn-outline-primary"><i class="tio-download-to"></i> {{ translate('messages.Download') }}</button>
                            <button class="btn btn-primary"><i class="tio-add"></i> {{ translate('messages.Created') }}</button>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ translate('messages.UUID') }}</th>
                                <th class="border-0">{{ translate('messages.the name') }}</th>
                                <th class="border-0">{{ translate('messages.user name') }}</th>
                                <th class="border-0">{{ translate('messages.Jawwal Points') }}</th>
                                <th class="border-0">{{ translate('messages.Enjoy Points') }}</th>
                                <th class="border-0">{{ translate('messages.Total Points') }}</th>
                                <th class="border-0">{{ translate('messages.Last Active City') }}</th>
                                <th class="border-0">{{ translate('messages.Last Active City Date') }}</th>
                                <th class="border-0 text-center">{{ translate('messages.Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->f_name }} {{ $user->l_name }}</td>
                                <td>{{ $user->username ?? $user->email }}</td>
                                <td>{{ $user->loyalty_point ?? 0 }}</td>
                                <td>{{ $user->wallet_balance ?? 0 }}</td>
                                <td>{{ ($user->loyalty_point ?? 0) + ($user->wallet_balance ?? 0) }}</td>
                                <td>{{ $user->last_active_city ?? '-' }}</td>
                                <td>{{ $user->updated_at ? $user->updated_at->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <button class="btn btn-sm btn-outline-primary">{{ translate('messages.Amendment') }}</button>
                                        <button class="btn btn-sm btn-outline-info">{{ translate('messages.Redeems') }} (0)</button>
                                        <button class="btn btn-sm btn-outline-success"><i class="tio-lock-opened"></i> {{ translate('messages.words.unlock') }}</button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="tio-delete"></i> {{ translate('messages.delete') }}</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(count($users) !== 0)
            <div class="card-footer">
                {!! $users->withQueryString()->links() !!}
            </div>
            @endif
            @if(count($users) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>{{translate('no_data_found')}}</h5>
            </div>
            @endif
        </div>
    </div>
@endsection
