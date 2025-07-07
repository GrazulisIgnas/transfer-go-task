<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Repository;

use App\NotificationPublisher\Domain\Entity\Notification;
use App\NotificationPublisher\Domain\ValueObject\NotificationStatus;

interface NotificationRepositoryInterface
{
    public function save(Notification $notification): void;

    public function findById(string $id): ?Notification;

    public function findByUserId(string $userId): array;

    public function findByStatus(NotificationStatus $status, int $limit = 100): array;

    public function findScheduledNotifications(\DateTimeImmutable $before): array;

    public function findFailedNotificationsForRetry(int $limit = 100): array;

    public function delete(Notification $notification): void;
}
