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

    public $tries = 3;
    public $timeout = 60;
    public $maxExceptions = 2;

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
        $order = Order::with(['items.product', 'address', 'user'])
            ->where('id', $this->order->id)
            ->first();

        if (!$order) {
            throw new \Exception('Order not found');
        }

        try {
            $mailMessage = (new MailMessage)
                ->subject('Order Confirmation #' . $order->order_number . ' - Karakib')
                ->view('emails.order-confirmation', ['order' => $order]);

            // Generate QR code as binary data (not file) for attachment
            if ($order->qr_code) {
                $qrCodeBinary = \App\Services\QrCodeService::generateQrCodeForEmail(
                    $order->qr_code,
                    250
                );

                // Attach the binary data directly (no temp file needed)
                $mailMessage->attachData(
                    $qrCodeBinary,
                    'order-qr-code.png',
                    ['mime' => 'image/png']
                );
            }

            return $mailMessage;
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());

            // Return email without QR attachment if it fails
            return (new MailMessage)
                ->subject('Order Confirmation #' . $order->order_number . ' - Karakib')
                ->view('emails.order-confirmation', ['order' => $order]);
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
