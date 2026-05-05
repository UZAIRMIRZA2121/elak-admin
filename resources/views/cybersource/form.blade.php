<!DOCTYPE html>
<html>
<head>
    <title>CyberSource Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">CyberSource Test Payment</h5>
                </div>

                <div class="card-body">

                    {{-- SUCCESS --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- ERROR --}}
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="/cybersource-pay">
                        @csrf

                        {{-- AMOUNT --}}
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" name="amount" class="form-control" value="10.00">
                        </div>

                        <hr>

                        <h6 class="mb-3">Card Details</h6>

                        {{-- CARD NUMBER --}}
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" name="card_number" class="form-control" value="4111111111111111">
                        </div>

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Month</label>
                                <input type="text" name="exp_month" class="form-control" value="12">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Year</label>
                                <input type="text" name="exp_year" class="form-control" value="2030">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">CVV</label>
                                <input type="text" name="cvv" class="form-control" value="123">
                            </div>

                        </div>

                        <button class="btn btn-primary w-100">
                            Pay Now
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>