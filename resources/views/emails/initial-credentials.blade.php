<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kredensial Akun {{ $appName }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .logo {
            margin-bottom: 15px;
        }
        .content {
            padding: 30px;
        }
        .welcome-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            border-radius: 50%;
            background-color: #d1fae5;
            color: #065f46;
        }
        .greeting {
            text-align: center;
            margin-bottom: 25px;
        }
        .greeting h2 {
            color: #1f2937;
            margin: 0 0 10px 0;
            font-size: 22px;
        }
        .greeting p {
            color: #6b7280;
            margin: 0;
        }
        .credentials-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }
        .credentials-box h3 {
            margin: 0 0 20px 0;
            color: #374151;
            font-size: 16px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .credential-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            border-left: 4px solid #667eea;
        }
        .credential-item:last-child {
            margin-bottom: 0;
        }
        .credential-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .credential-value {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        .warning-box {
            background-color: #fef3c7;
            border: 1px solid #fde047;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .warning-box h4 {
            margin: 0 0 10px 0;
            color: #92400e;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .warning-box ul {
            margin: 0;
            padding-left: 20px;
            color: #78350f;
        }
        .warning-box li {
            margin-bottom: 8px;
        }
        .warning-box li:last-child {
            margin-bottom: 0;
        }
        .action-button {
            display: block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 25px 0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .steps-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .steps-box h4 {
            margin: 0 0 15px 0;
            color: #0369a1;
            font-size: 16px;
        }
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .step:last-child {
            margin-bottom: 0;
        }
        .step-number {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .step-text {
            color: #374151;
            font-size: 14px;
        }
        .security-tips {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .security-tips h4 {
            margin: 0 0 10px 0;
            color: #991b1b;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .security-tips p {
            margin: 0;
            color: #7f1d1d;
            font-size: 14px;
        }
        .help-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .help-section p {
            margin: 0 0 10px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .help-section a {
            color: #667eea;
            text-decoration: none;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header, .content {
                padding: 20px;
            }
            .credential-value {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="{{ $appName }}" style="width: 60px; height: 60px; border-radius: 50%; background: white; padding: 8px;">
                @else
                    <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; color: #667eea; margin: 0 auto;">
                        {{ substr($appName, 0, 2) }}
                    </div>
                @endif
            </div>
            <h1>Selamat Datang di {{ $appName }}!</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Welcome Icon -->
            <div class="welcome-icon">🎉</div>

            <!-- Greeting -->
            <div class="greeting">
                <h2>Halo, {{ $user->name }}!</h2>
                <p>Akun Anda telah berhasil dibuat. Berikut adalah kredensial untuk login ke sistem.</p>
            </div>

            <!-- Credentials Box -->
            <div class="credentials-box">
                <h3>🔐 Kredensial Login Anda</h3>
                
                <div class="credential-item">
                    <div class="credential-label">NIM (Username)</div>
                    <div class="credential-value">{{ $nim }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Password Sementara</div>
                    <div class="credential-value">{{ $plainPassword }}</div>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="warning-box">
                <h4>⚠️ Penting! Segera Ganti Password Anda</h4>
                <ul>
                    <li>Password di atas adalah password sementara yang harus segera diganti.</li>
                    <li>Demi keamanan akun, <strong>ganti password dalam 24 jam pertama</strong> setelah login.</li>
                    <li>Jangan bagikan kredensial ini kepada siapapun.</li>
                </ul>
            </div>

            <!-- Steps -->
            <div class="steps-box">
                <h4>📋 Langkah-langkah Login:</h4>
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-text">Klik tombol "Login ke Sistem" di bawah atau buka <strong>{{ $loginUrl }}</strong></div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-text">Masukkan NIM dan password sementara di atas</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">Setelah login, segera buka menu <strong>Profil → Ubah Password</strong></div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-text">Buat password baru yang kuat (minimal 8 karakter, kombinasi huruf & angka)</div>
                </div>
            </div>

            <!-- Action Button -->
            <a href="{{ $loginUrl }}" class="action-button">
                🚀 Login ke Sistem
            </a>

            <!-- Security Tips -->
            <div class="security-tips">
                <h4>🔒 Tips Keamanan Password</h4>
                <p>
                    Gunakan password yang kuat dengan kombinasi huruf besar, huruf kecil, angka, dan simbol. 
                    Hindari menggunakan informasi pribadi seperti tanggal lahir atau nama.
                </p>
            </div>

            <!-- Help Section -->
            <div class="help-section">
                <p>Mengalami kesulitan atau ada pertanyaan?</p>
                <p>Hubungi admin di <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 12px;">
                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
            </p>
            <p style="margin-top: 10px; font-size: 11px; color: #9ca3af;">
                Jika Anda tidak merasa mendaftar di {{ $appName }}, abaikan email ini.
            </p>
        </div>
    </div>
</body>
</html>
