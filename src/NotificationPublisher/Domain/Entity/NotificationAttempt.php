<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'notification_attempts')]
class NotificationAttempt
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Notification::class, inversedBy: 'attempts')]
    #[ORM\JoinColumn(name: 'notification_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Notification $notification;

    #[ORM\Column(type: 'string', length: 100)]
    private string $providerName;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $attemptedAt;

    #[ORM\Column(type: 'boolean')]
    private bool $successful;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $providerResponse = null;

    #[ORM\Column(type: 'integer')]
    private int $httpStatusCode = 0;

    public function __construct(
        string $id,
        Notification $notification,
        string $providerName,
        bool $successful,
        ?string $errorMessage = null,
        ?string $providerResponse = null,
        int $httpStatusCode = 0
    ) {
        $this->id = $id;
        $this->notification = $notification;
        $this->providerName = $providerName;
        $this->attemptedAt = new DateTimeImmutable();
        $this->successful = $successful;
        $this->errorMessage = $errorMessage;
        $this->providerResponse = $providerResponse;
        $this->httpStatusCode = $httpStatusCode;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function getAttemptedAt(): DateTimeImmutable
    {
        return $this->attemptedAt;
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getProviderResponse(): ?string
    {
        return $this->providerResponse;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
