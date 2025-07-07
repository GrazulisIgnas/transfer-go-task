<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Repository;

use App\NotificationPublisher\Domain\Entity\NotificationAttempt;

interface NotificationAttemptRepositoryInterface
{
    public function save(NotificationAttempt $attempt): void;

    public function findById(string $id): ?NotificationAttempt;

    public function findByNotificationId(string $notificationId): array;

    public function findFailedAttemptsByProvider(string $providerName, \DateTimeImmutable $since): array;
}
