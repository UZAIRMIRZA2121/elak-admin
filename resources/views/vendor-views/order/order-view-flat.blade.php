@extends('layouts.vendor.app')

@section('title', translate('messages.Order Details'))


@section('content')
    <?php
    
    $tax_included = 0;
    if (count($order->details) > 0) {
        $campaign_order = isset($order?->details[0]?->item_campaign_id) ? true : false;
    }
    $max_processing_time = explode('-', $order['store']['delivery_time'])[0];
    ?>
    <?php
    
  
    
    ?>

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <span class="page-header-icon">
                            <img src="{{ asset('/public/assets/admin/img/shopping-basket.png') }}" class="w--20"
                                alt="">
                        </span>
                        <span>
                            {{ translate('order_details') }} <span
                                class="badge badge-soft-dark rounded-circle ml-1">{{ $order->details->count() }}</span>
                        </span>
                    </h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                        href="{{ route('vendor.order.details', [$order['id'] - 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Previous order">
                        <i class="tio-chevron-left"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                        href="{{ route('vendor.order.details', [$order['id'] + 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Next order">
                        <i class="tio-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header border-0 align-items-start flex-wrap">
                        <div class="order-invoice-left d-flex d-sm-flex justify-content-between">
                            <div>
                                <h1 class="page-header-title">
                                    {{ translate('messages.order') }} #{{ $order['id'] }}

                                    @if ($order->edited)
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.edited') }}
                                        </span>
                                    @endif
                                </h1>
                                <span class="mt-2 d-block">
                                    <i class="tio-date-range"></i>
                                    {{ date('d M Y ' . config('timeformat'), strtotime($order['created_at'])) }}
                                </span>
                                @if ($order->schedule_at && $order->scheduled)
                                    <h6 class="text-capitalize">
                                        {{ translate('messages.scheduled_at') }}
                                        : <label
                                            class="fz--10 badge badge-soft-warning">{{ date('d M Y ' . config('timeformat'), strtotime($order['schedule_at'])) }}</label>
                                    </h6>
                                @endif
                                @if ($order['cancellation_reason'])
                                    <h6>
                                        <span class="text-danger">{{ translate('messages.order_cancellation_reason') }}
                                            :</span>
                                        {{ $order['cancellation_reason'] }}
                                    </h6>
                                @endif
                                @if ($order['unavailable_item_note'])
                                    <h6 class="w-100 badge-soft-warning">
                                        <span class="text-dark">
                                            {{ translate('messages.order_unavailable_item_note') }} :
                                        </span>
                                        {{ $order['unavailable_item_note'] }}
                                    </h6>
                                @endif
                                @if ($order['delivery_instruction'])
                                    <h6 class="w-100 badge-soft-warning">
                                        <span class="text-dark">
                                            {{ translate('messages.order_delivery_instruction') }} :
                                        </span>
                                        {{ $order['delivery_instruction'] }}
                                    </h6>
                                @endif
                                @if ($order['order_note'])
                                    <h6>
                                        {{ translate('messages.order_note') }} :
                                        {{ $order['order_note'] }}
                                    </h6>
                                @endif
                            </div>
                            <div class="d-sm-none">
                                <a class="btn btn--primary print--btn font-regular"
                                    href={{ route('vendor.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i>
                                    <span>{{ translate('messages.print_invoice') }}</span>
                                </a>
                            </div>
                        </div>


                        <div class="order-invoice-right mt-3 mt-sm-0">
                            <div class="btn--container ml-auto align-items-center justify-content-end">
                                <a class="btn btn--primary print--btn font-regular d-none d-sm-block"
                                    href={{ route('vendor.order.generate-invoice', [$order['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i>
                                    <span>{{ translate('messages.print_invoice') }}</span>
                                </a>
                            </div>
                            <div class="text-right mt-3 order-invoice-right-contents text-capitalize">
                                <h6>
                                    {{ translate('messages.payment_status') }} :
                                    @if ($order['payment_status'] == 'paid')
                                        <span class="badge badge-soft-success ml-sm-3">
                                            {{ translate('messages.paid') }}
                                        </span>
                                    @elseif ($order['payment_status'] == 'partially_paid')
                                        @if ($order->payments()->where('payment_status', 'unpaid')->exists())
                                            <span class="text-danger">{{ translate('messages.partially_paid') }}</span>
                                        @else
                                            <span class="text-success">{{ translate('messages.paid') }}</span>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.unpaid') }}
                                        </span>
                                    @endif
                                </h6>
                                @if ($order->store && $order->store->module->module_type == 'food')
                                    <h6>
                                        <span>{{ translate('cutlery') }}</span> <span>:</span>
                                        @if ($order['cutlery'] == '1')
                                            <span class="badge badge-soft-success ml-sm-3">
                                                {{ translate('messages.yes') }}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-danger ml-sm-3">
                                                {{ translate('messages.no') }}
                                            </span>
                                        @endif

                                    </h6>
                                @endif
                                <h6 class="text-capitalize">
                                    {{ translate('messages.payment_method') }} :
                                    {{ translate(str_replace('_', ' ', $order['payment_method'])) }}
                                </h6>
                                @if ($order['transaction_reference'])
                                    <h6 class="">
                                        {{ translate('messages.reference_code') }} :
                                        <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                            data-target=".bd-example-modal-sm">
                                            {{ translate('messages.add') }}
                                        </button>
                                    </h6>
                                @endif
                                <h6 class="text-capitalize">{{ translate('messages.order_type') }}
                                    : <label
                                        class="fz--10 badge m-0 badge-soft-primary">{{ translate(str_replace('_', ' ', $order['order_type'])) }}</label>
                                </h6>
                                <h6>
                                    {{ translate('messages.order_status') }} :
                                    @if ($order['order_status'] == 'pending')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.pending') }}
                                        </span>
                                    @elseif($order['order_status'] == 'confirmed')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.confirmed') }}
                                        </span>
                                    @elseif($order['order_status'] == 'processing')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.processing') }}
                                        </span>
                                    @elseif($order['order_status'] == 'picked_up')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.out_for_delivery') }}
                                        </span>
                                    @elseif($order['order_status'] == 'delivered')
                                        <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.delivered') }}
                                        </span>
                                    @elseif($order['order_status'] == 'failed')
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.payment_failed') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ str_replace('_', ' ', $order['order_status']) }}
                                        </span>
                                    @endif
                                </h6>
                                   <h6>
                                    {{ translate('messages.voucher_type') }} :
                                    @if ($order['voucher_type'] == 'Flat discount')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.flat_discount') }}
                                        </span>
                                    @endif
                                   
                                </h6>
                                @if ($order->order_attachment)
                                    @php
                                        $order_images = json_decode($order->order_attachment, true);
                                    @endphp
                                    {{-- @if (is_array($order_images)) --}}
                                    <h5 class="text-dark">
                                        {{ translate('messages.prescription') }}:
                                    </h5>
                                    <div class="d-flex flex-wrap flex-md-row-reverse __gap-15px">
                                        @foreach ($order_images as $key => $item)
                                            @php($item = is_array($item) ? $item : ['img' => $item, 'storage' => 'public'])
                                            <div>
                                                <button class="btn w-100 px-0" data-toggle="modal"
                                                    data-target="#prescriptionimagemodal{{ $key }}"
                                                    title="{{ translate('messages.order_attachment') }}">
                                                    <div class="gallary-card ml-auto">
                                                        <img src="{{ \App\CentralLogics\Helpers::get_full_url('order', $item['img'], $item['storage']) }}"
                                                            alt="{{ translate('messages.prescription') }}"
                                                            class="initial--22 object-cover">
                                                    </div>
                                                </button>
                                            </div>
                                            <div class="modal fade" id="prescriptionimagemodal{{ $key }}"
                                                tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel">
                                                                {{ translate('messages.prescription') }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"><span
                                                                    aria-hidden="true">&times;</span><span
                                                                    class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src="{{ \App\CentralLogics\Helpers::get_full_url('order', $item['img'], $item['storage']) }}"
                                                                class="initial--22 w-100" alt="image">
                                                        </div>
                                                        @php($storage = $item['storage'] ?? 'public')
                                                        @php($file = $storage == 's3' ? base64_encode('order/' . $item['img']) : base64_encode('public/order/' . $item['img']))
                                                        <div class="modal-footer">
                                                            <a class="btn btn-primary"
                                                                href="{{ route('admin.file-manager.download', [$file, $storage]) }}"><i
                                                                    class="tio-download"></i>
                                                                {{ translate('messages.download') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body px-0">
                        <?php
                        $total_addon_price = 0;
                        $product_price = 0;
                        $store_discount_amount = 0;
                        $admin_flash_discount_amount = $order['flash_admin_discount_amount'];
                        $ref_bonus_amount = $order['ref_bonus_amount'];
                        $extra_packaging_amount = $order['extra_packaging_amount'];
                        $store_flash_discount_amount = $order['flash_store_discount_amount'];
                        
                        if ($order->prescription_order == 1) {
                            $product_price = $order['order_amount'] - $order['delivery_charge'] - $order['total_tax_amount'] - $order['dm_tips'] - $order['additional_charge'] + $order['store_discount_amount'];
                            if ($order->tax_status == 'included') {
                                $product_price += $order['total_tax_amount'];
                            }
                        }
                        
                        $total_addon_price = 0;
                        ?>
                        <div class="table-responsive">
                            <table
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer mb-0">
                             
                              
                                    @foreach ($order->details as $key => $detail)
                                        @if (isset($detail->item_id))

                                            @php($detail->item = json_decode($detail->item_details, true))
                                            @php($product = \App\Models\Item::where(['id' => $detail->item['id']])->first())
                                            <!-- Media -->
                                            <tr>
                                             
                                                <td>
                                                    <div class="media media--sm">
                                                        <a class="avatar avatar-xl mr-3"
                                                            href="{{ route('vendor.item.view', $detail->item['id']) }}">
                                                            <img class="img-fluid rounded onerror-image"
                                                                src="{{ $product->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                                data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                                alt="Image Description">
                                                        </a>
                                                        <div class="media-body">
                                                            <div>
                                                                <strong class=" h1">#{{$detail->item['name'] }}</strong>
                                                            
                                                             
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                             
                                            </tr>
                                     
                                        @endif
                                    @endforeach
                               
                            </table>
                        </div>
                        <div class="mx-3">
                            <hr>
                        </div>
                      
                        <div class="row justify-content-md-end mb-3 mx-0 mt-4">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-right">
                                     <dt class="col-6">{{ translate('messages.offer_type') }}:</dt>
                                    <dd class="col-6">   
                                         <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.instant_discount') }}
                                        </span></dd>
                                    <dt class="col-6">{{ translate('messages.total_price') }}:</dt>
                                    <dd class="col-6">{{ \App\CentralLogics\Helpers::format_currency(value: $order->total_order_amount) }} </dd>
                                      <dt class="col-6">{{ translate('messages.discount') }}:</dt>
                                    <dd class="col-6">-{{ \App\CentralLogics\Helpers::format_currency(value: $order->discount_amount) }} </dd>

                                    <dt class="col-6">{{ translate('messages.total_paid_amount') }}:</dt>
                                    <dd class="col-6">{{ \App\CentralLogics\Helpers::format_currency(value: $order->order_amount) }} 
                                    </dd>
                                
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4">
            
           @if($order->voucher_type === 'Gift' && !empty($order->details[0]['gift_details']))
                    <?php $gift = $order->details[0]['gift_details']; ?>
                <!-- order proof -->
                <div class="card mb-2 mt-2">
                    <div class="card-header border-0 text-center pb-0">
                        <h4 class="m-0">{{ translate('messages.Gift Details') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="media align-items-center">
                            <div class="delivery--information-single d-block">

                                <div class="d-flex mb-1">
                                    <span class="name mr-2">{{ translate('messages.Occasion') }}:</span>
                                    <span class="info">{{ $gift['occasion'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex mb-1">
                                    <span class="name mr-2">{{ translate('messages.Sender') }}:</span>
                                    <span class="info">{{ $gift['sender_name'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex mb-1">
                                    <span class="name mr-2">{{ translate('messages.Recipient Name') }}:</span>
                                    <span class="info">{{ $gift['recipient_name'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex mb-1">
                                    <span class="name mr-2">{{ translate('messages.Recipient Email') }}:</span>
                                    <span class="info">{{ $gift['recipient_email'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex mb-1">
                                    <span class="name ">{{ translate('messages.Message') }}:</span>
                                    <span class="info">{{ $gift['message'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex mb-1">
                                    <span class="name mr-2">{{ translate('messages.Delivery Time') }}:</span>
                                    <span class="info">{{ $gift['delivery_time'] ?? '-' }}</span>
                                </div>

                                <div class="d-flex mb-1">
                                    <span class="name mr-2">{{ translate('messages.Amount') }}:</span>
                                    <span class="info">{{ $gift['amount'] ?? '-' }}</span>
                                </div>
                            

                                @if (!empty($gift['image']))
                                    <div class="d-flex mt-2">
                                        <span class="name mr-2">{{ translate('messages.Image') }}:</span>
                                        <img src="{{ asset($gift['image']) }}" alt="Gift Image" class="img-fluid"
                                            style="max-height:100px;">
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    </div>
                   
                @endif


                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">
                            <span class="card-header-icon"><i class="tio-user"></i></span>
                            <span>{{ translate('messages.customer') }}</span>
                        </h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    @if ($order->customer)
                        <div class="card-body">

                            <div class="media align-items-center customer--information-single" href="javascript:">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img onerror-image "
                                        data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                        src="{{ $order->customer->image_full_url }}" alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span
                                        class="text-body d-block text-hover-primary mb-1">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-shopping-basket-outlined mr-2"></i>
                                        {{ $order->customer->orders_count }}
                                        {{ translate('messages.orders_delivered') }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        {{ $order->customer['phone'] }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-email-outlined mr-2"></i>
                                        {{ $order->customer['email'] }}
                                    </span>

                                </div>
                            </div>
                            <hr>




                            @if ($order->delivery_address)
                                @php($address = json_decode($order->delivery_address, true))
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{ translate('messages.delivery_info') }}</h5>
                                </div>
                                @if (isset($address))
                                    <span class="delivery--information-single d-block">
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.name') }}:</span>
                                            <span class="info">{{ $address['contact_person_name'] }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.contact') }}:</span>
                                            <a class="info deco-none"
                                                href="tel:{{ $address['contact_person_number'] }}">
                                                {{ $address['contact_person_number'] }}</a>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('Floor') }}:</span>
                                            <span
                                                class="info">{{ isset($address['floor']) ? $address['floor'] : '' }}</span>
                                        </div>

                                        <div class="d-flex mb-2">
                                            <span class="name">{{ translate('House') }}:</span>
                                            <span
                                                class="info">{{ isset($address['house']) ? $address['house'] : '' }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('Road') }}:</span>
                                            <span
                                                class="info">{{ isset($address['road']) ? $address['road'] : '' }}</span>
                                        </div>
                                        @if ($order['order_type'] != 'take_away' && isset($address['address']))
                                            @if (isset($address['latitude']) && isset($address['longitude']))
                                                <a target="_blank"
                                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                    <i class="tio-map"></i>{{ $address['address'] }}<br>
                                                </a>
                                            @else
                                                <i class="tio-map"></i>{{ $address['address'] }}<br>
                                            @endif
                                        @endif
                                    </span>
                                @endif
                            @endif
                        </div>
                    @elseif($order->is_guest)
                        <div class="card-body">
                            <span class="badge badge-soft-success py-2 mb-2 d-block qcont">
                                {{ translate('Guest_user') }}
                            </span>
                            @if ($order->delivery_address)
                                @php($address = json_decode($order->delivery_address, true))
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{ translate('messages.delivery_info') }}</h5>
                                </div>
                                @if (isset($address))
                                    <span class="delivery--information-single d-block">
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.name') }}:</span>
                                            <span class="info">{{ $address['contact_person_name'] }}</span>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('messages.contact') }}:</span>
                                            <a class="info deco-none"
                                                href="tel:{{ $address['contact_person_number'] }}">
                                                {{ $address['contact_person_number'] }}</a>
                                        </div>
                                        <div class="d-flex">
                                            <span class="name">{{ translate('Floor') }}:</span>
                                            <span
                                                class="info">{{ isset($address['floor']) ? $address['floor'] : '' }}</span>
                                        </div>

                                        <div class="d-flex mb-2">
                                            <span class="name">{{ translate('House') }}:</span>
                                            <span
                                                class="info">{{ isset($address['house']) ? $address['house'] : '' }}</span>
                                        </div>

                                        <div class="d-flex">
                                            <span class="name">{{ translate('Road') }}:</span>
                                            <span
                                                class="info">{{ isset($address['road']) ? $address['road'] : '' }}</span>
                                        </div>

                                        @if ($order['order_type'] != 'take_away' && isset($address['address']))
                                            <hr>
                                            @if (isset($address['latitude']) && isset($address['longitude']))
                                                <a target="_blank"
                                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                                    <i class="tio-map"></i>{{ $address['address'] }}<br>
                                                </a>
                                            @else
                                                <i class="tio-map"></i>{{ $address['address'] }}<br>
                                            @endif
                                        @endif
                                    </span>
                                @endif
                            @endif

                        </div>
                    @else
                        <div class="card-body">
                            <span class="badge badge-soft-danger py-2 d-block qcont">
                                {{ translate('Customer Not found!') }}
                            </span>
                        </div>
                    @endif
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        <!-- End Row -->
    </div>



    <!-- Modal -->
    <div class="modal fade order-proof-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{ translate('messages.add_delivery_proof') }}
                    </h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                        aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{ route('vendor.order.add-order-proof', [$order['id']]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Input Group -->
                        <div class="flex-grow-1 mx-auto">

                            <div class="d-flex flex-wrap __gap-12px __new-coba" id="coba">
                                @php($proof = isset($order->order_proof) ? json_decode($order->order_proof, true) : 0)
                                @if ($proof)

                                    @foreach ($proof as $key => $photo)
                                        @php($photo = is_array($photo) ? $photo : ['img' => $photo, 'storage' => 'public'])

                                        <div class="spartan_item_wrapper min-w-176px max-w-176px">
                                            <img class="img--square"
                                                src="{{ \App\CentralLogics\Helpers::get_full_url('order', $photo['img'], $photo['storage']) }}"
                                                alt="order image">

                                            <div class="pen spartan_remove_row"><i class="tio-edit"></i></div>
                                            <a href="{{ route('vendor.order.remove-proof-image', ['id' => $order['id'], 'name' => $photo]) }}"
                                                class="spartan_remove_row"><i class="tio-add-to-trash"></i></a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <!-- End Input Group -->
                        <div class="text-right mt-2">
                            <button class="btn btn--primary">{{ translate('messages.submit') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Modal -->

    <div class="modal fade" id="edit-order-amount" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.update_order_amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.order.update-order-amount') }}" method="POST" class="row">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="form-group col-12">
                            <label for="order_amount">{{ translate('messages.order_amount') }}</label>
                            <input id="order_amount" type="number" class="form-control" name="order_amount"
                                min="0"
                                value="{{ round($order['order_amount'] - $order['total_tax_amount'] - $order['additional_charge'] - $order['delivery_charge'] + $order['store_discount_amount'] - $order['dm_tips'], 6) }}"
                                step=".01">
                        </div>

                        <div class="form-group col-sm-12">
                            <button class="btn btn-sm btn-primary"
                                type="submit">{{ translate('messages.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-discount-amount" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('messages.update_discount_amount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.order.update-discount-amount') }}" method="POST" class="row">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="form-group col-12">
                            <label for="discount_amount">{{ translate('messages.discount_amount') }}</label>
                            <input type="number" id="discount_amount" class="form-control" name="discount_amount"
                                min="0" value="{{ $order['store_discount_amount'] }}" step=".01">
                        </div>

                        <div class="form-group col-sm-12">
                            <button class="btn btn-sm btn-primary"
                                type="submit">{{ translate('messages.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- End Content -->


@endsection
@push('script_2')
    <script src="{{ asset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script type="text/javascript">
        "use strict";


        $('.self-delivery-warning').on('click', function(event) {
            event.preventDefault();
            toastr.info(
                "{{ translate('messages.Self_Delivery_is_Disable') }}", {
                    CloseButton: true,
                    ProgressBar: true
                });
        });



        $('.cancelled-status').on('click', function() {
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: '{{ translate('messages.Change status to canceled ?') }}',
                type: 'warning',
                html: `   <select class="form-control js-select2-custom mx-1" name="reason" id="reason">
                    @foreach ($reasons as $r)
                    <option value="{{ $r->reason }}">
                            {{ $r->reason }}
                    </option>
                    @endforeach

                    </select>`,
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.yes') }}',
                reverseButtons: true,
                onOpen: function() {
                    $('.js-select2-custom').select2({
                        minimumResultsForSearch: 5,
                        width: '100%',
                        placeholder: "Select Reason",
                        language: "en",
                    });
                }
            }).then((result) => {
                if (result.value) {
                    let reason = document.getElementById('reason').value;
                    location.href = '{!! route('vendor.order.status', ['id' => $order['id'], 'order_status' => 'canceled']) !!}&reason=' + reason,
                        '{{ translate('Change status to canceled ?') }}';
                }
            })

        });

        $('.order-status-change-alert').on('click', function() {
            let route = $(this).data('url');
            let message = $(this).data('message');
            let verification = $(this).data('verification');
            let processing = $(this).data('processing-time') ?? false;

            if (verification) {
                Swal.fire({
                    title: '{{ translate('Enter order verification code') }}',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    confirmButtonText: '{{ translate('messages.submit') }}',
                    showLoaderOnConfirm: true,
                    preConfirm: (otp) => {
                        location.href = route + '&otp=' + otp;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
            } else if (processing) {
                Swal.fire({
                    title: '{{ translate('messages.Are you sure ?') }}',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: '{{ translate('messages.Cancel') }}',
                    confirmButtonText: '{{ translate('messages.submit') }}',
                    inputPlaceholder: "{{ translate('Enter processing time') }}",
                    input: 'text',
                    html: message + '<br/>' +
                        '<label>{{ translate('Enter Processing time in minutes') }}</label>',
                    inputValue: processing,
                    preConfirm: (processing_time) => {
                        location.href = route + '&processing_time=' + processing_time;
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
            } else {
                Swal.fire({
                    title: '{{ translate('messages.Are you sure ?') }}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: '{{ translate('messages.No') }}',
                    confirmButtonText: '{{ translate('messages.Yes') }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        location.href = route;
                    }
                })
            }

        });

        $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'order_proof[]',
                maxCount: 6 -
                    {{ $order->order_proof && is_array($order->order_proof) ? count(json_decode($order->order_proof)) : 0 }},
                rowHeight: '176px !important',
                groupClassName: 'spartan_item_wrapper min-w-176px max-w-176px',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{ asset('public/assets/admin/img/upload-img.png') }}",
                    width: '176px'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function() {
                    toastr.error(
                        "{{ translate('messages.please_only_input_png_or_jpg_type_file') }}", {
                            CloseButton: true,
                            ProgressBar: true
                        });
                },
                onSizeErr: function() {
                    toastr.error("{{ translate('messages.file_size_too_big') }}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
@endpush
