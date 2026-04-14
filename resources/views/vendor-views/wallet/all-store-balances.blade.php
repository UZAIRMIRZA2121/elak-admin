@php
    $vendorData = \App\CentralLogics\Helpers::get_store_data();
    $title = $vendorData?->module_type == 'rental' && addon_published_status('Rental') ? 'Provider' : 'Store';
@endphp

@extends('layouts.vendor.app')
@section('title', translate('messages.' . $title . '_wallet'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h2 class="page-header-title text-capitalize">
                        <div class="card-header-icon d-inline-flex mr-2 img">
                            <img src="{{ asset('/public/assets/admin/img/image_90.png') }}" alt="public">
                        </div>
                        <span>
                            {{ translate('messages.' . $title . '_wallet') }}
                        </span>
                    </h2>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
      
          <?php
  
        $wallets = \App\Models\StoreWallet::whereIn('vendor_id', $vendorIds)->get();
     
        ?>
 
        @include('vendor-views.wallet.partials._all_balance_data', ['wallets' => $wallets , 'vendorIds' => $vendorIds])

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true,
                                    "paging":false
                                }'>
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('messages.sl') }}</th>
                            <th>{{ translate('messages.store_name') }}</th>
                            <th>{{ translate('messages.total_earning') }}</th>
                            <th>{{ translate('messages.pending_withdraw') }}</th>
                            <th>{{ translate('messages.total_withdrawn') }}</th>
                            <th>{{ translate('messages.total_balance') }}</th>

                            {{-- <th class="w-5px">{{ translate('messages.Action') }}</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($all_stores as $store)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $store->name }} {{ $store->id }}</td>

                                <td>
                                    {{ \App\CentralLogics\Helpers::format_currency($store->vendor->wallet->total_earning ?? 0) }}
                                </td>

                                <td>
                                    {{ \App\CentralLogics\Helpers::format_currency($store->vendor->wallet->pending_withdraw ?? 0) }}
                                </td>

                                <td>
                                    {{ \App\CentralLogics\Helpers::format_currency($store->vendor->wallet->total_withdrawn ?? 0) }}
                                </td>

                                <td>
                                    {{ \App\CentralLogics\Helpers::format_currency(
                                        ($store->vendor->wallet->total_earning ?? 0) -
                                            ($store->vendor->wallet->pending_withdraw ?? 0) -
                                            ($store->vendor->wallet->total_withdrawn ?? 0),
                                    ) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if (count($withdraw_req) === 0)
                    <div class="empty--data">
                        <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                        <h5>
                            {{ translate('no_data_found') }}
                        </h5>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-footer pt-0 border-0">
            {{ $withdraw_req->links() }}
        </div>
    </div>

    <div class="modal fade" id="payment_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('messages.Pay_Via_Online') }} </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <form action="{{ route('vendor.wallet.make_payment') }}" method="POST" class="needs-validation">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" value="{{ \App\CentralLogics\Helpers::get_store_id() }}" name="store_id" />
                      <h5 class="mb-5 ">{{ translate('Pay_Via_Online') }} &nbsp;
                            <small>({{ translate('Faster_&_secure_way_to_pay_bill') }})</small>
                        </h5>
                        <div class="row g-3">
                            @forelse ($data as $item)
                                <div class="col-sm-6">
                                    <div class="d-flex gap-3 align-items-center">
                                        <input type="radio" required id="{{ $item['gateway'] }}" name="payment_gateway"
                                            value="{{ $item['gateway'] }}">
                                        <label for="{{ $item['gateway'] }}" class="d-flex align-items-center gap-3 mb-0">
                                            <img height="24"
                                                src="{{ asset('storage/app/public/payment_modules/gateway_image/' . $item['gateway_image']) }}"
                                                alt="">
                                            {{ $item['gateway_title'] }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <h1>{{ translate('no_payment_gateway_found') }}</h1>
                            @endforelse
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button id="reset_btn" type="reset" data-dismiss="modal"
                            class="btn btn-secondary">{{ translate('Close') }} </button>
                        <button type="submit" class="btn btn-primary">{{ translate('Proceed') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>


    <div class="modal fade" id="Adjust_wallet" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('messages.Adjust_Wallet') }} </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <form action="{{ route('vendor.wallet.make_wallet_adjustment') }}" method="POST" class="needs-validation">
                    <div class="modal-body">
                        @csrf
                        <h5 class="mb-5 ">{{ translate('This_will_adjust_the_collected_cash_on_your_earning') }} </h5>
                    </div>

                    <div class="modal-footer">
                        <button id="reset_btn" type="reset" data-dismiss="modal"
                            class="btn btn-secondary">{{ translate('Close') }} </button>
                        <button type="submit" class="btn btn-primary">{{ translate('Proceed') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/view-pages/vendor/wallet-method.js"></script>
    <script>
        "use strict";
        $('#withdraw_method').on('change', function() {
            $('#submit_button').attr("disabled", "true");
            let method_id = this.value;

            // Set header if need any otherwise remove setup part
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('vendor.wallet.method-list') }}" + "?method_id=" + method_id,
                data: {},
                processData: false,
                contentType: false,
                type: 'get',
                success: function(response) {
                    $('#submit_button').removeAttr('disabled');
                    let method_fields = response.content.method_fields;
                    $("#method-filed__div").html("");
                    method_fields.forEach((element, index) => {
                        $("#method-filed__div").append(`
                    <div class="form-group mt-2">
                        <label for="wr_num" class="fz-16 text-capitalize c1 mb-2">${element.input_name.replaceAll('_', ' ')}</label>
                        <input type="${element.input_type == 'phone' ? 'number' : element.input_type  }" class="form-control" name="${element.input_name}" placeholder="${element.placeholder}" ${element.is_required === 1 ? 'required' : ''}>
                    </div>
                `);
                    })

                },
                error: function() {

                }
            });
        });

        $('.payment-warning').on('click', function(event) {
            event.preventDefault();
            toastr.info(
                "{{ translate('messages.Currently,_there_are_no_payment_options_available._Please_contact_admin_regarding_any_payment_process_or_queries.') }}", {
                    CloseButton: true,
                    ProgressBar: true
                });
        });
        $(document).ready(function() {
            $("#withdraw_form").on("submit", function(event) {
                $('#set_disable').attr('disabled', true);
            });
        });
    </script>
@endpush
