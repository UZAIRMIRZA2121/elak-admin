    <style>
        .coupon-card {
            width: 600px;
            height: 200px;
            background-color: #f1f3f8;
            border: 4px solid #ff9800;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
        }

        /* Creating the Ticket Notches (Cutouts) */
        .coupon-card::before,
        .coupon-card::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            /* Matches page background */
            border: 4px solid #ff9800;
            border-radius: 50%;
            left: 33.3%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .coupon-card::before {
            top: -25px;
        }

        /* Top Notch */
        .coupon-card::after {
            bottom: -25px;
        }

        /* Bottom Notch */

        /* Left Section */
        .coupon-left {
            flex: 0 0 33.3%;
            background-color: #9db2a3;
            /* Muted green from image */
        }

        .side-label {
            background: linear-gradient(to bottom, #ff9800, #ffc107);
            color: white;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            text-align: center;
            padding: 10px 5px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Middle Section */
        .coupon-body {
            flex: 1;
            padding-left: 30px;
            border-left: 2px dashed #ff9800;
            /* Separator line */
        }

        .title {
            color: #0d3b66;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .subtitle {
            color: #0d3b66;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Right Section */
        .coupon-right {
            padding: 15px;
            flex: 0 0 20%;
        }

        .save-badge {
            background: linear-gradient(135deg, #ff9800 0%, #ffeb3b 100%);
            color: white;
            padding: 15px 10px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .save-text {
            display: block;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .percentage {
            display: block;
            font-size: 1.5rem;
            font-weight: 800;
        }
    </style>
    <style>
        #countdown {
            font-size: 18px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
            background: #f5f5f5;
            color: #333;
            transition: all 0.3s ease;
        }

        /* Warning (less than 10 min) */
        #countdown.warning {
            color: #fff;
            background: #dc3545;
            /* red */
            animation: pulse 1s infinite;
        }

        /* Pulse animation */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
