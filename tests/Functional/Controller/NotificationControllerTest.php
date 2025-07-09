<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\NotificationPublisher\Application\Command\SendNotificationCommand;
use App\NotificationPublisher\Domain\Service\NotificationResult;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use App\NotificationPublisher\Infrastructure\Provider\Email\AwsSesProvider;
use App\Shared\Domain\ValueObject\Email;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    public function testSendNotificationWithValidData(): void
    {
        $data = [
            'user_id' => 'user123',
            'recipient' => [
                'email' => $_ENV['AWS_SENDER_EMAIL'] ?? getenv('AWS_SENDER_EMAIL') ?? 'test@example.com',
                'name' => 'Test User',
            ],
            'message' => [
                'subject' => 'Test Notification',
                'body' => 'This is a test notification message.',
            ],
            'channels' => ['email'],
        ];

        $result = $this->sendEmail($data);

        $this->assertTrue($result->isSuccessful());
    }

    public function testSendNotificationWithInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->sendEmail([]);
    }

    private function sendEmail(array $data): NotificationResult
    {
        $command = new SendNotificationCommand(
            $data['user_id'] ?? '',
            $data['recipient']['email'] ?? '',
            $data['message']['subject'] ?? '',
            $data['message']['body'] ?? '',
            $data['channels'] ?? ['email'],
                $data['recipient']['phone'] ?? '',
            $data['recipient']['push_token'] ?? '',
            $data['recipient']['name'] ?? ''
        );

        $recipient = Recipient::withEmail(
            new Email($command->getRecipientEmail()),
            $command->getRecipientName()
        );

        $message = new Message(
            $command->getSubject(),
            $command->getBody(),
            $command->getTemplateVariables(),
            $command->getTemplateId()
        );

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

        return (new AwsSesProvider($config, $logger))->send($recipient, $message);
    }
}
