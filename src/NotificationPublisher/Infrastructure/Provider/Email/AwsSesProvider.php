<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Provider\Email;

use App\NotificationPublisher\Domain\Service\NotificationProviderInterface;
use App\NotificationPublisher\Domain\Service\NotificationResult;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use Psr\Log\LoggerInterface;

class AwsSesProvider implements NotificationProviderInterface
{
    private array $config;
    private LoggerInterface $logger;
    private bool $enabled;

    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->enabled = $config['enabled'] ?? true;
    }

    public function getName(): string
    {
        return 'aws_ses';
    }

    public function getChannel(): NotificationChannel
    {
        return NotificationChannel::email();
    }

    public function send(Recipient $recipient, Message $message): NotificationResult
    {
        try {
            if (!$recipient->hasEmail()) {
                return NotificationResult::failure('Recipient has no email address');
            }

            // Simulate AWS SES API call
            $this->logger->info('Sending email via AWS SES', [
                'to' => $recipient->getEmail()->getValue(),
                'subject' => $message->getSubject(),
            ]);

            // TODO: Implement actual AWS SES integration
            // For now, we'll simulate success/failure
            if ($this->simulateApiCall()) {
                return NotificationResult::success('Email sent successfully via AWS SES');
            } else {
                return NotificationResult::failure('AWS SES API error', 500);
            }

        } catch (\Throwable $exception) {
            $this->logger->error('AWS SES provider error', [
                'exception' => $exception->getMessage(),
            ]);

            return NotificationResult::failure($exception->getMessage());
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isHealthy(): bool
    {
        // TODO: Implement health check (e.g., ping AWS SES)
        return $this->enabled;
    }

    private function simulateApiCall(): bool
    {
        // Simulate 95% success rate for demo purposes
        return random_int(1, 100) <= 95;
    }
}
