<?php
namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

class LogExpoNotificationResponse
{
    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event)
    {
        if ($event->channel === 'expo') {
            Log::info('Expo HTTP response', [
                'response' => $event->response,
                'notifiable' => get_class($event->notifiable),
                'notification' => get_class($event->notification),
            ]);
        }
    }
}