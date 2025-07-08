<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Entity;

use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\NotificationStatus;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'notifications')]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $userId;

    #[ORM\Embedded(class: Recipient::class)]
    private Recipient $recipient;

    #[ORM\Embedded(class: Message::class)]
    private Message $message;

    #[ORM\Column(type: 'notification_channel')]
    private NotificationChannel $channel;

    #[ORM\Column(type: 'notification_status')]
    private NotificationStatus $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $sentAt = null;

    #[ORM\Column(type: 'integer')]
    private int $maxRetries;

    #[ORM\Column(type: 'integer')]
    private int $currentRetries = 0;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(type: 'json')]
    private array $metadata = [];

    #[ORM\OneToMany(mappedBy: 'notification', targetEntity: NotificationAttempt::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attempts;

    public function __construct(
        string $id,
        string $userId,
        Recipient $recipient,
        Message $message,
        NotificationChannel $channel,
        int $maxRetries = 3,
        array $metadata = [],
        ?DateTimeImmutable $scheduledAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->recipient = $recipient;
        $this->message = $message;
        $this->channel = $channel;
        $this->status = NotificationStatus::pending();
        $this->createdAt = new DateTimeImmutable();
        $this->maxRetries = $maxRetries;
        $this->metadata = $metadata;
        $this->scheduledAt = $scheduledAt;
        $this->attempts = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getChannel(): NotificationChannel
    {
        return $this->channel;
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function getCurrentRetries(): int
    {
        return $this->currentRetries;
    }

    public function getScheduledAt(): ?DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getAttempts(): Collection
    {
        return $this->attempts;
    }

    public function markAsSent(): void
    {
        $this->status = NotificationStatus::sent();
        $this->sentAt = new DateTimeImmutable();
    }

    public function markAsFailed(): void
    {
        $this->status = NotificationStatus::failed();
    }

    public function markAsProcessing(): void
    {
        $this->status = NotificationStatus::processing();
    }

    public function incrementRetries(): void
    {
        $this->currentRetries++;
    }

    public function canRetry(): bool
    {
        return $this->currentRetries < $this->maxRetries;
    }

    public function addAttempt(NotificationAttempt $attempt): void
    {
        $this->attempts->add($attempt);
    }

    public function isReadyToSend(): bool
    {
        if ($this->scheduledAt === null) {
            return true;
        }

        return $this->scheduledAt <= new DateTimeImmutable();
    }
}
