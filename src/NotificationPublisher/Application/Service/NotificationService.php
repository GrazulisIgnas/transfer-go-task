<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Service;

use App\NotificationPublisher\Domain\Entity\Notification;
use App\NotificationPublisher\Domain\Entity\NotificationAttempt;
use App\NotificationPublisher\Domain\Repository\NotificationAttemptRepositoryInterface;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class NotificationService
{
    private FailoverManager $failoverManager;
    private NotificationRepositoryInterface $notificationRepository;
    private NotificationAttemptRepositoryInterface $attemptRepository;
    private LoggerInterface $logger;

    public function __construct(
        FailoverManager                        $failoverManager,
        NotificationRepositoryInterface        $notificationRepository,
        NotificationAttemptRepositoryInterface $attemptRepository,
        LoggerInterface                        $logger
    ) {
        $this->failoverManager = $failoverManager;
        $this->notificationRepository = $notificationRepository;
        $this->attemptRepository = $attemptRepository;
        $this->logger = $logger;
    }

    public function sendNotification(Notification $notification): void
    {
        try {
            // Mark as processing
            $notification->markAsProcessing();
            $this->notificationRepository->save($notification);

            // Get available providers with failover support
            $result = $this->failoverManager->sendWithFailover(
                $notification->getChannel(),
                $notification->getRecipient(),
                $notification->getMessage()
            );

            // Create an attempt record
            $attempt = new NotificationAttempt(
                Uuid::uuid4()->toString(),
                $notification,
                $result['provider'],
                $result['result']->isSuccessful(),
                $result['result']->getErrorMessage(),
                $result['result']->getProviderResponse(),
                $result['result']->getHttpStatusCode()
            );

            $this->attemptRepository->save($attempt);
            $notification->addAttempt($attempt);

            if ($result['result']->isSuccessful()) {
                $notification->markAsSent();
                $this->logger->info('Notification sent successfully', [
                    'notification_id' => $notification->getId(),
                    'provider' => $result['provider'],
                    'channel' => $notification->getChannel()->getValue(),
                ]);
            } else {
                $notification->markAsFailed();
                $this->handleFailedNotification($notification, $result);
            }

        } catch (\Throwable $exception) {
            $notification->markAsFailed();
            $this->handleNotificationException($notification, $exception);
        }

        $this->notificationRepository->save($notification);
    }

    private function handleFailedNotification(Notification $notification, array $result): void
    {
        $notification->incrementRetries();

        if ($notification->canRetry()) {
            $this->logger->warning('Notification failed, will retry', [
                'notification_id' => $notification->getId(),
                'provider' => $result['provider'],
                'error' => $result['result']->getErrorMessage(),
                'retry_count' => $notification->getCurrentRetries(),
            ]);
        } else {
            $notification->markAsFailed();
            $this->logger->error('Notification failed permanently', [
                'notification_id' => $notification->getId(),
                'provider' => $result['provider'],
                'error' => $result['result']->getErrorMessage(),
            ]);
        }
    }

    private function handleNotificationException(Notification $notification, \Throwable $exception): void
    {
        $notification->incrementRetries();

        if (!$notification->canRetry()) {
            $notification->markAsFailed();
        }

        $this->logger->error('Notification processing failed with exception', [
            'notification_id' => $notification->getId(),
            'exception' => $exception->getMessage(),
            'retry_count' => $notification->getCurrentRetries(),
        ]);
    }
}
