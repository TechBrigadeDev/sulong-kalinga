<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Reset Your Password</h2>
        </div>
        
        <p>Hello,</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p>Please click the button below to reset your password:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>
        
        <p>If you did not request a password reset, no further action is required.</p>
        
        <p>This password reset link will expire in 24 hours.</p>
        
        <div class="footer">
            <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
            <p>{{ $resetUrl }}</p>
        </div>
    </div>
</body>
</html>