@php
    $voucher = json_decode($order->voucherDetail->item_details);

    $item_details = $order->firstDetail->item;
    $gift_details = $order->firstDetail->gift_details ?? null;
    $branches = $order->firstDetail->item->branches;

    $main_branch = $branches->firstWhere('type', 'main');

    // dd($branches);

@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burger Bar Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            overflow: hidden;
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
            gap: 24px;
            padding: 16px 0;
            border-bottom: 1px solid #e5e5e5;
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
                max-width: 420px;
            }

            .voucher-card {
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            }
        }
    </style>
    </style>
</head>

<body>
    <div class="voucher-container">
        <div class="voucher-card">
            <!-- Image with In-Store Badge -->
            <div class="voucher-image-wrapper">
                <div class="in-store-badge">{{ $order->voucher_type }}</div>
                <img src=" {{ $item_details->image }}" alt="{{ $order->voucher_type }}">
            </div>

            <!-- Dashed Border -->
            <hr class="dashed-divider">

            <!-- Main Content Body -->
            <div class="voucher-body">
                <!-- Title and Gift Value -->
                <div class="title-section">
                    <div class="voucher-title">
                        {{ $voucher->name }}
                    </div>
                    <div class="gift-value-box">
                        <div class="gift-value-label">Gift Value</div>
                        <div class="gift-value-amount"> {{ $order->order_amount }}</div>
                    </div>
                </div>
                <div class="restaurant-section">
                    <div class="restaurant-left">
                        <div class="restaurant-logo">
                            @php
                                // Determine logo URL
                                $logoUrl =
                                    $main_branch->logo_full_url ??
                                    asset('storage/store/' . ($main_branch->logo ?? 'default.png'));
                            @endphp

                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $main_branch->name }} Logo">
                            @else
                                <span class="restaurant-logo-text">
                                    {{ strtoupper(substr($main_branch->name, 0, 2)) }}
                                </span>
                            @endif
                        </div>
                        <div class="restaurant-name">{{ $main_branch->name }}</div>
                    </div>
                </div>


                <!-- QR Code and Expiry -->
                <div class="qr-expiry-section">
                    <div class="expiry-block">
                        <div class="expiry-label">Expires on</div>
                        <div class="expiry-date">Feb 7, 2026</div>
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
                    <button class="action-button">
                        <i class="bi bi-share"></i>
                        <span>Share</span>
                    </button>
                    <button class="action-button">
                        <i class="bi bi-download"></i>
                        <span>Download</span>
                    </button>
                </div>
                @if (isset($gift_details))
                    <!-- Message Box -->
                    <div class="message-box">
                        <div class="message-recipient">To: {{ $gift_details['recipient_name'] }}</div>
                        <div class="message-content">{{ $gift_details['message'] }}</div>
                        <div class="message-sender">{{ $gift_details['sender_name'] }}</div>
                    </div>
                @endif
                <!-- Info Items -->
                <!-- Info Items -->
                <div class="info-items">
                    <div class="info-row" onclick="toggleInfo(this)">
                        <span class="info-title">Vouchers Info</span>
                        <a href="#" class="view-button" onclick="event.preventDefault()">View <i
                                class="bi bi-chevron-down"></i></a>
                    </div>
                    <div class="info-content">
                        <p>This voucher can be used for purchasing burgers and related items at Burger Bar. Present the
                            QR code or voucher number at checkout.</p>
                    </div>

                    <div class="info-row" onclick="toggleInfo(this)">
                        <span class="info-title">Redeemable at 4 outlets</span>
                        <a href="#" class="view-button" onclick="event.preventDefault()">View <i
                                class="bi bi-chevron-down"></i></a>
                    </div>
                
                    @isset($branches)
                        <div class="info-content">
                            @foreach ($branches as $branch)
                                <div class="d-flex align-items-center mb-3">
                                    {{-- Branch Logo --}}
                                    @php
                                        $logoUrl = $branch['logo_full_url'] ?? ($branch['logo'] ? asset('storage/store/' . $branch['logo']) : null);
                                    @endphp
                                    <div class="me-3" style="width: 50px; height: 50px; flex-shrink:0;">
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $branch['name'] }}" class="img-fluid rounded-circle" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
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
                                            @if(isset($branch['type']))
                                                @if($branch['type'] === 'main')
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
                            <a href="#" class="view-button" onclick="event.preventDefault()">View <i
                                    class="bi bi-chevron-down"></i></a>
                        </div>
                        <div class="info-content">
                            <p>Valid for one-time use only. Cannot be combined with other offers. No cash value. Valid
                                until
                                expiry date shown above.</p>
                        </div>

                        <div class="info-row" onclick="toggleInfo(this)">
                            <span class="info-title">How to use card</span>
                            <a href="#" class="view-button" onclick="event.preventDefault()">View <i
                                    class="bi bi-chevron-down"></i></a>
                        </div>
                        <div class="info-content">
                            <p>1. Visit any participating Burger Bar outlet<br>
                                2. Show the QR code or voucher number to the cashier<br>
                                3. Your discount will be applied automatically<br>
                                4. Enjoy your meal!</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>

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
</body>

</html>
