{{-- filepath: resources/views/emails/care-manager-notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f7f7f7;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 24px 32px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
        }
        .header {
            background-color: #0d6efd;
            color: #fff;
            padding: 18px 0;
            border-radius: 8px 8px 0 0;
            margin: -24px -32px 24px -32px;
        }
        .content {
            margin-bottom: 24px;
        }
        .footer {
            margin-top: 32px;
            font-size: 12px;
            color: #777;
        }
        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 32px 0 16px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">{{ $subject ?? 'Notification' }}</h2>
        </div>
        <div class="content">
            <p style="font-size: 18px;">Dear {{ $careManager->first_name ?? 'Care Manager' }},</p>
            <p style="font-size: 16px;">{!! nl2br(e($message)) !!}</p>
        </div>
        <hr>
        <div class="footer">
            <p>This is an automated notification from Sulong Kalinga.</p>
        </div>
    </div>
</body>
</html>