<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
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
        .notification-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            border-radius: 50%;
        }
        .notification-icon.added {
            background-color: #d1fae5;
            color: #065f46;
        }
        .notification-icon.removed {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .notification-icon.updated {
            background-color: #fef3c7;
            color: #92400e;
        }
        .notification-icon.published {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
            font-size: 16px;
        }
        .schedule-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
        }
        .schedule-card .date {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .schedule-card .session {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .schedule-card .time {
            font-size: 16px;
            opacity: 0.9;
        }
        .info-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info-box.warning {
            background-color: #fef3c7;
            border-color: #fde047;
        }
        .info-box.error {
            background-color: #fee2e2;
            border-color: #fca5a5;
        }
        .info-box h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
        }
        .slotmates {
            margin: 25px 0;
        }
        .slotmates h4 {
            font-size: 16px;
            color: #374151;
            margin-bottom: 15px;
        }
        .slotmate-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .slotmate {
            display: flex;
            align-items: center;
            background-color: #f3f4f6;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        .slotmate img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .slotmate .avatar-placeholder {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 8px;
        }
        .data-section {
            margin: 25px 0;
        }
        .data-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-item:last-child {
            border-bottom: none;
        }
        .data-label {
            font-weight: 600;
            color: #6b7280;
        }
        .data-value {
            color: #111827;
            text-align: right;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
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
            .schedule-card .date {
                font-size: 20px;
            }
            .schedule-card .session {
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
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="width: 60px; height: 60px; border-radius: 50%; background: white; padding: 8px;">
                @else
                    <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; color: #667eea; margin: 0 auto;">
                        {{ substr(config('app.name'), 0, 2) }}
                    </div>
                @endif
            </div>
            <h1>{{ $title }}</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Notification Icon -->
            @if(isset($notificationType))
                @switch($notificationType)
                    @case('assignment_added')
                        <div class="notification-icon added">✅</div>
                        @break
                    @case('assignment_removed')
                        <div class="notification-icon removed">❌</div>
                        @break
                    @case('assignment_updated')
                        <div class="notification-icon updated">🔄</div>
                        @break
                    @case('schedule_published')
                        <div class="notification-icon published">📅</div>
                        @break
                    @default
                        <div class="notification-icon published">🔔</div>
                @endswitch
            @endif

            <!-- Message -->
            <div class="message">
                {{ $message }}
            </div>

            <!-- Schedule Card (for single assignment notifications) -->
            @if(isset($data['Tanggal']) && isset($data['Sesi']) && $notificationType !== 'schedule_published')
                <div class="schedule-card">
                    <div class="date">{{ $data['Hari'] ?? '' }}</div>
                    <div class="date" style="font-size: 18px; margin-bottom: 15px;">{{ $data['Tanggal'] }}</div>
                    <div class="session">{{ $data['Sesi'] }}</div>
                    <div class="time">{{ $data['Waktu'] }}</div>
                </div>
            @endif

            <!-- Slotmates Section -->
            @if(isset($data['slotmates']) && !empty($data['slotmates']) && $notificationType === 'assignment_added')
                <div class="slotmates">
                    <h4>👥 Rekan Kerja Anda ({{ $data['slotmate_count'] }} orang)</h4>
                    <div class="slotmate-list">
                        @foreach($data['slotmates'] as $slotmate)
                            <div class="slotmate">
                                @if(!empty($slotmate['photo']))
                                    <img src="{{ asset('storage/' . $slotmate['photo']) }}" alt="{{ $slotmate['name'] }}">
                                @else
                                    <div class="avatar-placeholder">
                                        {{ strtoupper(substr($slotmate['name'], 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $slotmate['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Reason Section -->
            @if(isset($data['Alasan']) && $data['Alasan'] !== 'Penjadwalan otomatis')
                <div class="info-box {{ $notificationType === 'assignment_removed' ? 'warning' : '' }}">
                    <h4>📝 Alasan Perubahan</h4>
                    <p>{{ $data['Alasan'] }}</p>
                </div>
            @endif

            <!-- Old vs New Schedule (for updates) -->
            @if($notificationType === 'assignment_updated' && isset($data['Jadwal Lama']) && isset($data['Jadwal Baru']))
                <div class="data-section">
                    <div class="data-item">
                        <span class="data-label">Jadwal Lama:</span>
                        <span class="data-value">{{ $data['Jadwal Lama'] }}</span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Jadwal Baru:</span>
                        <span class="data-value">{{ $data['Jadwal Baru'] }}</span>
                    </div>
                </div>
            @endif

            <!-- Assignment List (for published schedules) -->
            @if($notificationType === 'schedule_published' && isset($data['Daftar Jadwal']))
                <div class="info-box">
                    <h4>📋 Jadwal Anda</h4>
                    <p style="white-space: pre-line; font-family: monospace; font-size: 13px;">{{ $data['Daftar Jadwal'] }}</p>
                </div>
            @endif

            <!-- Schedule Info -->
            @if(isset($data['schedule_info']))
                <div class="info-box">
                    <h4>ℹ️ Informasi Penting</h4>
                    <p>{{ $data['schedule_info'] }}</p>
                </div>
            @endif

            <!-- Action Button -->
            @if(isset($data['action_url']))
                <div style="text-align: center;">
                    <a href="{{ $data['action_url'] }}" class="action-button">
                        {{ $data['action_text'] ?? 'Lihat Jadwal Lengkap' }}
                    </a>
                </div>
            @endif

            <!-- Help Text -->
            <div style="text-align: center; margin: 30px 0;">
                <p style="color: #6b7280; font-size: 14px;">
                    Jika Anda memiliki pertanyaan atau berhalangan hadir, silakan hubungi admin sistem.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 12px;">
                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
