<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update</title>
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
            background: #374151;
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
        .info-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .reason-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
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
        <h1>Application Status Update</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $application->full_name }},</p>
        
        <p>Thank you for your interest in becoming a beach owner on <strong>Dagat Ta bAI</strong>.</p>
        
        <p>After careful review of your application for <strong>{{ $application->business_name }}</strong>, we regret to inform you that we are unable to approve your application at this time.</p>
        
        @if($application->rejection_reason)
        <div class="reason-box">
            <strong>Reason:</strong><br>
            {{ $application->rejection_reason }}
        </div>
        @endif
        
        <p><strong>You may reapply if:</strong></p>
        <ul>
            <li>You can provide additional documentation</li>
            <li>You have updated your business information</li>
            <li>You can address the concerns mentioned above</li>
        </ul>
        
        <p>We encourage you to review your application and submit again with complete requirements.</p>
        
        <p style="text-align: center;">
            <a href="{{ url('/') }}" class="button">Visit Our Website</a>
        </p>
        
        <p>If you have any questions about this decision, please contact our support team.</p>
        
        <p>Best regards,<br>
        <em>The Dagat Ta bAI Team</em></p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
