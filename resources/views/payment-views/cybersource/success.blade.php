<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0fff4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 450px;
        }

        .icon {
            font-size: 60px;
            color: green;
        }

        h2 {
            color: #2d3748;
        }

        p {
            color: #4a5568;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: green;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✅</div>
        <h2>Payment Successful</h2>
        <p>{{ $message }}</p>
        <a href="{{ url('/') }}" class="btn">Go Home</a>
    </div>
</body>
</html>