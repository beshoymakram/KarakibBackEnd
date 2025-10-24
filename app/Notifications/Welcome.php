<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Welcome extends Notification implements ShouldQueue
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
            ->from($address = 'info@karakib.netlify.com', $name = 'Karakib')
            ->subject('Welcome to Karakib!')
            ->greeting("Dear " . $notifiable->name . ',')
            ->line('Thank you for selecting and supporting Karakib!')
            ->line('Your account is now active and you can start making money and save the enviroment as well.')
            ->line('For immediate queries connect with us on WhatsApp,')
            ->action('Get Started', env('FRONTEND_URL'))
            ->line('Feel free to reply to this email if you have any specific questions.');
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
