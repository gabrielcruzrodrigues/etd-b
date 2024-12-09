<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token)
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
        $front_form_url_reset_password = config('app.front_form_url_reset_password');
        
        #confira se esta sendo utlizado barra no final da url do arquivo .env
        $url = "$front_form_url_reset_password/$this->token";

        $estudieEmail = config('mail.from.address');

        return (new MailMessage)
            ->subject('Redefinição de senha')
            ->view('mails.reset-password-notification', [
                'url' => $url,
                'name' => $notifiable->name,
                'estudieEmail' => $estudieEmail,
            ]);
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
