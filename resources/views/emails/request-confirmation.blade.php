<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Collection Scheduled - Karakib</title>
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
            margin-bottom: 25px;
            font-size: 16px;
        }

        .qr-container {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8dfc8 100%);
            border: 3px solid #3d7c3d;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }

        .qr-title {
            color: #2d5f2d;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .qr-code {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            margin: 15px 0;
        }

        .qr-code img {
            width: 200px;
            height: 200px;
            display: block;
        }

        .request-id {
            color: #2d5f2d;
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .qr-instruction {
            color: #666666;
            font-size: 14px;
            margin-top: 15px;
            font-style: italic;
        }

        .collection-box {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin: 25px 0;
        }

        .collection-title {
            color: #2d5f2d;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #777777;
            font-weight: bold;
        }

        .info-value {
            color: #2d5f2d;
            font-weight: bold;
            text-align: right;
        }

        .address-box {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #3d7c3d;
            margin: 20px 0;
        }

        .address-title {
            color: #2d5f2d;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .address-text {
            color: #555555;
            line-height: 1.8;
        }

        .waste-items {
            margin: 20px 0;
        }

        .waste-item {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .waste-name {
            color: #2d5f2d;
            font-weight: bold;
        }

        .waste-quantity {
            color: #666666;
            font-size: 14px;
        }

        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 5px;
        }

        .warning-text {
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-block;
            background-color: #3d7c3d;
            color: #ffffff;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
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

            .qr-code img {
                width: 180px;
                height: 180px;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }

            .info-value {
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="{{ url('https://karakib.netlify.app/logos/logo_horizontal.png') }}" alt="Karakib Logo">
            </div>
            <div class="tagline">Every small act of recycling creates a cleaner tomorrow</div>
        </div>

        <div class="content">
            <div class="greeting">Collection Request Confirmed!</div>

            <div class="message">
                Hi {{ $request->user->name }}, great news! Your waste collection has been scheduled. Our courier will arrive at your location soon.
            </div>

            <div style="text-align: center;">
                <span class="status-badge">✓ Pickup Scheduled</span>
            </div>

            <div class="qr-container">
                <div class="qr-title">🔍 Show This QR Code to Courier</div>
                <div class="qr-code">
                    <img src="{{ $request->qr_code_image }}" alt="Collection QR Code">
                </div>
                <div class="request-id">Request ID: #{{ $request->request_number }}</div>
                <div class="qr-instruction">
                    The courier will scan this code to confirm your pickup
                </div>
            </div>

            <div class="collection-box">
                <div class="collection-title">
                    Collection Details
                </div>
                <div class="info-row">
                    <span class="info-label">Request Date:</span>
                    <span class="info-value">{{ $request->created_at->format('F d, Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estimated Arrival:</span>
                    <span class="info-value">{{ 'Within 2-3 days' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value" style="color: #3d7c3d;">{{ ucfirst($request->status) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Expected Points:</span>
                    <span class="info-value" style="color: #3d7c3d;">+{{ $request->total ?? 0 }} Points</span>
                </div>
            </div>

            <div class="address-box">
                <div class="address-title">📍 Pickup Address</div>
                <div class="address-text">
                    {{ $request->address->street_address }},<br>
                    {{ $request->address->city }}<br>
                    Phone: {{ $request->address->phone }}<br>
                </div>
            </div>

            <div class="collection-box">
                <div class="collection-title">
                    Items for Collection
                </div>
                <div class="waste-items">
                    @foreach($request->items as $item)
                    <div class="waste-item">
                        <div>
                            <div class="waste-name">{{ $item->item->name }}</div>
                            <div class="waste-quantity">Type: {{ $item->item->wasteType->name }}</div>
                        </div>
                        <div class="waste-quantity">{{ $item->quantity }} {{ $item->item->unit }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="warning-box">
                <div class="warning-text">
                    <strong>⚠️ Important Instructions:</strong><br>
                    • Please have your items ready and accessible<br>
                    • Keep this QR code ready on your phone or printed<br>
                    • Ensure someone is available at the pickup address<br>
                    • Items should be clean and separated by type
                </div>
            </div>

            <div class="divider"></div>

            <div class="message" style="text-align: center; background-color: #e8f5e8; padding: 20px; border-radius: 10px;">
                <div style="color: #3d7c3d; font-weight: bold; font-size: 18px; margin-bottom: 10px;">
                    Thank You for Making a Difference!
                </div>
                <div style="color: #555555; font-style: italic;">
                    Your contribution helps keep Egypt clean and creates a sustainable future for everyone.
                </div>
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