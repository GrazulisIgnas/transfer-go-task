<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Provider\Push;

use App\NotificationPublisher\Domain\Service\NotificationProviderInterface;
use App\NotificationPublisher\Domain\Service\NotificationResult;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
        if (!$recipient->hasPushToken()) {
            return NotificationResult::failure('Recipient has no push token');
        }

        $payload = [
            'to' => $recipient->getPushToken(),
            'data' => [
                'title' => $message->getSubject(),
            ],
            'notification' => [
                'title' => $message->getSubject(),
                'body' => $message->getBody(),
            ],
        ];

        try {
            $client = new Client([
                'base_uri' => 'https://api.pushy.me',
                'timeout' => 5.0,
            ]);

            $response = $client->post('/push?api_key=' . $this->config['api_key'], [
                'json' => $payload,
            ]);

            $this->logger->info('Push sent via Pushy', [
                'status' => $response->getStatusCode(),
            ]);

            return NotificationResult::success('Push sent via Pushy');
        } catch (RequestException $e) {
            $this->logger->error('Pushy error', [
                'error' => $e->getMessage(),
                'response' => $e->getResponse()?->getBody()?->getContents(),
            ]);

            return NotificationResult::failure('Pushy request failed: ' . $e->getMessage(), 502);
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
