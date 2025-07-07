<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Service;

use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;

interface NotificationProviderInterface
{
    public function getName(): string;

    public function getChannel(): NotificationChannel;

    public function send(Recipient $recipient, Message $message): NotificationResult;

    public function isEnabled(): bool;

    public function isHealthy(): bool;
}
