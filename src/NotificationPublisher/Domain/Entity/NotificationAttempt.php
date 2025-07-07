<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Entity;

use DateTimeImmutable;

class NotificationAttempt
{
    private string $id;
    private string $notificationId;
    private string $providerName;
    private DateTimeImmutable $attemptedAt;
    private bool $successful;
    private ?string $errorMessage = null;
    private ?string $providerResponse = null;
    private int $httpStatusCode = 0;

    public function __construct(
        string $id,
        string $notificationId,
        string $providerName,
        bool $successful,
        ?string $errorMessage = null,
        ?string $providerResponse = null,
        int $httpStatusCode = 0
    ) {
        $this->id = $id;
        $this->notificationId = $notificationId;
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

    public function getNotificationId(): string
    {
        return $this->notificationId;
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
