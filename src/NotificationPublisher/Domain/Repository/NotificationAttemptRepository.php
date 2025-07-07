<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Repository;

use App\NotificationPublisher\Domain\Entity\NotificationAttempt;

class NotificationAttemptRepository implements NotificationAttemptRepositoryInterface
{

    public function save(NotificationAttempt $attempt): void
    {
        // TODO: Implement save() method.
    }

    public function findById(string $id): ?NotificationAttempt
    {
        return null;
    }

    public function findByNotificationId(string $notificationId): array
    {
        return [];
    }

    public function findFailedAttemptsByProvider(string $providerName, \DateTimeImmutable $since): array
    {
        return [];
    }
}
