
        

                        <div class="col-sm-6 col-lg-4">
                            <div class="__dashboard-card-2">
                                <h6 class="name">{{ translate('messages.orders') }}</h6>
                                <h3 class="count">{{ $data['total_orders'] }}</h3>
                                <div class="subtxt">{{ $data['new_orders'] }} {{ translate('newly added') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="__dashboard-card-2">
                                <h6 class="name">{{ translate('Partner Stores') }}</h6>
                                <h3 class="count">{{ $data['total_stores'] }}</h3>
                                <div class="subtxt">{{ $data['new_stores'] }} {{ translate('newly added') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="__dashboard-card-2">

                                <h6 class="name">{{ translate('messages.customers') }}</h6>
                                <h3 class="count">{{ $data['total_customers'] }}</h3>
                                <div class="subtxt">{{ $data['new_customers'] }} {{ translate('newly added') }}</div>
                            </div>
                        </div>


                        <div class="col-sm-6 col-lg-3">
                            <div class="__dashboard-card-2">

                                <h6 class="name">{{ translate('messages.all_vouchers') }}</h6>
                                <h3 class="count">{{ $data['total_items'] }}</h3>
                                <div class="subtxt">{{ $data['new_items'] }} {{ translate('newly added') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="__dashboard-card-2">

                                <h6 class="name">{{ translate('messages.active_vouchers') }}</h6>
                                <h3 class="count">{{ $data['total_active_voucher'] }}</h3>
                                <div class="subtxt">{{ $data['new_active_voucher'] }} {{ translate('newly added') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="__dashboard-card-2">

                                <h6 class="name">{{ translate('messages.redeemed_vouchers') }}</h6>
                                <h3 class="count">{{ $data['total_redeemed_voucher'] }}</h3>
                                <div class="subtxt">{{ $data['new_redeemed_voucher'] }} {{ translate('newly added') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="__dashboard-card-2">

                                <h6 class="name">{{ translate('messages.expired_vouchers') }}</h6>
                                <h3 class="count">{{ $data['total_expired_voucher'] }}</h3>
                                <div class="subtxt">{{ $data['new_expired_voucher'] }} {{ translate('newly added') }}
                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="row g-2">
                                <div class="col-sm-6 col-lg-2">
                                    <a class="order--card h-100 d-flex flex-column justify-content-between"
                                        href="{{ route('admin.order.list', ['searching_for_deliverymen']) }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                <span>{{ translate('messages.pending') }}</span>
                                            </h6>
                                            <span class="card-title text-3F8CE8">
                                                {{ $data['pending_in_rs'] }}
                                            </span>
                                        </div>

                                        <div class="mt-auto">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ min($data['pending_in_rs'], $data['total_orders']) }}%; background-color: #3F8CE8;"
                                                    aria-valuenow="{{ $data['pending_in_rs'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>

                                            </div>
                                            <div class="d-flex justify-content-end mt-1">
                                                <small
                                                    class="text-muted">{{ $data['pending_in_rs'] }}/{{ $data['total_orders'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-2">
                                    <a class="order--card h-100 d-flex flex-column justify-content-between"
                                        href="{{ route('admin.order.list', ['searching_for_deliverymen']) }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">

                                                <span>{{ translate('messages.processing') }}</span>
                                            </h6>
                                            <span class="card-title text-3F8CE8">
                                                {{ $data['preparing_in_rs'] }}
                                            </span>
                                        </div>

                                        <div class="mt-auto">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ min($data['preparing_in_rs'], $data['total_orders']) }}%; background-color: #3F8CE8;"
                                                    aria-valuenow="{{ $data['preparing_in_rs'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>

                                            </div>
                                            <div class="d-flex justify-content-end mt-1">
                                                <small class="text-muted">
                                                    {{ $data['preparing_in_rs'] }}/{{ $data['total_orders'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <a class="order--card h-100 d-flex flex-column justify-content-between"
                                        href="{{ route('admin.order.list', ['searching_for_deliverymen']) }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6
                                                class="card-subtitle d-flex justify-content-between m-0 align-items-center">

                                                <span>{{ translate('messages.completed') }}</span>
                                            </h6>
                                            <span class="card-title text-3F8CE8">
                                                {{ $data['delivered'] }}
                                            </span>
                                        </div>

                                        <div class="mt-auto">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ min($data['delivered'], $data['total_orders']) }}%; background-color: #3F8CE8;"
                                                    aria-valuenow="{{ $data['delivered'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-1">
                                                <small class="text-muted">
                                                    {{ $data['delivered'] }}/{{ $data['total_orders'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-6 col-lg-2">
                                    <a class="order--card h-100 d-flex flex-column justify-content-between"
                                        href="{{ route('admin.order.list', ['searching_for_deliverymen']) }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6
                                                class="card-subtitle d-flex justify-content-between m-0 align-items-center">

                                                <span>{{ translate('messages.canceled') }}</span>
                                            </h6>
                                            <span class="card-title text-3F8CE8">
                                                {{ $data['canceled'] }}
                                            </span>
                                        </div>

                                        <div class="mt-auto">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ min($data['canceled'], $data['total_orders']) }}%; background-color: #3F8CE8;"
                                                    aria-valuenow=" {{ $data['canceled'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-1">
                                                <small class="text-muted">
                                                    {{ $data['canceled'] }}/{{ $data['total_orders'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-6 col-lg-2">
                                    <a class="order--card h-100 d-flex flex-column justify-content-between"
                                        href="{{ route('admin.order.list', ['searching_for_deliverymen']) }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6
                                                class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                                <span>{{ translate('messages.refunded') }}</span>
                                            </h6>
                                            <span class="card-title text-3F8CE8">
                                                {{ $data['refunded'] }}
                                            </span>
                                        </div>

                                        <div class="mt-auto">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ min($data['refunded'], $data['total_orders']) }}%; background-color: #3F8CE8;"
                                                    aria-valuenow=" {{ $data['refunded'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-1">
                                                <small class="text-muted">
                                                    {{ $data['refunded'] }}/{{ $data['total_orders'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>






                            </div>
                        </div>

            <!-- End Stats -->