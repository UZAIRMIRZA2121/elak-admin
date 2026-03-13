    <div class="card mb-2">
                        <!-- Header -->
                        <div class="card-header justify-content-center text-center px-0 mx-4">
                            <h5 class="card-header-title text-capitalize">
                                <span>{{ translate('messages.order_setup') }}</span>
                            </h5>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->

                        <div class="card-body">
                            <!-- Order Status Flow Starts -->
                            @php($order_delivery_verification = (bool) \App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value)
                            <div class="mb-4">
                                <div class="row g-1">
                                    <div class="{{ config('canceled_by_store') ? 'col-6' : 'col-12' }} d-none">
                                        <a class="btn btn--primary w-100 fz--13 px-2 {{ $order['order_status'] == 'pending' ? '' : 'd-none' }} route-alert"
                                            data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'confirmed']) }}"
                                            data-message="{{ translate('messages.confirm_this_order_?') }}"
                                            href="javascript:">{{ translate('messages.confirm_this_order') }}</a>
                                    </div>
                                    @if (config('canceled_by_store'))
                                        <div class="col-6">
                                            <a
                                                class="btn btn--danger w-100 fz--13 px-2 cancelled-status {{ $order['order_status'] == 'pending' ? '' : 'd-none' }}">{{ translate('Cancel Order') }}</a>
                                        </div>
                                    @endif
                                </div>
                                @if (($order->store && $order->store->module->module_type == 'food') || $order->store->module->module_type == 'voucher')
                                    <a class="btn btn--primary w-100 order-status-change-alert {{ $order['order_status'] == 'pending' || $order['order_status'] == 'accepted' ? '' : 'd-none' }}"
                                        data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'processing']) }}"
                                        data-message="{{ translate('Change status to cooking ?') }}"
                                        data-verification="false" data-processing-time="{{ $max_processing_time }}"
                                        href="javascript:">{{ translate('messages.proceed_for_processing') }}</a>
                                @else
                                    <a class="btn btn--primary w-100 route-alert  {{ $order['order_status'] == 'confirmed' || $order['order_status'] == 'accepted' ? '' : 'd-none' }}"
                                        data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'processing']) }}"
                                        data-message="{{ translate('messages.proceed_for_processing') }}"
                                        href="javascript:">{{ translate('messages.proceed_for_processing') }}</a>
                                @endif
                                <a class="btn btn--primary w-100 route-alert {{ $order['order_status'] == 'processing' ? '' : 'd-none' }}"
                                    data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'handover']) }}"
                                    data-message="{{ translate('messages.make_ready_for_handover') }}"
                                    href="javascript:">{{ translate('messages.make_ready_for_handover') }}</a>
                                @if ( $order['order_status'] == 'handover' || ($order['order_status'] == 'picked_up' && $order->store->sub_self_delivery == 1))
                                    <a class="btn  w-100
                                    {{ $order['order_type'] == 'take_away' || $order->store->sub_self_delivery == 1 ? 'btn--primary order-status-change-alert' : 'btn--secondary  self-delivery-warning' }} "
                                        data-url="{{ route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'delivered']) }}"
                                        data-message="{{ translate('messages.Change status to delivered (payment status will be paid if not)?') }}"
                                        data-verification="{{ $order_delivery_verification ? 'true' : 'false' }}"
                                        href="javascript:">{{ translate('messages.make_delivered') }}</a>
                                @endif

                            </div>
                        </div>

                        <!-- End Body -->
                    </div>