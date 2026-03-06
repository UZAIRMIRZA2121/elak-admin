@extends('layouts.vendor.app')

@section('title', '')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style type="text/css" media="print">
        @page {
            size: auto;
            /* auto is the initial value */
            margin: 0;
            /* this affects the margin in the printer settings */
        }
    </style>
@endpush


@section('content')

    @if ($order->voucher_type == 'Flat discount')
        @include('admin-views.order.partials._invoice_flat')
    @elseif ($order->voucher_type == 'In-Store' || $order->voucher_type == 'Delivery/Pickup')
        @if ($order->voucher_sub_type == 'bogo_free')
            @include('admin-views.order.partials._invoice_bogo')
        @elseif (
            $order->voucher_sub_type == 'simple' ||
                $order->voucher_sub_type == 'simple x' ||
                $order->voucher_sub_type == 'bundle')
            @include('admin-views.order.partials._invoice_simple')
        @elseif ($order->voucher_sub_type == 'mix_match')
            @include('admin-views.order.partials._invoice_mix_match')
        @endif
    @elseif ($order->voucher_type == 'Gift')
    @include('admin-views.order.partials._invoice_gift')
    @else
        @include('admin-views.order.partials._invoice')
    @endif

@endsection

@push('script')
    <script>
        "use strict";

        function printDiv(divName) {
            let printContents = document.getElementById(divName).innerHTML;
            let originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endpush
