<!DOCTYPE html>
<html>
<head>
    <title>Secure Acceptance Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="card p-4 shadow">
        <h4 class="mb-3">CyberSource Secure Payment</h4>

        <form id="paymentForm" method="post"
              action="https://testsecureacceptance.cybersource.com/silent/pay">

            {{-- REQUIRED FIELDS --}}
            <input type="hidden" name="access_key" value="{{ env('CYBERSOURCE_ACCESS_KEY') }}">
            <input type="hidden" name="profile_id" value="{{ env('CYBERSOURCE_PROFILE_ID') }}">
            <input type="hidden" name="transaction_uuid" value="{{ uniqid() }}">
            <input type="hidden" name="signed_field_names" id="signed_field_names">
            <input type="hidden" name="unsigned_field_names" value="">
            <input type="hidden" name="signed_date_time" value="{{ gmdate("Y-m-d\TH:i:s\Z") }}">
            <input type="hidden" name="locale" value="en">

            {{-- TRANSACTION --}}
            <input type="hidden" name="transaction_type" value="sale">
            <input type="hidden" name="reference_number" value="{{ time() }}">
            <input type="hidden" name="amount" value="10.00">
            <input type="hidden" name="currency" value="USD">

            {{-- BILLING --}}
            <input type="hidden" name="bill_to_forename" value="John">
            <input type="hidden" name="bill_to_surname" value="Doe">
            <input type="hidden" name="bill_to_email" value="test@example.com">

            {{-- SIGNATURE --}}
            <input type="hidden" name="signature" id="signature">

            <button type="submit" class="btn btn-primary w-100">
                Pay Now
            </button>
        </form>

    </div>

</div>

<script>
    const form = document.getElementById('paymentForm');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        let formData = new FormData(form);

        // Define signed fields
        let fields = [];
        formData.forEach((value, key) => {
            fields.push(key);
        });

        document.getElementById('signed_field_names').value = fields.join(',');

        // Send to Laravel to generate signature
        let response = await fetch('/secure-sign', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        let signature = await response.text();

        document.getElementById('signature').value = signature;

        form.submit();
    });
</script>

</body>
</html>