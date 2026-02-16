<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    @php($logo=\App\Models\BusinessSetting::where(['key'=>'icon'])->first())
    <link rel="icon" type="image/x-icon" href="{{\App\CentralLogics\Helpers::get_full_url('business', $logo?->value?? '', $logo?->storage[0]?->value ?? 'public','favicon')}}">
    <link href="{{asset('public/assets/admin/css/fonts.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/style.css')}}">
    @stack('css_or_js')
    <script src="{{asset('public/assets/admin/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/toastr.css')}}">
</head>

<body class="footer-offset">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="initial-hidden">
                <div class="loader--inner">
                    <img width="200" src="{{asset('public/assets/admin/img/loader.gif')}}" alt="image">
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    @include('layouts.customer.partials._header')
    <!-- End Header -->

    <!-- Sidebar -->
    @if(auth('customer')->check())
        @include('layouts.customer.partials._sidebar')
    @endif
    <!-- End Sidebar -->

    <main id="content" role="main" class="main pointer-event">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.admin.partials._footer')
    <!-- End Footer -->

    <script src="{{asset('public/assets/admin/js/custom.js')}}"></script>
    <script src="{{asset('public/assets/admin/js/vendor.min.js')}}"></script>
    <script src="{{asset('public/assets/admin/js/theme.min.js')}}"></script>
    <script src="{{asset('public/assets/admin/js/sweet_alert.js')}}"></script>
    <script src="{{asset('public/assets/admin/js/toastr.js')}}"></script>
    {!! Toastr::message() !!}

    @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
            toastr.error('{{$error}}', Error, {
                CloseButton: true,
                ProgressBar: true
            });
            @endforeach
        </script>
    @endif

    <script>
        $(window).on('load', function () {
            // Sidebar Initialization
            if (typeof HSNavbarVerticalAside !== 'undefined') {
                $('.js-navbar-vertical-aside-toggle-invoker').click(function () {
                    $('.js-navbar-vertical-aside').toggleClass('show-sidebar');
                });
            
                // Initialize the sidebar
                $('.js-navbar-vertical-aside').each(function () {
                    var sidebar = new HSNavbarVerticalAside($(this)).init();
                });
            } else {
                // Fallback if HSNavbarVerticalAside is not loaded
                $('.js-navbar-vertical-aside-toggle-invoker').click(function () {
                     $('.js-navbar-vertical-aside').toggleClass('show-sidebar');
                });
            }

            // INITIALIZATION OF UNFOLD
            // =======================================================
            if (typeof HSUnfold !== 'undefined') {
                $(".js-hs-unfold-invoker").each(function () {
                    let unfold = new HSUnfold($(this)).init();
                });
            } else {
                console.error('HSUnfold is not defined. Make sure theme.min.js is included.');
            }
        });
    </script>

    @stack('script')
    @stack('script_2')
</body>
</html>
