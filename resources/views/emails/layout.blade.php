<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject ?? 'Ivora Dental Studio' }}</title>
</head>
<body style="margin:0;padding:0;background:#F4F7F5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#0A1614;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F7F5;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:20px;overflow:hidden;border:1px solid rgba(16,64,59,.1);">

                    <tr>
                        <td style="background:#10403B;padding:26px 32px;">
                            <p style="margin:0;font-size:19px;font-weight:700;color:#F4F7F5;letter-spacing:-.02em;">Ivora Dental Studio</p>
                            <p style="margin:5px 0 0;font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:#A8E6D8;">
                                @yield('preheader', 'Quiet, careful dentistry')
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            @yield('body')
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 32px;background:#F4F7F5;border-top:1px solid rgba(16,64,59,.08);">
                            <p style="margin:0 0 6px;font-size:12px;color:#5C7A75;line-height:1.6;">
                                {{ \App\Models\Setting::get('address') }}<br>
                                {{ \App\Models\Setting::get('phone') }} · {{ \App\Models\Setting::get('email') }}
                            </p>
                            <p style="margin:10px 0 0;font-size:11px;color:#8AA39E;">
                                @yield('footer', 'You are receiving this because you are a patient at Ivora Dental Studio.')
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
