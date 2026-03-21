<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Approved</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .pin-box {
            background: white;
            border: 2px solid #0ea5e9;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
        }
        .pin {
            font-size: 32px;
            font-weight: bold;
            color: #0ea5e9;
            letter-spacing: 8px;
        }
        .info-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .button {
            display: inline-block;
            background: #0ea5e9;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Congratulations!</h1>
        <p>Your Beach Owner Application has been Approved</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $application->full_name }},</p>
        
        <p>We're excited to inform you that your application to become a beach owner on <strong>Dagat Ta bAI</strong> has been <strong>APPROVED</strong>!</p>
        
        <div class="info-box">
            <strong>Business:</strong> {{ $application->business_name }}<br>
            <strong>Email:</strong> {{ $application->email }}
        </div>
        
        <p>Your login credentials are below. Please keep your PIN secure:</p>
        
        <div class="pin-box">
            <div style="color: #6b7280; font-size: 14px; margin-bottom: 10px;">Your 6-Digit PIN</div>
            <div class="pin">{{ $pin }}</div>
        </div>
        
        <p style="text-align: center;">
            <a href="{{ url('/admin/login') }}" class="button">Login to Your Account</a>
        </p>
        
        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>Login using your email and the PIN above</li>
            <li>Complete your beach resort profile</li>
            <li>Add your beach location details and photos</li>
            <li>Start receiving bookings!</li>
        </ul>
        
        <p>If you have any questions, feel free to contact our support team.</p>
        
        <p>Welcome to the Dagat Ta bAI family!<br>
        <em>The Dagat Ta bAI Team</em></p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
