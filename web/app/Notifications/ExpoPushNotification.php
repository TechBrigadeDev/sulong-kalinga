<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\ExpoMessage;

class ExpoPushNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param string $token
     */
    public function __construct($title, $message, $token)
    {
        $this->title = $title;
        $this->message = $message;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['expo'];
    }

    /**
     * Get the Expo representation of the notification.
     *
     * @param mixed $notifiable
     * @return \NotificationChannels\Expo\ExpoMessage
     */
    public function toExpo($notifiable)
    {
        return ExpoMessage::create($this->title)
            ->body($this->message)
            ->priority('high')
            ->expiresAt(now()->addHour());
    }

    /**
     * Route notification for Expo.
     *
     * @param mixed $notifiable
     * @return string
     */
    public function routeNotificationForExpo($notifiable)
    {
        return $this->token;
    }
}
