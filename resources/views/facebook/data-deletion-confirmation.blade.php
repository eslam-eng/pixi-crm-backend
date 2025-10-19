<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Deletion Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success-icon {
            color: #4CAF50;
            font-size: 48px;
            text-align: center;
            margin-bottom: 20px;
        }
        .confirmation-code {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        
        <h1>Data Deletion Request Confirmed</h1>
        
        <p>Your data deletion request has been successfully processed.</p>
        
        <div class="info-box">
            <h3>Request Details:</h3>
            <p><strong>User ID:</strong> {{ $user_id }}</p>
            @if($confirmation_code)
                <p><strong>Confirmation Code:</strong></p>
                <div class="confirmation-code">{{ $confirmation_code }}</div>
            @endif
            <p><strong>Date:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
        
        <h3>What happens next?</h3>
        <ul>
            <li>Your personal data has been removed from our systems</li>
            <li>Any Facebook-related data associated with your account has been deleted</li>
            <li>This action cannot be undone</li>
            <li>You will receive a confirmation email shortly</li>
        </ul>
        
        <div class="info-box">
            <h3>Important Notes:</h3>
            <ul>
                <li>This deletion only affects data stored by our application</li>
                <li>Your Facebook account and data on Facebook remain unchanged</li>
                <li>If you have any questions, please contact our support team</li>
            </ul>
        </div>
        
        <p style="text-align: center; margin-top: 30px;">
            <a href="/" style="background: #1877f2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                Return to Homepage
            </a>
        </p>
    </div>
</body>
</html>
