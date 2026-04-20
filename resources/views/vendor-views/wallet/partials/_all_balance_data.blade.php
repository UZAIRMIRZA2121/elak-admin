<div class="row g-3">


    <?php
    
    $disbursement_type = \App\Models\BusinessSetting::where('key', 'disbursement_type')->first()->value ?? 'manual';
    $min_amount_to_pay_store = \App\Models\BusinessSetting::where('key', 'min_amount_to_pay_store')->first()->value ?? 0;
    
    $pending_withdraw = 0;
    $total_withdrawn = 0;
    $total_earning = 0;
    
    $pending_withdraw = $wallets->sum('pending_withdraw');
    $total_withdrawn = $wallets->sum('total_withdrawn');
    $total_earning = $wallets->sum('total_earning');
    
    $total_balance = $total_earning - ($total_withdrawn + $pending_withdraw);
    
    $wallet_earning = round($total_earning - ($total_withdrawn + $pending_withdraw), 8);
    
    $digital_payment = App\CentralLogics\Helpers::get_business_settings('digital_payment');
    $digital_payment = $digital_payment['status'];
    
    ?>






    <div class="col-md-12">
        <div class="row g-3">
            <!-- Earnings (Monthly) Card Example -->
              <div class="col-sm-3">
                <div class="resturant-card shadow--card-2">
                    <h4 class="title">
                        {{ \App\CentralLogics\Helpers::format_currency($total_balance > 0 ? $total_balance : 0) }}
                    </h4>
                    <span class="subtitle">{{ translate('messages.withdraw_able_balance') }}</span>
                    <img class="resturant-icon"
                        src="{{ asset('/public/assets/admin/img/transactions/image_w_balance.png') }}" alt="public">
                </div>
            </div>


            <!-- Panding Withdraw Card Example -->
            <div class="col-sm-3">
                <div class="resturant-card  bg--3">
                    <h4 class="title">{{ \App\CentralLogics\Helpers::format_currency($pending_withdraw) }}
                    </h4>
                    <span class="subtitle">{{ translate('messages.pending_withdraw') }}</span>
                    <img class="resturant-icon"
                        src="{{ asset('/public/assets/admin/img/transactions/image_pending.png') }}" alt="public">
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-sm-3">
                <div class="resturant-card  bg--2">
                    <h4 class="title">{{ \App\CentralLogics\Helpers::format_currency($total_withdrawn) }}</h4>
                    <span class="subtitle">{{ translate('messages.Total_Withdrawn') }}</span>
                    <img class="resturant-icon"
                        src="{{ asset('/public/assets/admin/img/transactions/image_withdaw.png') }}" alt="public">
                </div>
            </div>


            <!-- Pending Requests Card Example -->
            <div class="col-sm-3">
                <div class="resturant-card  bg--1">
                    <h4 class="title">{{ \App\CentralLogics\Helpers::format_currency($total_earning) }}</h4>
                    <span class="subtitle">{{ translate('messages.total_earning') }}</span>
                    <img class="resturant-icon"
                        src="{{ asset('/public/assets/admin/img/transactions/image_total89.png') }}" alt="public">
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="balance-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ translate('messages.withdraw_request') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true" class="btn btn--circle btn-soft-danger text-danger"><i
                            class="tio-clear"></i></span>
                </button>
            </div>

            <form id="withdraw_form" action="{{ route('vendor.wallet.all.withdraw-request') }}" method="post">
                <input type="text" name="vendor_ids" value="{{ $vendorIds }}">

                <div class="modal-body">
                    @csrf
                    <div class="">
                        <select class="form-control" id="withdraw_method" name="withdraw_method" required>
                            <option value="" selected disabled>{{ translate('Select_Withdraw_Method') }}
                            </option>
                            @foreach ($withdrawal_methods as $item)
                                <option value="{{ $item['id'] }}">{{ $item['method_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="" id="method-filed__div">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="amount" value="{{ abs($total_balance) }}"
                            value="{{ abs($total_balance) }}" min="1" max="{{ abs($total_balance) }}">
                        <div class="col-sm-12 my-3">
                            <div class="resturant-card  bg--2">
                                <h4 class="title">
                                    {{ \App\CentralLogics\Helpers::format_currency($total_balance) }}</h4>
                                <span class="subtitle">{{ translate('messages.total_balance') }}</span>
                                <img class="resturant-icon"
                                    src="{{ asset('/public/assets/admin/img/transactions/image_withdaw.png') }}"
                                    alt="public">
                            </div>
                            <p class="text-danger">{{ translate('messages.its your all branches balance') }}</p>
                        </div>

                    </div>
                </div>
                <div class="modal-footer pt-0 border-0">
                    <button type="button" class="btn btn--reset"
                        data-dismiss="modal">{{ translate('messages.cancel') }}</button>
                    <button type="submit" id="set_disable" id="submit_button"
                        class="btn btn--primary">{{ translate('messages.Submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('messages.Note') }}: </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <div class="modal-body">

                <div class="form-group">
                    <p id="hiddenValue"> </p>
                </div>
            </div>
            <div class="modal-footer">
                <button id="reset_btn" type="reset" data-dismiss="modal"
                    class="btn btn-secondary">{{ translate('Close') }} </button>
            </div>
        </div>
    </div>
</div>
<!-- Content Row -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">

                <ul class="nav nav-tabs page-header-tabs pb-2">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('vendor-panel/wallet') ? 'active' : '' }}"
                            href="{{ route('vendor.wallet.all.balances') }}">{{ translate('messages.all_store_balances') }}</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link  {{ Request::is('vendor-panel/wallet/wallet-payment-list') ? 'active' : '' }}"
                            href="{{ route('vendor.wallet.wallet_payment_list') }}"
                            aria-disabled="true">{{ translate('messages.Payment_history') }}</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link  {{ Request::is('vendor-panel/wallet/disbursement-list') ? 'active' : '' }}"
                            href="{{ route('vendor.wallet.getDisbursementList') }}"
                            aria-disabled="true">{{ translate('messages.Next_Payouts') }}</a>
                    </li>
                </ul>

            </div>
