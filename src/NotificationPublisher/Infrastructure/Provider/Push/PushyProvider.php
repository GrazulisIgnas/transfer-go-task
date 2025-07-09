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
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
                'response' => $response,
            ]);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                return NotificationResult::success('Push notification sent via Pushy');
            } else {
                return NotificationResult::failure('Failed to push notification via Pushy', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (RequestException $exception) {
            $this->logger->error('Pushy error', [
                'error' => $exception->getMessage(),
                'response' => $exception->getResponse()?->getBody()?->getContents(),
            ]);

            return NotificationResult::failure('Pushy request failed: ' . $exception->getMessage(), 502);
        } catch (Throwable $exception) {
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
