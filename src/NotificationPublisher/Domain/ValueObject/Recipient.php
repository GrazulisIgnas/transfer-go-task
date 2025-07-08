<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\PhoneNumber;
use App\Shared\Domain\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Recipient
{
    #[ORM\Column(type: 'email', nullable: true)]
    private ?Email $email = null;

    #[ORM\Column(type: 'phone_number', nullable: true)]
    private ?PhoneNumber $phoneNumber = null;

    #[ORM\Column(type: 'user_id', nullable: true)]
    private ?UserId $userId = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $pushToken = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $name = null;

    public function __construct(
        ?Email $email = null,
        ?PhoneNumber $phoneNumber = null,
        ?UserId $userId = null,
        ?string $pushToken = null,
        ?string $name = null
    ) {
        if ($email === null && $phoneNumber === null && $pushToken === null && $userId === null) {
            throw new \InvalidArgumentException('At least one contact method must be provided');
        }

        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->userId = $userId;
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

    public static function withUserId(UserId $userId, ?string $name = null): self
    {
        return new self(null, null, $userId, null, $name);
    }

    public static function withPushToken(string $pushToken, ?string $name = null): self
    {
        return new self(null, null, null, $pushToken, $name);
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getUserId(): ?UserId
    {
        return $this->userId;
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

    public function hasUserId(): bool
    {
        return $this->userId !== null;
    }

    public function hasPushToken(): bool
    {
        return $this->pushToken !== null;
    }
}
