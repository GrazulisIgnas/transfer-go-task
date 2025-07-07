<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

class NotificationStatus
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const SENT = 'sent';
    public const FAILED = 'failed';
    public const SCHEDULED = 'scheduled';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function sent(): self
    {
        return new self(self::SENT);
    }

    public static function failed(): self
    {
        return new self(self::FAILED);
    }

    public static function scheduled(): self
    {
        return new self(self::SCHEDULED);
    }

    public static function fromString(string $value): self
    {
        if (!in_array($value, [self::PENDING, self::PROCESSING, self::SENT, self::FAILED, self::SCHEDULED], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid notification status: %s', $value));
        }

        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->value === self::PROCESSING;
    }

    public function isSent(): bool
    {
        return $this->value === self::SENT;
    }

    public function isFailed(): bool
    {
        return $this->value === self::FAILED;
    }

    public function isScheduled(): bool
    {
        return $this->value === self::SCHEDULED;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
