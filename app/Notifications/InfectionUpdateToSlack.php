<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class InfectionUpdateToSlack extends Notification
{
    use Queueable;

    private $country;
    private $state;
    private $confirmed;
    private $infected;
    private $recovered;
    private $deaths;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($country,$state,$confirmed,$infected,$recovered,$deaths)
    {
        $this->country = $country;
        $this->state = $state;
        $this->confirmed = $confirmed;
        $this->infected = $infected;
        $this->recovered = $recovered;
        $this->deaths = $deaths;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
                ->success()
                ->content("Nova Atualização")
                ->attachment(function ($attachment) {
                    $attachment->title($this->country)
                               ->fields([
                                    'Country' => $this->country,
                                    'State' => $this->state,
                                    'Confirmed' => $this->confirmed,
                                    'Infected' => $this->infected,
                                    'Recovered' => $this->recovered,
                                    'Deaths' => $this->deaths,
                                ]);
                });

                
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'content' => $this->content,
        ];
    }
}
