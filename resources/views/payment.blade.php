<!DOCTYPE html>
<html>
<head>
    <title>CyberSource Test</title>
</head>
<body>

<h2>Test Payment</h2>

<form method="POST" action="{{ url('/cybersource-test') }}">
    @csrf

    <label>Amount:</label>
    <input type="text" name="amount" value="10.00" />

    <br><br>

    <button type="submit">Pay Now</button>
</form>

</body>
</html>