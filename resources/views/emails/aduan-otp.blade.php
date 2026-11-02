<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi Laporan Bencana</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        .container {
            max-width: 560px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #ea5921;
            padding: 24px 32px;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #ffedd5;
            font-weight: 500;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 15px;
            line-height: 1.5;
            color: #334155;
            margin-bottom: 20px;
        }
        .otp-box {
            background-color: #fff7ed;
            border: 2px dashed #fdba74;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 24px 0;
        }
        .otp-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #c2380f;
            margin-bottom: 8px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #ea5921;
            font-family: 'Courier New', Courier, monospace;
        }
        .notice {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
            margin-top: 16px;
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 16px 32px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AKSARA BOGOR</h1>
            <p>PUSDALOPS PB - BPBD KABUPATEN BOGOR</p>
        </div>
        <div class="content">
            <p class="greeting">
                Halo,<br>
                Kami menerima permintaan verifikasi email pelapor untuk sistem pengaduan bencana Kabupaten Bogor. Gunakan kode verifikasi di bawah ini untuk mengonfirmasi email <strong>{{ $email }}</strong>:
            </p>
            <div class="otp-box">
                <div class="otp-label">Kode Verifikasi (OTP)</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            <p class="notice">
                • Kode ini berlaku selama <strong>10 menit</strong>.<br>
                • Jangan bagikan kode ini kepada orang lain untuk keamanan data Anda.<br>
                • Jika Anda tidak merasa mengajukan laporan bencana ini, Anda dapat mengabaikan email ini.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Badan Penanggulangan Bencana Daerah (BPBD) Kabupaten Bogor.
        </div>
    </div>
</body>
</html>
