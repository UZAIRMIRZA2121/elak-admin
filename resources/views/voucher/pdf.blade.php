@php
    $voucherDetail = $order->voucherDetail;
    $voucher = $voucherDetail ? json_decode($voucherDetail->item_details) : null;
    
    $firstDetail = $order->firstDetail;
    $item = $firstDetail ? $firstDetail->item : null;
    $gift = $firstDetail->gift_details ?? null;
    $branches = $item ? ($item->branches ?? []) : [];
    $store = $item ? $item->store : null;

    $mainImage = ($item && $item->image)
        ? public_path('storage/' . str_replace('storage/', '', $item->image))
        : null;

    $logo = ($store && $store->logo)
        ? public_path('storage/store/' . $store->logo)
        : null;
@endphp

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Voucher</title>

<style>
body {
    font-family: DejaVu Sans, sans-serif;
    background: #e8e8e8;
}
.voucher-container {
    max-width: 420px;
    margin: auto;
    background: #fff;
}
.image {
    height: 220px;
}
.image img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}
.badge {
    position: absolute;
    background: #1bb4c5;
    color: #fff;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 12px;
}
.divider {
    border-top: 2px dashed #1bb4c5;
}
.body {
    padding: 16px;
}
.title {
    color: #0066cc;
    font-size: 17px;
    font-weight: bold;
}
.value {
    background: #1bb4c5;
    color: #fff;
    padding: 6px 10px;
    font-size: 20px;
    text-align: center;
    float: right;
}
.section {
    border-bottom: 1px solid #e5e5e5;
    padding: 14px 0;
}
.logo img {
    width: 42px;
    height: 42px;
    border-radius: 50%;
}
.qr img {
    width: 95px;
}
.small {
    font-size: 12px;
    color: #666;
}
.box {
    background: #f7f8f9;
    padding: 12px;
    border-radius: 8px;
}
</style>
</head>

<body>

<div class="voucher-container">

    @if($mainImage && file_exists($mainImage))
        <div class="image">
            <img src="{{ $mainImage }}">
        </div>
    @endif

    <hr class="divider">

    <div class="body">

        <div class="section">
            <span class="title">{{ optional($voucher)->name ?? 'Voucher Details Unavailable' }}</span>
            <span class="value">{{ $order->order_amount }}</span>
            <div style="clear:both"></div>
        </div>

        <div class="section">
            @if($logo && file_exists($logo))
                <span class="logo">
                    <img src="{{ $logo }}">
                </span>
            @endif
            <strong style="margin-left:10px">{{ optional($store)->name ?? 'Store N/A' }}</strong>
        </div>

        <div class="section qr">
            @if(isset($qrPath))
                <img src="{{ $qrPath }}">
            @endif
            <div class="small">Voucher Code</div>
            <strong>{{ $order->qr_code }}</strong>
        </div>

        @if($gift)
            <div class="box">
                <div>To: {{ $gift['recipient_name'] }}</div>
                <div>{{ $gift['message'] }}</div>
                <div>From: {{ $gift['sender_name'] }}</div>
            </div>
        @endif

        @if($item && !empty($item->description))
            <div class="section">
                <strong>Voucher Info</strong>
                <p class="small">{{ $item->description }}</p>
            </div>
        @endif

        <div class="section">
            <strong>Redeemable At</strong>
            @foreach($branches as $branch)
                <p class="small">
                    {{ $branch['name'] }}<br>
                    {{ $branch['address'] }}
                </p>
            @endforeach
        </div>

        <div class="section">
            <strong>Usage Terms</strong>
            <p class="small">Valid for one-time use only.</p>
        </div>

        <div class="section">
            <strong>How to Use</strong>
            <p class="small">
                1. Visit outlet<br>
                2. Show QR code<br>
                3. Enjoy
            </p>
        </div>

    </div>
</div>

</body>
</html>
