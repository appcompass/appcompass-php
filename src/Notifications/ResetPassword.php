<?php

namespace AppCompass\AppCompass\Notifications;

use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use AppCompass\AppCompass\Models\Website;

class ResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $website = request()->web_property;

        // @TODO: we shouldn't assume the end point for a password reset form on the frontend will always be /reset-password
        // we should set this as a page of a specific type or set config info pointing to the reset password page for a given website.
        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $website->url.'/login/reset/'.$this->token)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
