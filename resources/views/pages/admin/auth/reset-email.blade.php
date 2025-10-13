<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset - MDC CampusLens</title>
    <style>
        @font-face {
            font-family: "Cubano";
            src: url("{{ asset('fonts/Cubano.woff2') }}") format("woff2"),
                url("{{ asset('fonts/Cubano.woff') }}") format("woff"),
                url("{{ asset('fonts/Cubano.ttf') }}") format("truetype");
        }

        body {
            background-color: #f8fafc;
            font-family: "Cubano", Verdana, sans-serif;
            margin: 0;
            padding: 20px 10px;
            /* Reduced padding for small screens */
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        .card {
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(to right, #157ee1, #93c5fd);
            color: #ffffff;
            padding: 24px 16px;
        }

        .header img {
            height: 60px;
            width: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .header h1 {
            font-family: "Cubano", Verdana, sans-serif;
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content {
            padding: 24px 16px;
            text-align: left;
        }

        .content p {
            font-family: "Cubano", Verdana, sans-serif;
            font-size: 16px;
            color: #434549;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .button {
            display: inline-block;
            max-width: 300px;
            background-color: #157ee1;
            color: #ffffff;
            padding: 8px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #157ee1;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(21, 126, 225, 0.3);
            text-decoration: none;
            text-align: center;
            margin: 0 auto;
        }

        .button:hover {
            background-color: #ffffff;
            color: #157ee1;
        }

        .footer {
            color: #434549;
            padding: 16px;
            font-size: 14px;
            text-align: left;
        }

        .footer p {
            margin: 4px 0;
        }

        span {
            color: #157ee1;
        }

        /* Mobile adjustments */
        @media only screen and (max-width: 480px) {
            .header h1 {
                font-size: 20px;
            }

            .content {
                padding: 16px 12px;
            }

            .content p {
                font-size: 14px;
            }

            .button {
                font-size: 14px;
                padding: 8px;
                max-width: 240px;
            }

            .footer {
                font-size: 12px;
                padding: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="card">
            <div class="header">
                <img src="{{ asset('images/mdc.png') }}" alt="MDC Logo">
                <h1>{{ config('app.name') }}</h1>
            </div>

            <div class="content">
                <p>Hello, <span>{{ $user->name ?? 'User' }}</span></p>
                <p>We received a request to reset the password for your account.</p>

                <p style="text-align:center;">
                    <a href="{{ $resetLink }}" class="button">Reset Password</a>
                </p>

                <p>If you didn’t request a password reset, you can safely ignore this message.</p>
            </div>

            <div class="footer">
                <p>Thank you,</p>
                <p><span>{{ config('app.name') }} Team</span></p>
            </div>
        </div>
    </div>
</body>

</html>
