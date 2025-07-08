<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP untuk Reset Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #0d2f57;
            padding: 30px 20px;
            text-align: center;
            color: #fff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 30px 20px;
            text-align: center;
        }
        .content h2 {
            color: #fd6e14;
            margin-bottom: 10px;
        }
        .otp-box {
            background-color: #f9f9f9;
            border: 2px dashed #fd6e14;
            display: inline-block;
            padding: 15px 25px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #0d2f57;
            letter-spacing: 6px;
        }
        .note {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }
        .footer {
            background-color: #0d2f57;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }
        .footer a {
            color: #fd6e14;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>Solusindo Sinergi Lelang</h1>
            <p>Kode OTP untuk Reset Password</p>
        </div>
        <div class="content">
            <h2>Halo,</h2>
            <p>Kami menerima permintaan untuk mereset password akun Anda.</p>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            <p>Kode ini hanya berlaku selama <strong>10 menit</strong>. Jangan bagikan kode ini kepada siapa pun.</p>
            <div class="note">
                Jika Anda tidak meminta reset password, silakan abaikan email ini.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Solusindo Sinergi Lelang. All rights reserved.
        </div>
    </div>
</body>
</html>
