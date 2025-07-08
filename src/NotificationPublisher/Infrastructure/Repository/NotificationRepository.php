<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Repository;

use App\NotificationPublisher\Domain\Entity\Notification;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use App\NotificationPublisher\Domain\ValueObject\NotificationStatus;
use Doctrine\ORM\EntityManagerInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Notification $notification): void
    {
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function findById(string $id): ?Notification
    {
        return $this->em->getRepository(Notification::class)->find($id);
    }

    public function findByUserId(string $userId): array
    {
        return $this->em->getRepository(Notification::class)
            ->findBy(['userId' => $userId]);
    }

    public function findByStatus(NotificationStatus $status, int $limit = 100): array
    {
        return $this->em->getRepository(Notification::class)->findBy(
            ['status' => $status],
            ['createdAt' => 'ASC'],
            $limit
        );
    }

    public function findScheduledNotifications(\DateTimeImmutable $before): array
    {
        return $this->em->createQueryBuilder()
            ->select('n')
            ->from(Notification::class, 'n')
            ->where('n.scheduledAt <= :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult();
    }

    public function findFailedNotificationsForRetry(int $limit = 100): array
    {
        return $this->em->createQueryBuilder()
            ->select('n')
            ->from(Notification::class, 'n')
            ->where('n.status = :failed')
            ->orderBy('n.updatedAt', 'ASC')
            ->setParameter('failed', NotificationStatus::FAILED)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function delete(Notification $notification): void
    {
        $this->em->remove($notification);
        $this->em->flush();
    }
}
