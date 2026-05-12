<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure Checkout</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f6fb;
            color: #111827;
            line-height: 1.5;
        }

        .wrapper {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 24px;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .card-body {
            padding: 30px;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 28px;
            font-size: 14px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 15px;
            transition: all .2s ease;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .summary {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
        }

        .summary .card-body {
            padding: 28px;
        }

        .business {
            text-align: center;
            margin-bottom: 28px;
        }

        .business img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            background: #fff;
            border: 3px solid rgba(255, 255, 255, .2);
            margin-bottom: 12px;
        }

        .business h2 {
            font-size: 24px;
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            font-size: 12px;
            letter-spacing: .4px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            font-size: 14px;
            color: #e5e7eb;
        }

        .summary-row strong {
            color: #fff;
        }

        .summary-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, .15);
            display: flex;
            justify-content: space-between;
            font-size: 24px;
            font-weight: 700;
        }

        .customer-box {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, .15);
        }

        .customer-box h4 {
            font-size: 16px;
            margin-bottom: 12px;
        }

        .customer-box p {
            color: #d1d5db;
            font-size: 13px;
            margin-bottom: 6px;
            word-break: break-word;
        }

        .pay-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 16px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }

        .pay-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.25);
        }

        .secure-note {
            text-align: center;
            margin-top: 14px;
            font-size: 12px;
            color: #6b7280;
        }

        .input-icon {
            position: relative;
        }

        .input-icon span {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 12px;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }

            .summary {
                order: -1;
            }
        }

        @media (max-width: 576px) {
            .wrapper {
                margin: 15px auto;
                padding: 0 10px;
            }

            .card-body {
                padding: 20px;
            }

            .title {
                font-size: 24px;
            }

            .row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .business h2 {
                font-size: 20px;
            }

            .summary-total {
                font-size: 20px;
            }

            .pay-btn {
                font-size: 15px;
                padding: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <div class="checkout-grid">

            {{-- Payment Form --}}
            <div class="card">
                <div class="card-body">
                    <h1 class="title">Secure Checkout</h1>
                    <p class="subtitle">Complete your payment securely using your debit or credit card.</p>

                    <form action="{{ route('cybersource.token', ['payment_id' => $data['payment_request_id']]) }}"
                        method="POST">
                        @csrf

                        <h3 class="section-title">Card Details</h3>

                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="card_number" class="form-control"
                                placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number" required>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" name="expiry_date" class="form-control" placeholder="MM/YY"
                                    maxlength="5" autocomplete="cc-exp" required>
                            </div>

                            <div class="form-group">
                                <label>CVV</label>
                                <input type="password" name="cvv" class="form-control" placeholder="123"
                                    maxlength="4" autocomplete="cc-csc" required>
                            </div>
                        </div>




                        <button type="submit" class="pay-btn">
                            Pay {{ number_format((float) $data['amount'], 2) }} {{ $data['currency'] }}
                        </button>

                        <div class="secure-note">
                            🔒 Your payment is encrypted and processed securely by CyberSource.
                        </div>
                    </form>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="card summary">
                <div class="card-body">
                    <div class="business">
                        <img src="{{ $data['business_logo'] }}" alt="{{ $data['business_name'] }}"
                            onerror="this.src='{{ asset('assets/admin/img/160x160/img2.jpg') }}'">
                        <h2>{{ $data['business_name'] }}</h2>
                        <span class="badge">{{ ucfirst($data['payment_method']) }}</span>
                    </div>

                    <div class="summary-row">
                        <span>Order Type</span>
                        <strong>{{ ucfirst($data['attribute']) }}</strong>
                    </div>

                    <div class="summary-row">
                        <span>Reference ID</span>
                        <strong>#{{ $data['attribute_id'] }}</strong>
                    </div>

                    <div class="summary-row">
                        <span>Currency</span>
                        <strong>{{ $data['currency'] }}</strong>
                    </div>

                    <div class="summary-row">
                        <span>Receiver</span>
                        <strong>{{ $data['receiver_name'] ?: 'N/A' }}</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span>{{ number_format((float) $data['amount'], 2) }} {{ $data['currency'] }}</span>
                    </div>

                    <div class="customer-box">
                        <h4>Customer Information</h4>
                        <p><strong>Name:</strong> {{ $data['customer_name'] }}</p>
                        <p><strong>Email:</strong> {{ $data['customer_email'] }}</p>
                        <p><strong>Phone:</strong> {{ $data['customer_phone'] }}</p>
                        @if (!empty($data['customer_address']))
                            <p><strong>Address:</strong> {{ $data['customer_address'] }}</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Format card number
        const cardNumber = document.querySelector('input[name="card_number"]');
        if (cardNumber) {
            cardNumber.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '').substring(0, 16);
                value = value.replace(/(.{4})/g, '$1 ').trim();
                e.target.value = value;
            });
        }

        // Format expiry date
        const expiryDate = document.querySelector('input[name="expiry_date"]');
        if (expiryDate) {
            expiryDate.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '').substring(0, 4);
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2);
                }
                e.target.value = value;
            });
        }
    </script>

</body>

</html>
