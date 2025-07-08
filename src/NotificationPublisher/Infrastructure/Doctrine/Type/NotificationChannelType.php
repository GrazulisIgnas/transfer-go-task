<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Doctrine\Type;

use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class NotificationChannelType extends Type
{
    public const NAME = 'notification_channel';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL(['length' => 16]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : NotificationChannel::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof NotificationChannel ? $value->getValue() : $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): true
    {
        return true;
    }
}
