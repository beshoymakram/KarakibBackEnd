<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $order = Order::where('id', $this->order->id)->first();
        $qrCodePath = null;

        try {
            // Generate QR code file for attachment
            $qrCodePath = \App\Services\QrCodeService::generateQrCodeFile(
                $order->qr_code,
                250
            );

            return (new MailMessage)
                ->subject('Order Confirmation #' . $order->order_number . ' - Karakib')
                ->view('emails.order-confirmation', ['order' => $order])
                ->attach($qrCodePath, [
                    'as' => 'order-qr-code.png',
                    'mime' => 'image/png',
                ]);
        } finally {
            // Clean up temporary file after email is sent
            if ($qrCodePath && file_exists($qrCodePath)) {
                @unlink($qrCodePath);
            }
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
