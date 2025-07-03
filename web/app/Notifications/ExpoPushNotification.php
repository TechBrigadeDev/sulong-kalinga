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
    public function __construct($title, $message)
    {
        \Log::info('ExpoPushNotification constructed', [
            'title' => $title,
            'message' => $message,
        ]);
        $this->title = $title;
        $this->message = $message;
        //$this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        \Log::info('ExpoPushNotification via called');
        return ['custom_expo'];
    }

    /**
     * Get the Expo representation of the notification.
     *
     * @param mixed $notifiable
     * @return \NotificationChannels\Expo\ExpoMessage
     */
    public function toExpo($notifiable)
    // {
    //     \Log::info('📩 Entered toExpo method'); // NEW LINE

    //     try {
    //         $message = ExpoMessage::create($this->title)
    //             ->body($this->message)
    //             ->sound('default')
    //             ->priority('high')
    //             ->expiresAt(now()->addHour());

    //         \Log::info('ExpoPushNotification payload', [ // EXISTING
    //             //'to' => $this->token,
    //             'title' => $this->title,
    //             'body' => $this->message,
    //         ]);

    //         return $message;
    //     } catch (\Throwable $e) {
    //         \Log::error('❌ Error inside toExpo: ' . $e->getMessage());
    //         return null;
    //     }
    // }
    {
        \Log::info('📩 Entered toExpo method');
        try {
            $message = ExpoMessage::create($this->title)
                ->body($this->message);
            \Log::info('ExpoPushNotification payload', [
                'title' => $this->title,
                'body' => $this->message,
            ]);
            return $message;
        } catch (\Throwable $e) {
            \Log::error('❌ Error inside toExpo: ' . $e->getMessage());
            return null;
        }
    }

    // /**
    //  * Route notification for Expo.
    //  *
    //  * @param mixed $notifiable
    //  * @return string
    //  */
    // public function routeNotificationForExpo($notifiable)
    // {
    //     return $this->token;
    // }
}
