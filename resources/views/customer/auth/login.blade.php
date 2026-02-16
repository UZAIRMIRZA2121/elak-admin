@extends('layouts.customer.app')

@section('title', translate('messages.customer_login'))

@section('content')
    <!-- Content -->
    <div class="container py-5 py-sm-7">
        @php($logo=\App\Models\BusinessSetting::where(['key'=>'icon'])->first())
        <a class="d-flex justify-content-center mb-5" href="javascript:">
            <img class="z-index-2" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" src="{{\App\CentralLogics\Helpers::get_full_url('business', $logo?->value?? '', $logo?->storage[0]?->value ?? 'public','favicon')}}" alt="Image Description" style="max-height: 100px; max-width: 300px">
        </a>

        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <!-- Card -->
                <div class="card card-lg mb-5">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="mb-5">
                                <h1 class="display-4">{{translate('messages.customer_sign_in')}}</h1>
                            </div>
                        </div>

                        <!-- Form -->
                        <form action="{{route('customer.auth.login.submit')}}" method="post">
                            @csrf
                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signinSrEmail">{{translate('messages.email')}}</label>
                                <input type="email" class="form-control" name="email" id="signinSrEmail" tabindex="1" placeholder="email@address.com" aria-label="email@address.com" required data-msg="Please enter a valid email address.">
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signupSrPassword" tabindex="0">
                                    <span class="d-flex justify-content-between align-items-center">
                                        {{translate('messages.password')}}
                                    </span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="js-toggle-password form-control" name="password" id="signupSrPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'6+'])}}" required data-msg="{{translate('messages.invalid_password_warning')}}">
                                    <div class="js-toggle-password-target-2 input-group-append">
                                        <a class="input-group-text" href="javascript:;">
                                            <i class="js-toggle-passowrd-show-icon-2 tio-visible-outlined"></i>
                                            <i class="js-toggle-passowrd-hide-icon-2 d-none tio-hidden-outlined"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Checkbox -->
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="remember">
                                    <label class="custom-control-label text-muted" for="termsCheckbox"> {{translate('messages.remember_me')}}</label>
                                </div>
                            </div>
                            <!-- End Checkbox -->

                            <button type="submit" class="btn btn-lg btn-block btn-primary">{{translate('messages.sign_in')}}</button>
                        </form>
                        <!-- End Form -->
                    </div>
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
    <!-- End Content -->
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF SHOW PASSWORD
            // =======================================================
            $('.js-toggle-password').each(function () {
                new HSTogglePassword(this).init();
            });

            // INITIALIZATION OF FORM VALIDATION
            // =======================================================
            $('.js-validate').each(function () {
                $.HSCore.components.HSValidation.init($(this));
            });
        });
    </script>
@endpush
