<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $broadcast->subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            margin: 0 auto;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #10b981;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px;
            line-height: 1.6;
        }
        .content h2 {
            font-size: 18px;
            font-weight: 700;
            margin-top: 0;
            color: #111827;
        }
        .message-box {
            background-color: #f3f4f6;
            padding: 24px;
            border-radius: 16px;
            margin: 24px 0;
            font-size: 14px;
            white-space: pre-wrap;
        }
        .footer {
            padding: 40px;
            text-align: center;
            background-color: #f9fafb;
            border-top: 1px solid #f3f4f6;
        }
        .footer p {
            margin: 0;
            font-size: 12px;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CuanFlow Info</h1>
        </div>
        <div class="content">
            <h2>Halo, {{ $user->name }}!</h2>
            <p>Admin kami baru saja mengirimkan pengumuman penting untuk Anda:</p>
            
            <div class="message-box">
                {{ $content }}
            </div>

            <p>Harap perhatikan informasi di atas untuk kelancaran operasional bisnis Anda.</p>
            <p>Terima kasih telah menggunakan CuanFlow.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} CuanFlow - Solusi Bisnis Digital</p>
        </div>
    </div>
</body>
</html>
