@php
    $voucherDetail = $order->voucherDetail;
    $voucher = $voucherDetail ? json_decode($voucherDetail->item_details) : null;
   
    $items_details = $order->details;

    $firstDetail = $order->firstDetail;
    $item_details = $firstDetail ? $firstDetail->item : null;

    $branches = $firstDetail && $firstDetail->item ? $firstDetail->item->branches : collect([]);

    $main_branch = $item_details ? $item_details->store : null;
    $voucherSetting = $item_details ? App\Models\VoucherSetting::where('item_id', $item_details->id)->first() : null;




    $gift_exist = !empty($order->gift_details) ? true : false;

@endphp

<?php
$voucherSetting = $order->voucher_setting;

// Decode nested JSON safely
$usageUser = isset($voucherSetting['usage_limit_per_user']) && is_string($voucherSetting['usage_limit_per_user']) ? json_decode($voucherSetting['usage_limit_per_user'], true) : $voucherSetting['usage_limit_per_user'] ?? [];

$usageStore = isset($voucherSetting['usage_limit_per_store']) && is_string($voucherSetting['usage_limit_per_store']) ? json_decode($voucherSetting['usage_limit_per_store'], true) : $voucherSetting['usage_limit_per_store'] ?? [];

$afterPurchase = isset($voucherSetting['offer_validity_after_purchase']) && is_string($voucherSetting['offer_validity_after_purchase']) ? json_decode($voucherSetting['offer_validity_after_purchase'], true) : $voucherSetting['offer_validity_after_purchase'] ?? [];

$validity = isset($voucherSetting['validity_period']) && is_string($voucherSetting['validity_period']) ? json_decode($voucherSetting['validity_period'], true) : $voucherSetting['validity_period'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burger Bar Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #e8e8e8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            padding: 0;
            margin: 0;
        }

        .voucher-container {
            max-width: 100%;
            margin: 0 auto;
        }

        .voucher-card {
            background: white;
            /* overflow: hidden; */
            /* Removing this to allow share dropdown to be visible */
            position: relative;
        }

        /* Image Section */
        .voucher-image-wrapper {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
        }

        .voucher-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .in-store-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #1bb4c5;
            color: white;
            padding: 5px 14px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-download-btn {
            background: white;
            color: #1bb4c5;
            border: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, background 0.2s ease;
            padding: 0;
            margin-right: -4px;
        }

        .badge-download-btn:hover {
            transform: scale(1.1);
            background: #f8f8f8;
        }

        .badge-download-btn i {
            font-size: 14px;
            line-height: 1;
        }

        /* Dashed Border */
        .dashed-divider {
            height: 0;
            border: none;
            border-top: 2px dashed #1bb4c5;
            margin: 0;
            position: relative;
        }

        /* Main Content */
        .voucher-body {
            padding: 18px 16px;
        }

        /* Title and Gift Value Section */
        .title-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
            gap: 12px;
        }

        .voucher-title {
            color: #0066cc;
            font-size: 17px;
            font-weight: 600;
            line-height: 1.35;
            flex: 1;
        }

        .gift-value-box {
            background: #1bb4c5;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-align: center;
            min-width: 95px;
            flex-shrink: 0;
        }

        .gift-value-label {
            font-size: 10px;
            font-weight: 400;
            margin-bottom: 1px;
        }

        .gift-value-amount {
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
        }

        /* Restaurant Section */
        .restaurant-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .restaurant-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .restaurant-logo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #000;
            /* fallback background if no image */
            overflow: hidden;
            /* make image circular */
        }

        .restaurant-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* cover the circle */
            display: block;
        }

        .restaurant-logo-text {
            color: white;
            font-size: 9px;
            font-weight: 700;
            text-align: center;
            line-height: 1.2;
            display: block;
        }

        .restaurant-name {
            font-size: 17px;
            font-weight: 600;
            color: #000;
        }

        .delete-icon {
            color: #1bb4c5;
            font-size: 20px;
            cursor: pointer;
        }

        /* QR and Expiry Section */
        .qr-expiry-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0 16px;
            border-bottom: 1px solid #e5e5e5;
        }

        .expiry-block {
            flex-shrink: 0;
        }

        .expiry-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 3px;
        }

        .expiry-date {
            font-size: 13px;
            font-weight: 600;
            color: #000;
        }

        .qr-block {
            text-align: center;
            flex-shrink: 0;
        }

        .qr-code-box {
            width: 95px;
            height: 95px;
            background: white;
            border: 1px solid #ddd;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-code-box svg {
            width: 90px;
            height: 90px;
        }

        .voucher-code-label {
            font-size: 10px;
            color: #999;
            margin-bottom: 2px;
        }

        .voucher-code-number {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #000;
        }

        /* Share Download Section */
        .share-download-section {
            display: flex;
            gap: 16px;
            padding: 20px 0;
            border-bottom: 1px solid #e5e5e5;
            flex-wrap: wrap;
            position: relative;
        }

        .share-dropdown-container {
            position: relative;
        }

        .share-btn {
            background: #1bb4c5;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(27, 180, 197, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .share-btn:hover {
            background: #179fb0;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(27, 180, 197, 0.3);
            color: white;
        }

        .share-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border: 1px solid #eee;
            min-width: 220px;
            z-index: 1050;
            overflow: visible;
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            padding: 8px 0;
        }

        .share-dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .share-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s ease;
            cursor: pointer;
        }

        .share-item:hover {
            background: #f5f5f5;
            color: #1bb4c5;
        }

        .share-item i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .share-item.whatsapp i {
            color: #25D366;
        }

        .share-item.telegram i {
            color: #0088cc;
        }

        .share-item.facebook i {
            color: #1877F2;
        }

        .share-item.twitter i {
            color: #1DA1F2;
        }

        .share-item.copy i {
            color: #1bb4c5;
        }

        .download-btn-outline {
            background: white;
            color: #1bb4c5;
            border: 2px solid #1bb4c5;
            padding: 8px 22px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .download-btn-outline:hover {
            background: rgba(27, 180, 197, 0.05);
            transform: translateY(-2px);
            color: #179fb0;
        }

        .action-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .action-button i {
            font-size: 19px;
            color: #666;
        }

        .action-button span {
            font-size: 12px;
            color: #666;
        }

        /* Message Section */
        .message-box {
            background: #f7f8f9;
            padding: 12px 14px;
            border-radius: 8px;
            margin: 16px 0;
        }

        .message-recipient {
            font-size: 12px;
            color: #666;
            margin-bottom: 6px;
        }

        .message-content {
            font-size: 13px;
            color: #000;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .message-sender {
            font-size: 12px;
            color: #666;
        }

        /* Info Items Section */
        .info-items {
            padding-top: 4px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #e5e5e5;
            cursor: pointer;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-title {
            font-size: 13px;
            color: #000;
            font-weight: 400;
        }

        .view-button {
            color: #1bb4c5;
            font-size: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 3px;
            font-weight: 500;
        }

        .view-button i {
            font-size: 11px;
            transition: transform 0.3s ease;
        }

        .info-row.active .view-button i {
            transform: rotate(180deg);
        }

        .info-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .info-content.show {
            max-height: 500px;
            padding-top: 10px;
        }

        .info-content p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .voucher-container {
                padding: 0;
            }

            .voucher-card {
                border-radius: 0;
            }

            .voucher-body {
                padding: 16px 14px;
            }

            .voucher-title {
                font-size: 16px;
            }

            .gift-value-amount {
                font-size: 20px;
            }
        }

        @media (min-width: 577px) {
            .voucher-container {
                padding: 20px;
                max-width: 590px;
            }

            .voucher-card {
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            }
        }
    </style>

</head>

<body>


    <div class="voucher-container" id="voucherArea">
        <div class="voucher-card">
            <!-- Image with In-Store Badge -->
            <div class="voucher-image-wrapper">
                <div class="in-store-badge">
                    <span>{{ $order->voucher_type }}</span>
                </div>
                <img class="img-fluid rounded onerror-image"
                    src="{{ optional($item_details)->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                    data-onerror-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                    alt="Image Description">
            </div>
            <!-- Dashed Border -->
            <hr class="dashed-divider">

            <!-- Main Content Body -->
            <div class="voucher-body">
                <!-- Title and Gift Value -->
                <div class="title-section">
                    <div class="voucher-title">
                        {{ $voucher->name ?? 'Voucher Details Unavailable' }}
                    </div>
                    <div class="gift-value-box">
                        @if ($gift_exist)
                            <div class="gift-value-label">Gift Value</div>
                            <div class="gift-value-amount">
                                {{ \App\CentralLogics\Helpers::format_currency($order->total_order_amount) }}</div>
                        @else
                            <div class="gift-value-label">Save</div>
                            <div class="gift-value-amount">
                                {{ \App\CentralLogics\Helpers::format_currency($order->discount_amount) }}</div>
                        @endif
                    </div>
                </div>
                <div class="restaurant-section">
                    <div class="restaurant-left">
                        <div class="restaurant-logo">
                            @php
                                // Determine logo URL
                                $logoUrl =
                                    optional($main_branch)->logo_full_url ??
                                    ($main_branch
                                        ? asset('storage/store/' . ($main_branch->logo ?? 'default.png'))
                                        : null);
                            @endphp

                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ optional($main_branch)->name }} Logo">
                            @elseif ($main_branch)
                                <span class="restaurant-logo-text">
                                    {{ strtoupper(substr($main_branch->name, 0, 2)) }}
                                </span>
                            @else
                                <span class="restaurant-logo-text">N/A</span>
                            @endif
                        </div>
                        <div class="restaurant-name">{{ optional($main_branch)->name ?? 'Store N/A' }}</div>
                    </div>
                    <div class="restaurant-right">

                        <div class="restaurant-name">
                            @if ($gift_exist)
                                <i class="bi bi-gift" style="font-size: 30px"></i>
                            @else
                                <span class="text-muted "> <del>
                                        {{ \App\CentralLogics\Helpers::format_currency($order->total_order_amount) }}</del>
                                </span>
                                <br>
                                <span> {{ \App\CentralLogics\Helpers::format_currency($order->total_order_amount - $order->discount_amount) }}</span>
                            @endif


                        </div>
                    </div>
                </div>
                <!-- QR Code and Expiry -->
                <div class="qr-expiry-section">
                    <div class="expiry-block">

                        @php
                            use Carbon\Carbon;

                            $expiryDate = null;
                            $isExpired = false;

                            if (!empty($afterPurchase['value']) && !empty($order->created_at)) {
                                $expiry = Carbon::parse($order->created_at)->addDays((int) $afterPurchase['value']);

                                $expiryDate = $expiry->format('d M Y');

                                // Check if expired (today allowed)
                                $isExpired = $expiry->isPast() && !$expiry->isToday();
                            }
                        @endphp


                        @if ($expiryDate)
                            <div class="expiry-label {{ $isExpired ? 'text-danger' : 'text-success' }}">
                                {{ $isExpired ? 'Voucher Expired' : 'Expires On' }}
                            </div>

                            <div class="expiry-date {{ $isExpired ? 'text-danger fw-bold' : '' }}">
                                {{ $expiryDate }}
                            </div>
                        @endif



                    </div>
                    <div class="qr-code-box">
                        @if ($order->qr_code)
                            {!! QrCode::size(80)->generate($order->qr_code) !!}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="qr-block">
                        <div class="voucher-code-label">Voucher Code</div>
                        <div class="voucher-code-number">{{ $order->qr_code }}</div>
                    </div>
                </div>
                <!-- Share and Download Buttons -->
                <div class="share-download-section">
                    @if (!$gift_exist)
                        <div class="share-dropdown-container">
                            <button class="share-btn" id="shareTrigger">
                                <i class="bi bi-share"></i>
                                <span>Share Voucher</span>
                            </button>
                            <div class="share-dropdown-menu" id="shareMenu">
                                <div class="share-item whatsapp" onclick="shareTo('whatsapp')">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </div>
                                <div class="share-item telegram" onclick="shareTo('telegram')">
                                    <i class="bi bi-telegram"></i> Telegram
                                </div>
                                <div class="share-item facebook" onclick="shareTo('facebook')">
                                    <i class="bi bi-facebook"></i> Facebook
                                </div>
                                <div class="share-item twitter" onclick="shareTo('twitter')">
                                    <i class="bi bi-twitter-x"></i> Twitter
                                </div>
                                <div class="share-item copy" onclick="shareTo('copy')">
                                    <i class="bi bi-link-45deg"></i> Copy Link
                                </div>
                            </div>
                        </div>
                    @endif


                    <a href="{{ route('voucher.download', $order->qr_code) }}" class="download-btn-outline">
                        <i class="bi bi-download"></i>
                        <span>Download PDF</span>
                    </a>
                </div>
                @if (!empty($order->gift_details))
                    <div class="message-box">


                        <div class="message-recipient">
                            To: {{ $order->gift_details['recipient_name'] ?? '' }}
                        </div>

                        <div class="message-content">
                            {{ $order->gift_details['message'] ?? '' }}
                        </div>

                        <div class="message-sender">
                            From: {{ $order->gift_details['sender_name'] ?? '' }}
                        </div>



                    </div>
                @endif
                <!-- Info Items -->
                <!-- Info Items -->
                <div class="info-items">

                    @if ($items_details)
                        <div class="info-row" onclick="toggleInfo(this)">
                            <span class="info-title">Vouchers Info</span>
                            <a href="#" class="view-button" onclick="event.preventDefault()">
                                View <i class="bi bi-chevron-down"></i>
                            </a>
                        </div>

                        <div class="info-content">
                            @foreach ($items_details as $detail)
                                @if (isset($detail->item_id) && optional($detail->item)->type !== 'voucher')
                                    <div class="media media--sm">
                                        <div class="media-body">
                                            <div>

                                                {{-- Top Row --}}
                                                <div class="d-flex justify-content-between align-items-start">

                                                    <div>
                                                        <strong class="line--limit-1">
                                                            {{ Str::limit(optional($detail->item)->name ?? 'Item', 25, '...') }}
                                                        </strong>
                                                    </div>
                                                    @if (!$gift_exist)
                                                        {{-- Price Right Side --}}
                                                        <div class="text-end">

                                                            @if ($detail->is_paid == 0)
                                                                <h5>
                                                                    <del class="text-muted me-2">
                                                                        {{ \App\CentralLogics\Helpers::format_currency($detail['total_price']) }}
                                                                    </del>
                                                                </h5>

                                                                <span class="badge bg-success ms-sm-3">
                                                                    {{ translate('messages.free') }}
                                                                </span>
                                                            @elseif ($detail->is_paid == 1)
                                                                <h5>
                                                                    {{ \App\CentralLogics\Helpers::format_currency($detail['total_price']) }}
                                                                </h5>
                                                            @endif


                                                        </div>
                                                    @endif

                                                </div>


                                                {{-- Addons --}}
                                                @php
                                                    $add_ons = json_decode($detail['add_ons'], true);
                                                @endphp

                                                @if (!empty($add_ons))
                                                    <span>{{ translate('messages.add_ons') }} :</span>

                                                    @foreach ($add_ons as $addon)
                                                        <div class="font-size-sm text-body">
                                                            <span>{{ Str::limit($addon['name'], 25, '...') }}</span>
                                                        </div>
                                                        @if (!$gift_exist)
                                                            <span class="font-weight-bold">
                                                                {{ $addon['quantity'] }} x
                                                                {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                @endif


                                                {{-- Variations --}}
                                                @php
                                                    $variations = json_decode($detail['variation'], true);
                                                @endphp

                                                @if (!empty($variations))
                                                    <span>{{ translate('messages.variation') }} :</span>

                                                    @foreach ($variations as $variation)
                                                        <div class="font-size-sm text-body">
                                                            <span>{{ ucfirst($variation['name'] ?? 'Variation') }}
                                                                :</span>
                                                            <span class="fw-bold">
                                                                {{ $variation['values'][0]['label'] ?? '' }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                @endif
                            @endforeach

                        </div>
                    @endif
                    @isset($branches)
                        <div class="info-row" onclick="toggleInfo(this)">
                            <span class="info-title">Redeemable at {{ $branches->count() }} outlets</span>
                            <a href="#" class="view-button" onclick="event.preventDefault()">View <i
                                    class="bi bi-chevron-down"></i></a>
                        </div>


                        <div class="info-content">
                            @foreach ($branches as $branch)
                                <div class="d-flex align-items-center mb-3">
                                    {{-- Branch Logo --}}
                                    @php
                                        $logoUrl =
                                            $branch['logo_full_url'] ??
                                            ($branch['logo'] ? asset('storage/store/' . $branch['logo']) : null);
                                    @endphp
                                    <div class="me-3" style="width: 50px; height: 50px; flex-shrink:0;">
                                        @if ($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $branch['name'] }}"
                                                class="img-fluid rounded-circle"
                                                style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width:50px; height:50px;">
                                                {{ strtoupper(substr($branch['name'] ?? 'N/A', 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Branch Info --}}
                                    <div>
                                        <p class="mb-1">
                                            <strong>{{ $branch['name'] ?? 'N/A' }}</strong> -
                                            {{ $branch['address'] ?? 'N/A' }}

                                            {{-- Bootstrap badge for branch type --}}
                                            @if (isset($branch['type']))
                                                @if ($branch['type'] === 'main')
                                                    <span class="badge bg-primary">Main Branch</span>
                                                @elseif($branch['type'] === 'sub branch')
                                                    <span class="badge bg-warning text-dark">Sub Branch</span>
                                                @endif
                                            @endif
                                        </p>
                                        <p class="mb-0">Phone: {{ $branch['phone'] ?? 'N/A' }}</p>
                                        <p class="mb-0">Email: {{ $branch['email'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    @endisset





                    <div class="info-row" onclick="toggleInfo(this)">
                        <span class="info-title">Usage Terms</span>
                        <a href="#" class="view-button" onclick="event.preventDefault()">
                            View <i class="bi bi-chevron-down"></i>
                        </a>
                    </div>

                    <div class="info-content space-y-4 text-sm text-gray-700">

                        {{-- Usage Limits --}}
                        @if (!empty($usageUser) && !empty($usageStore))
                            <div>
                                <h6 class="font-semibold text-gray-900 mb-1">Usage Limits</h6>
                                <ul class="list-disc list-inside">
                                    <li>
                                        Each user may redeem this voucher
                                        <strong>{{ $usageUser['value'] ?? 0 }}</strong>
                                        time(s) {{ strtolower($usageUser['period'] ?? '') }}.
                                    </li>
                                    <li>
                                        This voucher may be redeemed a maximum of
                                        <strong>{{ $usageStore['value'] ?? 0 }}</strong>
                                        time(s) {{ strtolower($usageStore['period'] ?? '') }} per store.
                                    </li>
                                </ul>
                            </div>
                        @endif


                        {{-- Validity After Purchase --}}
                        @if (!empty($afterPurchase['value']))
                            <div>
                                <h6 class="font-semibold text-gray-900 mb-1">Validity After Purchase</h6>
                                <p>
                                    The voucher must be used within
                                    <strong>
                                        {{ $afterPurchase['value'] }} Days
                                    </strong>
                                    from the date of purchase.
                                </p>
                            </div>
                        @endif


                        {{-- Age Restriction --}}
                        @if (!empty($voucherSetting['age_restriction']))
                            <div>
                                <h6 class="font-semibold text-gray-900 mb-1">Age Restriction</h6>
                                <ul class="list-disc list-inside">
                                    @foreach ($voucherSetting['age_restriction'] as $age)
                                        @if (isset($age['text']))
                                            <li>{{ $age['text'] }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <hr>
                        @endif


                        {{-- Group Size Requirement --}}
                        @if (!empty($voucherSetting['group_size_requirement']))
                            <div>
                                <h6 class="font-semibold text-gray-900 mb-1">Group Size Requirement</h6>
                                <ul class="list-disc list-inside">
                                    @foreach ($voucherSetting['group_size_requirement'] as $group)
                                        @if (isset($group['text']))
                                            <li>{{ $group['text'] }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <hr>
                        @endif


                        {{-- Blackout Dates --}}
                        @if (!empty($voucherSetting['custom_blackout_dates']))
                            <div>
                                <h6 class="font-semibold text-gray-900 mb-1">Blackout Dates</h6>
                                <ul class="list-disc list-inside">
                                    @foreach ($voucherSetting['custom_blackout_dates'] as $date)
                                        @if (isset($date['date']))
                                            <li>
                                                {{ \Carbon\Carbon::parse($date['date'])->format('d M Y') }}
                                                @if (isset($date['description']))
                                                    – {{ $date['description'] }}
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <hr>
                        @endif

                    </div>




                    <div class="info-row" onclick="toggleInfo(this)">
                        <span class="info-title">How to use card</span>
                        <a href="#" class="view-button" onclick="event.preventDefault()">View <i
                                class="bi bi-chevron-down"></i></a>
                    </div>

                    <div class="info-content text-sm text-gray-700 space-y-3">

                        @php
                            $guides = is_string($order->voucher_usage_term_and_conditions)
                                ? json_decode($order->voucher_usage_term_and_conditions, true)
                                : $order->voucher_usage_term_and_conditions;
                        @endphp

                        @if (!empty($guides))
                            <div class="space-y-4">
                                @foreach ($guides as $guide)
                                    <div class="border p-3 rounded">

                                        {{-- Guide Title --}}
                                        <h5 class="font-semibold text-lg">
                                            {{ $guide['guide_title'] ?? '' }}
                                        </h5>

                                        {{-- Sections --}}
                                        @if (!empty($guide['sections']))
                                            @foreach ($guide['sections'] as $section)
                                                <div class="mt-2">

                                                    {{-- Section Title --}}
                                                    @if (!empty($section['title']))
                                                        <p class="font-medium">
                                                            {{ $section['title'] }}
                                                        </p>
                                                    @endif

                                                    {{-- Steps --}}
                                                    @if (!empty($section['steps']))
                                                        <ul class="list-disc list-inside text-sm mt-1">
                                                            @foreach ($section['steps'] as $step)
                                                                <li>{{ $step }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif

                                                </div>
                                            @endforeach
                                        @endif

                                    </div>
                                @endforeach

                            </div>
                        @endif
                    </div>
                </div>




            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleInfo(element) {
            // Get the next sibling (the info-content div)
            const content = element.nextElementSibling;

            // Toggle the 'show' class on the content
            content.classList.toggle('show');

            // Toggle the 'active' class on the row (for chevron rotation)
            element.classList.toggle('active');
        }
    </script>
    <script>
        async function downloadVoucherPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const element = document.getElementById('voucherArea');

            const actionSection = document.querySelector('.share-download-section');
            actionSection.style.display = 'none';

            // 🔥 Wait for images
            await waitForImages(element);

            const canvas = await html2canvas(element, {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                imageTimeout: 0
            });

            const imgData = canvas.toDataURL('image/png');

            const pdf = new jsPDF('p', 'mm', 'a4');

            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            const pxToMm = 25.4 / 96;
            const imgWidthMm = canvas.width * pxToMm;
            const imgHeightMm = canvas.height * pxToMm;

            const scale = Math.min(
                pageWidth / imgWidthMm,
                pageHeight / imgHeightMm
            );

            const finalWidth = imgWidthMm * scale;
            const finalHeight = imgHeightMm * scale;

            const marginX = (pageWidth - finalWidth) / 2;
            const marginY = (pageHeight - finalHeight) / 2;

            pdf.addImage(imgData, 'PNG', marginX, marginY, finalWidth, finalHeight);
            pdf.save('voucher-{{ $order->qr_code }}.pdf');

            actionSection.style.display = 'flex';
        }

        // 🔥 Image wait helper
        async function waitForImages(container) {
            const images = container.querySelectorAll('img');
            await Promise.all(
                Array.from(images).map(img => {
                    if (img.complete) return;
                    return new Promise(resolve => {
                        img.onload = img.onerror = resolve;
                    });
                })
            );
        }

        async function shareVoucher() {
            const text =
                'Check out this voucher: {{ $voucher->name ?? 'Gift Voucher' }} from {{ optional($main_branch)->name ?? 'our store' }}!';
            const url = window.location.href;

            if (navigator.share) {
                try {
                    await navigator.share({
                        title: '{{ $voucher->name ?? 'Voucher' }}',
                        text: text,
                        url: url,
                    });
                } catch (err) {
                    console.error('Share failed:', err);
                }
            }
        }

        // Share Dropdown Logic
        const shareTrigger = document.getElementById('shareTrigger');
        const shareMenu = document.getElementById('shareMenu');

        shareTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            shareMenu.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            shareMenu.classList.remove('show');
        });

        shareMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        function shareTo(platform) {
            const text =
                'Check out this voucher: {{ $voucher->name ?? 'Gift Voucher' }} from {{ optional($main_branch)->name ?? 'our store' }}!';
            const url = window.location.href;
            let shareUrl = '';

            switch (platform) {
                case 'whatsapp':
                    shareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(text + "\n" + url)}`;
                    break;
                case 'telegram':
                    shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
                    break;
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'twitter':
                    shareUrl =
                        `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
                    break;
                case 'copy':
                    navigator.clipboard.writeText(url).then(() => {
                        alert('Link copied to clipboard!');
                    });
                    return;
            }

            if (shareUrl) {
                window.open(shareUrl, '_blank');
            }
            shareMenu.classList.remove('show');
        }
    </script>

</body>

</html>
