<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Infrastructure\Provider\Push;

use App\NotificationPublisher\Domain\ValueObject\Recipient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use App\NotificationPublisher\Infrastructure\Provider\Push\PushyProvider;
use App\NotificationPublisher\Domain\ValueObject\Message;

class PushyProviderTest extends TestCase
{
    /**
     * Test that the PushyProvider sends a notification successfully.
     * @see /docs/providers.md
     */
    public function testPushyProviderSendsNotificationSuccessfully()
    {
        $pushyDeviceToken = $_ENV['PUSHY_DEVICE_TOKEN'] ?? getenv('PUSHY_DEVICE_TOKEN') ?? '';
        $this->assertNotEmpty($pushyDeviceToken, 'PUSHY_DEVICE_TOKEN environment variable is not set');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $config = [
            'api_key' => $_ENV['PUSHY_API_KEY'] ?? getenv('PUSHY_API_KEY') ?? 'test_pushy_api_key',
            'enabled' => true,
            'priority' => 1,
        ];

        $provider = new PushyProvider($config, $logger);

        // Create a Message mock with the required methods
        $message = $this->createMock(Message::class);
        $message->method('getBody')->willReturn('Test body');
        $message->method('getSubject')->willReturn('Test subject');

        $recipient = $this->createMock(Recipient::class);
        $recipient->method('hasPushToken')->willReturn(true);
        $recipient->method('getPushToken')->willReturn($pushyDeviceToken);

        $result = $provider->send($recipient, $message);
        $this->assertTrue($result->isSuccessful());
    }
}
