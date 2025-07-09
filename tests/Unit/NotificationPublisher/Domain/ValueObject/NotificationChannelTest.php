<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\ValueObject;

use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use PHPUnit\Framework\TestCase;

class NotificationChannelTest extends TestCase
{
    public function testCanCreateEmailChannel(): void
    {
        $channel = NotificationChannel::email();
        
        $this->assertEquals('email', $channel->getValue());
        $this->assertEquals('email', (string) $channel);
    }

    public function testCanCreateSmsChannel(): void
    {
        $channel = NotificationChannel::sms();
        
        $this->assertEquals('sms', $channel->getValue());
    }

    public function testCanCreateFromString(): void
    {
        $channel = NotificationChannel::fromString('push');
        
        $this->assertEquals('push', $channel->getValue());
    }

    public function testThrowsExceptionForInvalidChannel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid notification channel: invalid');
        
        NotificationChannel::fromString('invalid');
    }

    public function testCanCompareChannels(): void
    {
        $channel1 = NotificationChannel::email();
        $channel2 = NotificationChannel::email();
        $channel3 = NotificationChannel::sms();
        
        $this->assertTrue($channel1->equals($channel2));
        $this->assertFalse($channel1->equals($channel3));
    }
}
