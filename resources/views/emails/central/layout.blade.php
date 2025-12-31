<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mazal CRM')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            padding: 40px 20px 20px;
            background-color: #ffffff;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563EB;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .logo svg {
            display: inline-block;
            vertical-align: middle;
        }

        .header p {
            margin-top: 5px;
            margin-bottom: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .content {
            padding: 40px;
        }

        .footer {
            text-align: center;
            padding: 30px 20px;
            color: #9ca3af;
            font-size: 13px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        /* Common Utility Classes */
        .cta-container {
            text-align: center;
            margin: 40px 0;
        }

        .cta-button {
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
            transition: background-color 0.2s;
        }

        .cta-button:hover {
            background-color: #1d4ed8;
        }

        .help-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 25px;
            text-align: center;
            border-radius: 4px;
        }

        .help-text {
            color: #1e40af;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        .help-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }

        .help-link:hover {
            text-decoration: underline;
        }

        /* Child specific styles can be injected here */
        @yield('styles')

        /* Responsive adjustments */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .content {
                padding: 20px !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    style="display:inline-block; vertical-align: middle; margin-right: 8px;">
                    <path d="M4 4H10V10H4V4Z" fill="#2563EB" />
                    <path d="M14 4H20V10H14V4Z" fill="#2563EB" fill-opacity="0.5" />
                    <path d="M4 14H10V20H4V14Z" fill="#2563EB" fill-opacity="0.5" />
                    <path d="M14 14H20V20H14V14Z" fill="#2563EB" />
                </svg>
                <span style="display:inline-block; vertical-align: middle;">MAZAL CRM</span>
            </div>
            <p>@yield('sub-header', 'Your Business Growth Partner')</p>
        </div>

        <!-- Hero/Banner -->
        @yield('hero-banner')

        <!-- Main Content -->
        <div class="content">
            @yield('content')

            <!-- Default Help Section if not overridden -->
            @section('help-section')
            <div class="help-box">
                <span class="help-text">🙋 Need help getting started?</span>
                <a href="#" class="help-link">Visit our Documentation Center →</a>
            </div>
            @show
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>If you have any questions or need assistance, our support team is here to help.</p>
            <div style="margin-top: 20px; font-size: 11px; opacity: 0.7;">
                &copy; {{ date('Y') }} Mazal CRM. All rights reserved.<br>
                123 Business Street, Tech City, TC 90210
            </div>
        </div>
    </div>
</body>

</html>