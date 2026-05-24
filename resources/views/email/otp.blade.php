<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi TebasLahan</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 40px 0; color: #374151;">
    <table width="100%" cellpadding="0" cellspacing="0"
        style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
        <tr>
            <td style="background-color: #0d9488; text-align: center; padding: 30px 20px;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px;">
                    TebasLahan</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 30px;">
                <h2 style="margin-top: 0; color: #111827; font-size: 20px;">Verifikasi Akun Anda</h2>
                <p style="font-size: 15px; line-height: 1.6; color: #4b5563;">
                    Halo, <br><br>
                    Terima kasih telah mendaftar. Untuk melanjutkan proses di aplikasi, silakan gunakan kode verifikasi
                    (OTP) berikut:
                </p>
                <div style="text-align: center; margin: 35px 0;">
                    <span
                        style="display: inline-block; font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #0d9488; background-color: #f0fdfa; padding: 15px 30px; border-radius: 8px; border: 1px dashed #5eead4;">
                        {{ $otp }}
                    </span>
                </div>
                <p style="font-size: 14px; line-height: 1.6; color: #6b7280;">
                    Kode ini bersifat rahasia dan berlaku selama 5 menit. Jangan pernah membagikan kode ini kepada siapa
                    pun.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f3f4f6; text-align: center; padding: 20px; font-size: 12px; color: #9ca3af;">
                <p style="margin: 0;">&copy; {{ date('Y') }} TebasLahan Indonesia.</p>
                <p style="margin: 5px 0 0 0;">Email otomatis, mohon tidak dibalas.</p>
            </td>
        </tr>
    </table>
</body>

</html>
