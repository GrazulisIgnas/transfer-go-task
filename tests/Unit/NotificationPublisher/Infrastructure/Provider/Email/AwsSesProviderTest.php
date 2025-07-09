<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Infrastructure\Provider\Email;

use App\Shared\Domain\ValueObject\Email;
use Aws\Result;
use Aws\Ses\SesClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use App\NotificationPublisher\Infrastructure\Provider\Email\AwsSesProvider;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use ReflectionClass;

class AwsSesProviderTest extends TestCase
{
    /**
     * Test that the AwsSesProvider sends a notification successfully.
     * @see /docs/providers.md
     */
    public function testAwsSesProviderSendsNotificationSuccessfully()
    {
        $testEmail = $_ENV['AWS_SENDER_EMAIL'] ?? getenv('AWS_SENDER_EMAIL') ?? '';
        $this->assertNotEmpty($testEmail, 'AWS_SENDER_EMAIL environment variable is not set');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $config = [
            'region' => $_ENV['AWS_DEFAULT_REGION'] ?? getenv('AWS_DEFAULT_REGION') ?? 'eu-north-1',
            'access_key_id' => $_ENV['AWS_ACCESS_KEY_ID'] ?? getenv('AWS_ACCESS_KEY_ID') ?? 'test_access_key',
            'secret_access_key' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? getenv('AWS_SECRET_ACCESS_KEY') ?? 'test_secret_key',
            'from' => $testEmail,
            'enabled' => true,
            'priority' => 1,
        ];

        $provider = new AwsSesProvider($config, $logger);

        // Create test message and recipient
        $message = $this->createMock(Message::class);
        $message->method('getBody')->willReturn('Test email body');
        $message->method('getSubject')->willReturn('Test email subject');

        $email = new Email($testEmail);

        $recipient = $this->createMock(Recipient::class);
        $recipient->method('hasEmail')->willReturn(true);
        $recipient->method('getEmail')->willReturn($email);
        $recipient->method('getName')->willReturn('Test User');

        $result = $provider->send($recipient, $message);
        $this->assertTrue($result->isSuccessful());
    }
}
