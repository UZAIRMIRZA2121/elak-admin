@extends('layouts.client.app')
@section('title', translate('Edit User'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('/public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                     {{ translate('messages.Edit User') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-body">
                <form action="{{ route('all_user.user_update', [$user->id]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="f_name">{{ translate('messages.first_name') }}</label>
                                <input type="text" name="f_name" class="form-control" placeholder="{{ translate('messages.first_name') }}" value="{{ $user->f_name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="l_name">{{ translate('messages.last_name') }}</label>
                                <input type="text" name="l_name" class="form-control" placeholder="{{ translate('messages.last_name') }}" value="{{ $user->l_name }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="username">{{ translate('messages.user name') }}</label>
                                <input type="text" name="username" class="form-control" placeholder="{{ translate('messages.user name') }}" value="{{ $user->username }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="password">{{ translate('messages.password') }} ({{ translate('messages.leave_blank_if_not_want_to_change') }})</label>
                                <input type="password" name="password" class="form-control" placeholder="{{ translate('messages.password') }}">
                            </div>
                        </div>
                    </div>

                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--secondary">{{ translate('messages.Reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
