<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Otp extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        return (new MailMessage)
            ->subject('Reset Your Karakib Password')
            ->view('emails.otp', [
                'user' => $notifiable,
                'otp' => $notifiable->otp,
                'expiresAt' => $notifiable->otp_expired_at->format('h:i A')
            ]);
        // ->subject('Karakib | Your OTP Code')
        // ->greeting("Hello " . $notifiable->name . '!')
        // ->line('Your OTP Code is : **' . $notifiable->otp . '**')
        // ->action('Verify', env('FRONTEND_URL') . '/verify-code?email=' . $notifiable->email)
        // ->line('Code expires after 10 minutes.')
        // ->line("If you don't recognize this action, kindly change your password and contact the developer ASAP.");
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
