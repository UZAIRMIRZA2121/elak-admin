<!DOCTYPE html>
<html>
<head>
    <title>CyberSource Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">

<div class="container mt-4">

    <div class="card bg-black text-white shadow">

        <div class="card-header">
            <h5 class="mb-0">CyberSource Activity Logs</h5>
        </div>

        <div class="card-body">

            <div style="height:500px; overflow:auto; background:#111; padding:15px; border-radius:5px;">

                @foreach($logs as $log)
                    <div style="border-bottom:1px solid #333; padding:5px 0;">
                        {{ $log }}
                    </div>
                @endforeach

            </div>

        </div>

    </div>

</div>

</body>
</html>