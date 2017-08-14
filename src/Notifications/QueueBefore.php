<?php

namespace P3in\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Events\JobProcessing;

class QueueBefore extends Notification
{
    use Queueable;

    private $event;
    private $subject = 'A Queued Command is Starting!';
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobProcessing $event)
    {
        // $event->connectionName
        // $event->job
        // $event->job->payload()
        $this->event = $event;
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
        return info($this->subject, [
            'name' => $this->event->connectionName,
            'Payload' => $this->event->job->payload(),
        ]);
    }

    /**
     * toMail
     *
     * @param      mixed    $notifiable
     * @return     \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        info($this->event->job->payload());
        return (new MailMessage)
            ->subject($this->subject)
            ->line($this->subject);
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
            ->success()
            ->content($this->subject)
            ->attachment(function ($attachment) {
                $attachment->title($this->event->connectionName) // second param can be URL.
                    ->fields([
                        'Payload' => $this->event->job->payload(),
                    ]);
            });
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

    /**
     * toDatabase
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            //
        ];
    }
}
