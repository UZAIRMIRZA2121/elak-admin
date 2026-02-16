@extends('layouts.customer.app')

@section('title', translate('messages.dashboard'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('messages.welcome')}}, {{auth('customer')->user()->name}}.</h1>
                    <p class="page-header-text">{{translate('messages.hello_here_is_your_overview')}}</p>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                <!-- Card -->
                <a class="card card-hover-shadow h-100" href="#">
                    <div class="card-body">
                        <h6 class="card-subtitle">{{translate('messages.client_id')}}</h6>
                        <div class="row align-items-center gx-2 mb-1">
                            <div class="col-6">
                                <span class="card-title h2">{{auth('customer')->user()->id}}</span>
                            </div>
                        </div>
                    </div>
                </a>
                <!-- End Card -->
            </div>

            {{-- Stats temporarily disabled as Client model differs from User model
            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                <a class="card card-hover-shadow h-100" href="#">
                    <div class="card-body">
                        <h6 class="card-subtitle">{{translate('messages.wallet_balance')}}</h6>
                        <div class="row align-items-center gx-2 mb-1">
                            <div class="col-6">
                                <span class="card-title h2">{{\App\CentralLogics\Helpers::format_currency(auth('customer')->user()->wallet_balance)}}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            --}}
        </div>
    </div>
@endsection
