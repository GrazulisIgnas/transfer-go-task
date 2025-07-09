<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

class RetryFailedNotificationCommand
{
    private string $notificationId;

    public function __construct(string $notificationId)
    {
        $this->notificationId = $notificationId;
    }

    public function getNotificationId(): string
    {
        return $this->notificationId;
    }
}
