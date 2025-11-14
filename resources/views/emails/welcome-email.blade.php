<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to CRM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .welcome-message {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .highlight {
            color: #4a5568;
            font-weight: bold;
        }
        .support-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to CRM!</h1>
    </div>

    <div class="content">
        <div class="welcome-message">
            Hello {{ $user->name }},
        </div>

        <p>We're thrilled to have you on board! 🎉</p>

        <p>Your journey with our CRM has just begun, and you now have access to powerful tools designed to help you manage and grow your business with ease.</p>

        <div class="support-section">
            <p><strong>We're Here to Help! 💪</strong></p>
            <p>Our support team is available 24/7 to assist you with any questions or concerns. We're committed to helping you make the most of your CRM experience.</p>
        </div>

        <p>Ready to take your business to the next level? Explore all the features, and whenever you're ready, you can choose the plan that best fits your needs.</p>

        {{--        <center>--}}
{{--            <a href="{{ config('app.url') }}/subscription" class="button">--}}
{{--                Explore Premium Plans--}}
{{--            </a>--}}
{{--        </center>--}}

        <p style="margin-top: 30px;">Best regards,<br>The CRM Team</p>
    </div>
</body>
</html>