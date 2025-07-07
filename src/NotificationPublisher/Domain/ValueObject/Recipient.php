<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\PhoneNumber;

class Recipient
{
    private ?Email $email = null;
    private ?PhoneNumber $phoneNumber = null;
    private ?string $pushToken = null;
    private ?string $name = null;

    public function __construct(
        ?Email $email = null,
        ?PhoneNumber $phoneNumber = null,
        ?string $pushToken = null,
        ?string $name = null
    ) {
        if ($email === null && $phoneNumber === null && $pushToken === null) {
            throw new \InvalidArgumentException('At least one contact method must be provided');
        }

        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->pushToken = $pushToken;
        $this->name = $name;
    }

    public static function withEmail(Email $email, ?string $name = null): self
    {
        return new self($email, null, null, null, $name);
    }

    public static function withPhoneNumber(PhoneNumber $phoneNumber, ?string $name = null): self
    {
        return new self(null, $phoneNumber, null, null, $name);
    }

    public static function withPushToken(string $pushToken, ?string $name = null): self
    {
        return new self(null, null, $pushToken, null, $name);
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getPushToken(): ?string
    {
        return $this->pushToken;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function hasEmail(): bool
    {
        return $this->email !== null;
    }

    public function hasPhoneNumber(): bool
    {
        return $this->phoneNumber !== null;
    }

    public function hasPushToken(): bool
    {
        return $this->pushToken !== null;
    }
}
