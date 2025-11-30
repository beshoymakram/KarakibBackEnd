<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Karakib</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #c8dfc8;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #ffffff;
            /* or #f8f9fa for slight gray */
            padding: 40px 20px;
            text-align: center;
            border-bottom: 4px solid #3d7c3d;
        }

        .tagline {
            color: #3d7c3d;
            /* Change from light to dark green */
            font-size: 14px;
            font-style: italic;
        }

        .logo {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            height: 75px;
            width: auto;
            max-width: 200px;
            margin: auto;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 24px;
            color: #2d5f2d;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .message {
            color: #555555;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .otp-container {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8dfc8 100%);
            border: 3px dashed #3d7c3d;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-label {
            color: #2d5f2d;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: #2d5f2d;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }

        .expiry {
            color: #777777;
            font-size: 14px;
            margin-top: 15px;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 5px;
        }

        .warning-text {
            color: #856404;
            font-size: 14px;
            line-height: 1.5;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #c8dfc8, transparent);
            margin: 30px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #777777;
            font-size: 14px;
        }

        .footer-message {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .eco-message {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }

        .eco-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .eco-text {
            color: #3d7c3d;
            font-size: 14px;
            font-style: italic;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            color: #3d7c3d;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }

            .otp-code {
                font-size: 36px;
                letter-spacing: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="{{ url('https://karakib.netlify.app/logos/logo_horizontal.png') }}" alt="Karakib Logo">
            </div>
            <div class="tagline">Every small act of recycling creates a cleaner tomorrow</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $user->name ?? 'there' }}! 👋</div>

            <div class="message">
                We received a request to reset your Karakib account password.
                Use the verification code below to proceed with resetting your password.
            </div>

            <!-- OTP Box -->
            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="expiry">⏰ This code expires at {{ $expiresAt }}</div>
            </div>

            <!-- Warning -->
            <div class="warning">
                <div class="warning-text">
                    <strong>🔒 Security Notice:</strong> If you didn't request this password reset,
                    please ignore this email. Your account remains secure.
                </div>
            </div>

            <div class="divider"></div>

            <!-- Eco Message -->
            <div class="eco-message">
                <div class="eco-text">
                    Thank you for being part of our mission to keep Egypt clean and green!
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">
                <strong>Karakib</strong> - An eco-friendly platform that rewards recycling<br>
                and supports a sustainable future.
            </div>

            <div class="footer-message">
                📧 Need help? Contact us at <a href="mailto:karakib@gmail.com" style="color: #3d7c3d;">karakib@gmail.com</a>
            </div>

            <div class="social-links">
                <a href="#">Facebook</a> |
                <a href="#">Instagram</a> |
                <a href="#">Twitter</a>
            </div>

            <div style="margin-top: 20px; color: #999999; font-size: 12px;">
                © 2025 Karakib. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>