<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetConfirmationNotification extends Notification
{
  /**
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $estudieEmail = config('mail.from.address');
    
    return (new MailMessage)
      ->subject('Senha Alterada com Sucesso')
      ->view('mails.success-password-reset', [
        'name' => $notifiable->name,
        'estudieEmail' => $estudieEmail,
      ]);
  }

  public function toArray($notifiable)
  {
    return [
    ];
  }
}
