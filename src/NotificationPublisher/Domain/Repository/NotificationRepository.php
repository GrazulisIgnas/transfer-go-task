<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Repository;

class NotificationRepository implements NotificationRepositoryInterface
{

    public function save(\App\NotificationPublisher\Domain\Entity\Notification $notification): void
    {
        // TODO: Implement save() method.
    }

    public function findById(string $id): ?\App\NotificationPublisher\Domain\Entity\Notification
    {
        return null;
    }

    public function findByUserId(string $userId): array
    {
        return [];
    }

    public function findByStatus(\App\NotificationPublisher\Domain\ValueObject\NotificationStatus $status, int $limit = 100): array
    {
        return [];
    }

    public function findScheduledNotifications(\DateTimeImmutable $before): array
    {
        return [];
    }

    public function findFailedNotificationsForRetry(int $limit = 100): array
    {
        return [];
    }

    public function delete(\App\NotificationPublisher\Domain\Entity\Notification $notification): void
    {
        // TODO: Implement delete() method.
    }
}
