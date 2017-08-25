<?php

namespace P3in\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use P3in\Models\FormStorage;

class FormStored extends Notification
{
    use Queueable;

    private $form;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FormStorage $form)
    {
        $this->form = $form;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toLog($notifiable)
    {
        return info('');
    }

    /**
     * toMail
     *
     * @param      mixed    $notifiable
     * @return     \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // info($notifiable);
        // info('Sending Mail');
        // return;
        // info($notifiable->toArray());

        return (new MailMessage)
                    ->line('Hey! Somebody submitted a form.');
                    // ->action('Notification Action', url('/'))
                    // ->line('Thank you for using our application!');
    }

    /**
     * toSlack
     *
     * @param      <type>        $notifiable  The notifiable
     * @return     SlackMessage  ( description_of_the_return_value )
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->to('#leads')
            ->content('One of your invoices has been paid!');
    }

    /**
     * toArray
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
