<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>

<body
    style="background-color: #f8fafc; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; padding: 40px 0;">

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                    style="background-color: #ffffff; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #157ee1; text-align: center; padding: 24px;">
                            <h1 style="color: #ffffff; font-size: 24px; font-weight: 700; margin: 0;">
                                {{ config('app.name') }} Admin
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px; color: #111827;">
                            <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 16px;">Password Reset Request
                            </h2>

                            <p style="margin-bottom: 16px; color: #374151;">
                                Hello, {{ $user->name ?? 'User' }}
                            </p>

                            <p style="margin-bottom: 16px; color: #374151;">
                                You’re receiving this email because we received a password reset request for your
                                <strong>{{ config('app.name') }}</strong> account.
                            </p>

                            <p style="text-align: center; margin: 32px 0;">
                                <a href="{{ url('/admin/reset-password/' . $token) }}"
                                    style="display: inline-block; background-color: #157ee1; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 0.5rem; font-weight: 600;">
                                    Reset Password
                                </a>
                            </p>

                            <p style="margin-bottom: 16px; color: #6b7280;">
                                If you did not request a password reset, no further action is required.
                            </p>

                            <p style="margin-top: 24px; color: #111827;">
                                Regards,<br>
                                <strong>The {{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color: #f3f4f6; text-align: center; color: #9ca3af; font-size: 12px; padding: 16px;">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
