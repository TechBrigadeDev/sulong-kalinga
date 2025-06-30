<?php

return [
    'listeners' => [
        \Illuminate\Notifications\Events\NotificationSent::class => [
            \App\Listeners\LogExpoNotificationResponse::class,
        ],
        // ...other listeners...
    ],
];