<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

class NotificationChannel
{
    public const EMAIL = 'email';
    public const SMS = 'sms';
    public const PUSH = 'push';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function email(): self
    {
        return new self(self::EMAIL);
    }

    public static function sms(): self
    {
        return new self(self::SMS);
    }

    public static function push(): self
    {
        return new self(self::PUSH);
    }

    public static function fromString(string $value): self
    {
        if (!in_array($value, [self::EMAIL, self::SMS, self::PUSH], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid notification channel: %s', $value));
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

    public function __toString(): string
    {
        return $this->value;
    }
}
