<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Handler;

use App\NotificationPublisher\Application\Command\RetryFailedNotificationCommand;
use App\NotificationPublisher\Application\Service\NotificationService;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;

class RetryFailedNotificationHandler
{
    private NotificationService $notificationService;
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        NotificationService $notificationService,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->notificationService = $notificationService;
        $this->notificationRepository = $notificationRepository;
    }

    public function handle(RetryFailedNotificationCommand $command): void
    {
        $notification = $this->notificationRepository->findById($command->getNotificationId());
        
        if ($notification === null) {
            throw new \InvalidArgumentException(
                sprintf('Notification with ID %s not found', $command->getNotificationId())
            );
        }

        if (!$notification->canRetry()) {
            throw new \InvalidArgumentException(
                sprintf('Notification %s has exceeded maximum retry attempts', $command->getNotificationId())
            );
        }

        $this->notificationService->sendNotification($notification);
    }
}
