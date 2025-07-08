<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Repository;

use App\NotificationPublisher\Domain\Entity\NotificationAttempt;
use App\NotificationPublisher\Domain\Repository\NotificationAttemptRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class NotificationAttemptRepository implements NotificationAttemptRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(NotificationAttempt $attempt): void
    {
        $this->em->persist($attempt);
        $this->em->flush();
    }

    public function findById(string $id): ?NotificationAttempt
    {
        return $this->em->getRepository(NotificationAttempt::class)->find($id);
    }

    public function findByNotificationId(string $notificationId): array
    {
        return $this->em->getRepository(NotificationAttempt::class)->findBy([
            'notificationId' => $notificationId,
        ]);
    }

    public function findFailedAttemptsByProvider(string $providerName, \DateTimeImmutable $since): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(NotificationAttempt::class, 'a')
            ->where('a.providerName = :provider')
            ->andWhere('a.status = :failed')
            ->andWhere('a.createdAt >= :since')
            ->setParameters([
                'provider' => $providerName,
                'failed' => 'FAILED',
                'since' => $since,
            ])
            ->getQuery()
            ->getResult();
    }
}

