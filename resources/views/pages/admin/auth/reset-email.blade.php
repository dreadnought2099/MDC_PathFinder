<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Reset - MDC CampusLens</title>
    <style>
        /* Basic embedded styles (emails can’t load app.css) */
        @font-face {
            font-family: "Cubano";
            src: url("{{ asset('fonts/Cubano.woff2') }}") format("woff2"),
                url("{{ asset('fonts/Cubano.woff') }}") format("woff"),
                url("{{ asset('fonts/Cubano.ttf') }}") format("truetype");

        }

        @font-face {
            font-family: "Sofia Pro";
            src: url("{{ asset('fonts/Sofia Pro Black Az.woff2') }}") format("woff2"),
                url("{{ asset('fonts/Sofia Pro Black Az.woff') }}") format("woff"),
                url("{{ asset('fonts/Sofia Pro Black Az.ttf') }}") format("truetype");

        }

        body {
            background-color: #f8fafc;
            font-family: "Cubano", Verdana, sans-serif;
            margin: 0;
            padding: 40px 0;
        }


        .font-sofia {
            font-family: "Sofia Pro", Verdana, sans-serif;
            text-transform: none;
        }

        .email-container {
            width: 100%;
            text-align: center;
        }

        .card {
            width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(to right, #157ee1, #93c5fd);
            color: #ffffff;
            padding: 24px;
        }

        .header img {
            height: 60px;
            width: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .header h1 {
            font-family: "Cubano", Verdana, sans-serif;
            font-size: 26px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .content {
            padding: 32px;
            text-align: left;
        }

        .content p {
            font-family: "Cubano", Verdana, sans-serif;
            font-size: 16px;
            color: #434549;
            line-height: 1.6;
        }

        .button {
            width: 100%;
            background-color: #157ee1;
            color: #ffffff;
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            border: 1px solid #157ee1;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(21, 126, 225, 0.3);
            text-decoration: none;
            display: inline-block
        }

        .button:hover {
            background-color: #ffffff;
            color: #157ee1;
        }

        /* Responsive for small screens (sm: prefix in Tailwind = min-width: 640px) */
        @media (min-width: 640px) {
            .btn-primary {
                padding-top: 0.625rem;
                padding-bottom: 0.625rem;
                font-size: 1rem;
            }
        }

        .footer {
            color: #434549;
            padding: 16px 32px 32px;
            font-size: 14px;
            text-align: left;
        }

        .footer p {
            margin: 4px 0
        }

        span {
            color: #157ee1
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
