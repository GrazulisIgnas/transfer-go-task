<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Service;

class NotificationResult
{
    private bool $successful;
    private ?string $errorMessage;
    private ?string $providerResponse;
    private int $httpStatusCode;
    private array $metadata;

    public function __construct(
        bool $successful,
        ?string $errorMessage = null,
        ?string $providerResponse = null,
        int $httpStatusCode = 0,
        array $metadata = []
    ) {
        $this->successful = $successful;
        $this->errorMessage = $errorMessage;
        $this->providerResponse = $providerResponse;
        $this->httpStatusCode = $httpStatusCode;
        $this->metadata = $metadata;
    }

    public static function success(?string $providerResponse = null, array $metadata = []): self
    {
        return new self(true, null, $providerResponse, 200, $metadata);
    }

    public static function failure(
        string $errorMessage,
        int $httpStatusCode = 0,
        ?string $providerResponse = null,
        array $metadata = []
    ): self {
        return new self(false, $errorMessage, $providerResponse, $httpStatusCode, $metadata);
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

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
