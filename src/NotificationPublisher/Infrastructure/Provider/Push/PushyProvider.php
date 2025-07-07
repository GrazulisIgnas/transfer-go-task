<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Provider\Push;

use App\NotificationPublisher\Domain\Service\NotificationProviderInterface;
use App\NotificationPublisher\Domain\Service\NotificationResult;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use Psr\Log\LoggerInterface;

class PushyProvider implements NotificationProviderInterface
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
        return 'pushy';
    }

    public function getChannel(): NotificationChannel
    {
        return NotificationChannel::push();
    }

    public function send(Recipient $recipient, Message $message): NotificationResult
    {
        try {
            if (!$recipient->hasPushToken()) {
                return NotificationResult::failure('Recipient has no push token');
            }

            $this->logger->info('Sending push notification via Pushy', [
                'token' => substr($recipient->getPushToken(), 0, 10) . '...',
                'subject' => $message->getSubject(),
            ]);

            // TODO: Implement actual Pushy integration
            if ($this->simulateApiCall()) {
                return NotificationResult::success('Push notification sent successfully via Pushy');
            } else {
                return NotificationResult::failure('Pushy API error', 502);
            }

        } catch (\Throwable $exception) {
            $this->logger->error('Pushy provider error', [
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
        // Simulate 88% success rate for demo purposes
        return random_int(1, 100) <= 88;
    }
}
