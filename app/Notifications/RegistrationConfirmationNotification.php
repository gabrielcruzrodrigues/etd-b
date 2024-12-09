<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RegistrationConfirmationNotification extends Notification
{

  /**
   * Obtenha os canais de entrega da notificação.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Obtenha a representação da notificação por e-mail.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    $estudieEmail = config('mail.from.address');

    $estudieLoginUrl = config('app.estudie_login_url');

    return (new MailMessage)
      ->subject('Seja muito bem-vindo(a) à Estudie')
      ->view('mails.registration-confirmation-notification', [
        'name' => $notifiable->name,
        'estudieEmail' => $estudieEmail,
        'estudieLoginUrl' => $estudieLoginUrl
      ]);
  }

  /**
   * Obtenha a representação da notificação no array.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
