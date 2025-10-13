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
            /* Tailwind blue-500 → blue-300 */
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
            font-family: "Sofia Pro", Verdana, sans-serif;
            font-size: 16px;
            color: #374151;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            background-color: #157ee1;
            /* blue-600 */
            color: #ffffff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 500;
            margin: 24px 0;
            font-family: "Sofia Pro", Verdana, sans-serif;
        }

        .footer {
            padding: 16px 32px 32px;
            font-size: 14px;
            color: #6b7280;
            text-align: left;
        }

        .footer p {
            margin: 4px 0;
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
                <p>Hello, {{ $user->name ?? 'User' }}</p>
                <p>We received a request to reset the password for your account.</p>

                <p style="text-align:center;">
                    <a href="{{ url('/admin/reset-password/' . $token) }}" class="button">Reset Password</a>
                </p>

                <p>If you didn’t request a password reset, you can safely ignore this message.</p>
            </div>

            <div class="footer">
                <p>Thank you,</p>
                <p><strong>{{ config('app.name') }} Team</strong></p>
            </div>
        </div>
    </div>
</body>

</html>
