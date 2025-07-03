<?php

namespace App\Notifications\Channels;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use NotificationChannels\Expo\Exceptions\CouldNotSendNotification;
use NotificationChannels\Expo\Gateway\ExpoEnvelope;
use NotificationChannels\Expo\Gateway\ExpoGateway;
use NotificationChannels\Expo\ExpoPushToken;

class CustomExpoChannel
{
    public const NAME = 'custom_expo';

    public function __construct(private ExpoGateway $gateway, private Dispatcher $events)
    {
        //
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $tokens = $this->getTokens($notifiable, $notification);

        if (!count($tokens)) {
            return;
        }

        $message = $this->getMessage($notifiable, $notification);

        $response = $this->gateway->sendPushNotifications(
            ExpoEnvelope::make($tokens, $message)
        );

        if ($response->isFailure()) {
            $this->dispatchFailedEvents($notifiable, $notification, $response->errors());
        } elseif ($response->isFatal()) {
            throw CouldNotSendNotification::becauseTheServiceRespondedWithAnError($response->message());
        }
    }

    private function dispatchFailedEvents(object $notifiable, Notification $notification, array $errors): void
    {
        foreach ($errors as $error) {
            $this->events->dispatch(new \Illuminate\Notifications\Events\NotificationFailed($notifiable, $notification, self::NAME, $error));
        }
    }

    private function getMessage(object $notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toExpo')) {
            throw CouldNotSendNotification::becauseTheMessageIsMissing();
        }

        return $notification->toExpo($notifiable);
    }

    private function getTokens(object $notifiable, Notification $notification): array
    {
        if (method_exists($notifiable, 'routeNotificationForExpo')) {
            return $notifiable->routeNotificationForExpo($notification) ?? [];
        }

        return [];
    }
}