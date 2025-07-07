<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Provider\Sms;

use App\NotificationPublisher\Domain\Service\NotificationProviderInterface;
use App\NotificationPublisher\Domain\Service\NotificationResult;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use Psr\Log\LoggerInterface;

class TwilioProvider implements NotificationProviderInterface
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
        return 'twilio';
    }

    public function getChannel(): NotificationChannel
    {
        return NotificationChannel::sms();
    }

    public function send(Recipient $recipient, Message $message): NotificationResult
    {
        try {
            if (!$recipient->hasPhoneNumber()) {
                return NotificationResult::failure('Recipient has no phone number');
            }

            $this->logger->info('Sending SMS via Twilio', [
                'to' => $recipient->getPhoneNumber()->getValue(),
                'message' => substr($message->getBody(), 0, 50) . '...',
            ]);

            // TODO: Implement actual Twilio integration
            if ($this->simulateApiCall()) {
                return NotificationResult::success('SMS sent successfully via Twilio');
            } else {
                return NotificationResult::failure('Twilio API error', 400);
            }

        } catch (\Throwable $exception) {
            $this->logger->error('Twilio provider error', [
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
        return $this->enabled;
    }

    private function simulateApiCall(): bool
    {
        // Simulate 92% success rate for demo purposes
        return random_int(1, 100) <= 92;
    }
}
