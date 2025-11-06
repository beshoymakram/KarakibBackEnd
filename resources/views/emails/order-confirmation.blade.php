<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Karakib</title>
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


        .order-box {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8dfc8 100%);
            border-radius: 15px;
            padding: 25px;
            margin: 25px 0;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #3d7c3d;
        }

        .order-number {
            color: #2d5f2d;
            font-weight: bold;
            font-size: 18px;
        }

        .order-date {
            color: #666666;
            font-size: 14px;
        }

        .order-items {
            margin: 20px 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #d0e7d0;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-name {
            color: #2d5f2d;
            font-weight: bold;
            flex: 1;
        }

        .item-qty {
            color: #666666;
            margin: 0 15px;
        }

        .item-price {
            color: #2d5f2d;
            font-weight: bold;
        }

        .order-summary {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: #555555;
        }

        .summary-row.total {
            border-top: 2px solid #3d7c3d;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #2d5f2d;
        }

        .shipping-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
        }

        .shipping-title {
            color: #2d5f2d;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .shipping-address {
            color: #555555;
            line-height: 1.6;
            font-size: 14px;
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

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
            <div class="greeting">Thank You for Your Order!</div>

            <div class="message">
                Hi {{ $order->user->name }}, your order has been confirmed and is being prepared. We'll notify you once it's on its way!
            </div>

            <div style="text-align: center;">
                <span class="status-badge">✓ Order Confirmed</span>
            </div>

            <div class="qr-container">
                <div class="qr-title">🔍 Show This QR Code to Courier</div>
                <div class="qr-code">
                    <img src="{{ $order->qr_code_image }}" alt="Collection QR Code">
                </div>
                <div class="order-id">Order ID: #{{ $order->order_number }}</div>
                <div class="qr-instruction">
                    The courier will scan this code to confirm your delivery
                </div>
            </div>

            <div class="order-box">
                <div class="order-header">
                    <div>
                        <div class="order-number">Order #{{ $order->order_number }}</div>
                        <div class="order-date">{{ $order->created_at->format('F d, Y - h:i A') }}</div>
                    </div>
                </div>

                <div class="order-items">
                    @foreach($order->items as $item)
                    <div class="item">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-qty">x{{ $item->quantity }}</div>
                        <div class="item-price">{{ number_format($item->price * $item->quantity, 2) }} EGP</div>
                    </div>
                    @endforeach
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>{{ number_format($order->total, 2) }} EGP</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>{{ number_format(20, 2) }} EGP</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>{{ number_format($order->total, 2) }} EGP</span>
                    </div>
                </div>
            </div>

            <div class="shipping-box">
                <div class="shipping-title">Delivery Address</div>
                <div class="shipping-address">
                    {{ $order->address->name }}<br>
                    {{ $order->address->phone }}<br>
                    {{ $order->address->street_address }},<br>
                    {{ $order->address->city }}<br>
                </div>
            </div>

            <div class="divider"></div>

            <div class="message" style="text-align: center; background-color: #e8f5e8; padding: 20px; border-radius: 10px;">
                <div style="font-size: 32px; margin-bottom: 10px;">🌱</div>
                <div style="color: #3d7c3d; font-style: italic;">
                    Thank you for supporting Karakib!<br>
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
