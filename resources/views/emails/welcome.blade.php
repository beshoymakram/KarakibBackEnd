<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Karakib</title>
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
            padding: 40px 20px;
            text-align: center;
            border-bottom: 4px solid #3d7c3d;
        }

        .tagline {
            color: #3d7c3d;
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
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 28px;
            color: #2d5f2d;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        .message {
            color: #555555;
            line-height: 1.8;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .welcome-box {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8dfc8 100%);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }

        .welcome-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }

        .welcome-text {
            color: #2d5f2d;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .welcome-subtext {
            color: #555555;
            font-size: 14px;
        }

        .features {
            margin: 30px 0;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .feature-icon {
            font-size: 32px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .feature-content {
            flex: 1;
        }

        .feature-title {
            color: #2d5f2d;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .feature-desc {
            color: #666666;
            font-size: 14px;
            line-height: 1.5;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #3d7c3d 0%, #2d5f2d 100%);
            color: #ffffff;
            padding: 15px 40px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin: 20px 0;
            font-size: 16px;
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

            .greeting {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="https://karakib.netlify.app/assets/logo_horizontal-DGy32ZJX.svg" alt="Karakib Logo">
            </div>
            <div class="tagline">Every small act of recycling creates a cleaner tomorrow</div>
        </div>

        <div class="content">
            <div class="greeting">Welcome to Karakib! 🎉</div>

            <div class="welcome-box">
                <div class="welcome-text">Hi {{ $user->name }}!</div>
                <div class="welcome-subtext">You're now part of Egypt's eco-friendly revolution</div>
            </div>

            <div class="message">
                Thank you for joining Karakib! We're thrilled to have you on board. Together, we're making Egypt cleaner and greener, one recyclable at a time.
            </div>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">♻️</div>
                    <div class="feature-content">
                        <div class="feature-title">Recycle & Earn Points</div>
                        <div class="feature-desc">Submit your recyclable waste and earn points for every contribution</div>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🛍️</div>
                    <div class="feature-content">
                        <div class="feature-title">Shop Eco-Friendly Products</div>
                        <div class="feature-desc">Browse our sustainable merchandise and use your points to purchase</div>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🚚</div>
                    <div class="feature-content">
                        <div class="feature-title">Free Pickup Service</div>
                        <div class="feature-desc">Schedule a collection and we'll pick up your recyclables from your doorstep</div>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">💚</div>
                    <div class="feature-content">
                        <div class="feature-title">Make a Difference</div>
                        <div class="feature-desc">Track your environmental impact and see how you're helping Egypt</div>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ env('FRONTEND_URL') }}" class="cta-button">Start Recycling Now</a>
            </div>

            <div class="divider"></div>

            <div class="message" style="text-align: center; color: #3d7c3d; font-style: italic;">
                💡 <strong>Pro Tip:</strong> The more you recycle, the more points you earn. Start your journey today!
            </div>
        </div>

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
