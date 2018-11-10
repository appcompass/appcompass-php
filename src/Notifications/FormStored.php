<?php

namespace AppCompass\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use AppCompass\Models\FormStorage;

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

    private function formatLine(&$email, $key, $val)
    {
        if (is_array($val) || is_object($val)){
            $email->line("---");
            $email->line("{$key}:");
            foreach ($val as $s_key => $s_val) {
                $this->formatLine($email, $s_key, $s_val);
            }
            $email->line("---");
        }else{
            if ($val) {
                $email->line("{$key}: {$val}");
            }
        }
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
        $email = new MailMessage;

        $email->line('Hey! Somebody submitted a form.');

        if ($this->form->content){
            foreach ($this->form->content as $key => $val) {
                $this->formatLine($email, $key, $val);
            }
        }
        return $email;
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
