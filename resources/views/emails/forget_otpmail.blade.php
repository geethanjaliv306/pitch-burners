<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        /* Reset styles */
        body, table, td, div, p, a {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
        }
        
        /* Base styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        
        /* Container styles */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header styles */
        .header {
            background: #4169E1;
            padding: 30px 0;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        
        .header img {
            width: 80px;
            height: auto;
        }
        
        /* Content styles */
        .content {
            background: #ffffff;
            padding: 40px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* OTP styles */
        .otp-container {
            margin: 30px 0;
            text-align: center;
        }
        
        .otp-code {
            background: #f8f9fa;
            padding: 15px 30px;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #4169E1;
            border-radius: 8px;
            display: inline-block;
            margin: 20px 0;
        }
        
        /* Typography */
        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        p {
            color: #666666;
            font-size: 16px;
            margin: 0 0 20px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            color: #999999;
            font-size: 14px;
        }
        
        /* Responsive styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                padding: 10px;
            }
            
            .content {
                padding: 20px;
            }
            
            .otp-code {
                font-size: 24px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">       
        <div class="content">
            <h1>Hello {{ isset($user) ? $user->name : 'User' }},</h1>
            
            <p>You've requested to reset your password. Please use the verification code below to continue:</p>
            
            <div class="otp-container">
                <div class="otp-label">Your OTP is: <b>{{ $otp }}</b></div>
            </div>
            
            <p>This code will expire in 5 minutes for security reasons. If you didn't request this password reset, please ignore this email or contact support if you have concerns.</p>
            
            <div class="footer">
                <p>Thanks and Regards,<br>Pitch Burners</p>
            </div>
        </div>
    </div>
</body>
</html>