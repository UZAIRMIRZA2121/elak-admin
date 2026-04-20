<div class="row">
    <div class="col-lg-12 text-center "><h1 >{{ translate('order_transactions_report') }}</h1></div>
    <div class="col-lg-12">



    <table>
        <thead>
            <tr>
                <th>{{ translate('Search_Criteria') }}</th>
                <th></th>
                <th></th>
                <th>
                    {{ translate('module' )}} - {{ $data['module']?translate($data['module']):translate('all') }}
                    <br>
                    {{ translate('zone' )}} - {{ $data['zone']??translate('all') }}
                    <br>
                    {{ translate('store' )}} - {{ $data['store']??translate('all') }}
                    @if ($data['from'])
                    <br>
                    {{ translate('from' )}} - {{ $data['from']?Carbon\Carbon::parse($data['from'])->format('d M Y'):'' }}
                    @endif
                    @if ($data['to'])
                    <br>
                    {{ translate('to' )}} - {{ $data['to']?Carbon\Carbon::parse($data['to'])->format('d M Y'):'' }}
                    @endif
                    <br>
                    {{ translate('filter')  }}- {{  translate($data['filter']) }}
                    <br>
                    {{ translate('Search_Bar_Content')  }}- {{ $data['search'] ??translate('N/A') }}

                </th>
                <th> </th>
                <th></th>
                <th></th>
                <th></th>
                </tr>
            <tr>
                <th>{{ translate('Transaction_Analytics') }}</th>
                <th></th>
                <th></th>
                <th>
                    {{ translate('Completed_Transactions')  }}- {{ $data['delivered'] ??translate('N/A') }}
                    <br>
                    {{ translate('Refunded_Transactions')  }}- {{ $data['canceled'] ??translate('N/A') }}
                </th>
                <th> </th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <th>{{ translate('Earning_Analytics') }}</th>
                <th></th>
                <th></th>
                <th>
                    {{ translate('Admin_Earnings')  }} - {{ $data['admin_earned'] ??translate('N/A') }}
                    <br>
                    {{ translate('Store_Earnings')  }} - {{ $data['store_earned'] ??translate('N/A') }}
                    <br>
                    {{ translate('Delivery_Man_Earnings')  }} - {{ $data['deliveryman_earned'] ??translate('N/A') }}
                </th>
                <th> </th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        <tr>
            <th class="border-0">{{ translate('sl') }}</th>
                                <th class="border-0">{{ translate('messages.order_id') }}</th>
                                <th class="border-0">{{ translate('messages.store') }}</th>
                                <th class="border-0">{{ translate('messages.customer_name') }}</th>

                                <th class="border-0">{{ translate('messages.voucher_value') }}</th>
                                <th class="border-0">{{ translate('messages.discount_amount') }}</th>
                                <th class="border-0">{{ translate('messages.paid_amount') }}</th>

                                <th class="border-0">{{ translate('messages.viza_commission') }}</th>
                                <th class="min-w-140 text-capitalize">{{ translate('balance') }}</th>
                                <th class="border-0 min-w-120">{{ translate('messages.amount_received_by') }}</th>
                                <th class="border-top border-bottom text-capitalize">
                                    {{ translate('messages.payment_method') }}</th>
                                <th class="border-0">{{ translate('messages.payment_status') }}</th>
        </thead>
        <tbody>
        @foreach($data['order_transactions'] as $key => $ot)
            <tr>
                <td>{{ $key + 1 }}</td>
                                    @if ($ot->order->order_type == 'parcel')
                                        <td><a
                                                href="{{ route('admin.transactions.parcel.order.details', $ot->order_id) }}">{{ $ot->order_id }}</a>
                                        </td>
                                    @else
                                        <td><a
                                                href="{{ route('admin.transactions.order.details', $ot->order_id) }}">{{ $ot->order_id }}</a>
                                        </td>
                                    @endif
                                    <td class="text-capitalize">
                                        @if ($ot->order->store)
                                            {{ Str::limit($ot->order->store->name, 25, '...') }}
                                        @else
                                            <label
                                                class="badge badge-soft-success white-space-nowrap">{{ translate('messages.parcel_order') }}
                                        @endif
                                    </td>
                                    <td class="white-space-nowrap">
                                        @if ($ot->order->customer)
                                            <a class="text-body text-capitalize"
                                                href="{{ route('admin.users.customer.view', [$ot->order['user_id']]) }}">
                                                <strong>{{ $ot->order->customer['f_name'] . ' ' . $ot->order->customer['l_name'] }}</strong>
                                            </a>
                                        @else
                                            <label
                                                class="badge badge-danger">{{ translate('messages.invalid_customer_data') }}</label>
                                        @endif
                                    </td>
                                    <td class="white-space-nowrap">
                                        {{ \App\CentralLogics\Helpers::format_currency($ot->order['total_order_amount']) }}
                                    </td>
                                    <td class="white-space-nowrap">
                                        {{ \App\CentralLogics\Helpers::format_currency($ot->order['discount_amount']) }}
                                    </td>

                                    {{-- total_item_amount --}}
                                    <td class="white-space-nowrap">
                                        {{ \App\CentralLogics\Helpers::format_currency($ot->order['order_amount']) }}
                                    </td>
                                    {{-- admin_commission --}}
                                    <td class="white-space-nowrap">
                                        {{ \App\CentralLogics\Helpers::format_currency($ot->admin_commission + $ot->admin_expense - $ot->delivery_fee_comission - $ot->additional_charge - $ot->order['flash_admin_discount_amount']) }}
                                    </td>


                        
                                    {{-- store_net_income --}}
                                    <td class="white-space-nowrap">
                                        {{ \App\CentralLogics\Helpers::format_currency($ot->store_amount - ($ot?->order?->order_type == 'parcel' ? 0 : $ot->tax)) }}
                                    </td>
                                         {{-- store_net_income --}}
                                    <td class="white-space-nowrap">
                                      {{ $ot->received_by }}
                                    </td>

                                    <td class="mw--85px text-capitalize min-w-120 ">
                                        {{ translate(str_replace('_', ' ', $ot->order['payment_method'])) }}
                                    </td>
                                    <td class="text-capitalize white-space-nowrap">
                                        @if ($ot->status)
                                            <span class="badge badge-soft-danger">
                                                {{ translate('messages.refunded') }}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-success">
                                                {{ translate('messages.completed') }}
                                            </span>
                                        @endif
                                    </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
