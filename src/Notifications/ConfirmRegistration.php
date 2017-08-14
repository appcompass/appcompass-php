<?php

namespace P3in\Notifications;

use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use P3in\Models\Website;

class ConfirmRegistration extends Notification
{
    /**
     * The user registration activation code.
     *
     * @var string
     */
    public $activation_code;

    /**
     * Create a notification instance.
     *
     * @param  string  $activation_code
     * @return void
     */
    public function __construct($activation_code)
    {
        $this->activation_code = $activation_code;
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
        $request = Request::capture();
        $website = Website::fromRequest($request);

        return (new MailMessage)
            ->line("You are receiving this email because you registered an account on {$website->url}.")
            ->action('Confirm account', route('cp-activate-account', $this->activation_code))
            ->line('If you did not register this account, no further action is required.');
    }
}
