<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Service;

use App\NotificationPublisher\Domain\Service\NotificationProviderInterface;
use App\NotificationPublisher\Domain\Service\NotificationResult;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use App\NotificationPublisher\Infrastructure\Configuration\ProviderConfiguration;
use Psr\Log\LoggerInterface;

class FailoverManager
{
    private ProviderConfiguration $providerConfiguration;
    private LoggerInterface $logger;

    /** @var NotificationProviderInterface[] */
    private array $providers = [];

    public function __construct(
        ProviderConfiguration $providerConfiguration,
        LoggerInterface $logger
    ) {
        $this->providerConfiguration = $providerConfiguration;
        $this->logger = $logger;
    }

    public function addProvider(NotificationProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    public function sendWithFailover(
        NotificationChannel $channel,
        Recipient $recipient,
        Message $message
    ): array {
        $providers = $this->getAvailableProvidersForChannel($channel);

        if (empty($providers)) {
            throw new \RuntimeException(
                sprintf('No available providers for channel: %s', $channel->getValue())
            );
        }

        $lastException = null;

        foreach ($providers as $provider) {
            try {
                $this->logger->info('Attempting to send notification', [
                    'provider' => $provider->getName(),
                    'channel' => $channel->getValue(),
                ]);

                $result = $provider->send($recipient, $message);

                if ($result->isSuccessful()) {
                    return [
                        'provider' => $provider->getName(),
                        'result' => $result,
                    ];
                }

                $this->logger->warning('Provider failed to send notification', [
                    'provider' => $provider->getName(),
                    'error' => $result->getErrorMessage(),
                ]);

                // Continue to the next provider on failure
            } catch (\Throwable $exception) {
                $lastException = $exception;
                $this->logger->error('Provider threw exception', [
                    'provider' => $provider->getName(),
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        // All providers failed
        $errorMessage = $lastException
            ? $lastException->getMessage()
            : 'All providers failed to send notification';

        return [
            'provider' => $providers[0]->getName(), // Return the first provider name for logging
            'result' => NotificationResult::failure($errorMessage),
        ];
    }

    /**
     * @return NotificationProviderInterface[]
     */
    private function getAvailableProvidersForChannel(NotificationChannel $channel): array
    {
        $channelProviders = array_filter($this->providers, function (NotificationProviderInterface $provider) use ($channel) {
            return $provider->getChannel()->equals($channel) &&
                   $provider->isEnabled() &&
                   $provider->isHealthy();
        });

        // Sort by priority from configuration
        usort($channelProviders, function (NotificationProviderInterface $a, NotificationProviderInterface $b) use ($channel) {
            $priorityA = $this->providerConfiguration->getProviderPriority($channel->getValue(), $a->getName());
            $priorityB = $this->providerConfiguration->getProviderPriority($channel->getValue(), $b->getName());

            return $priorityA <=> $priorityB;
        });

        return $channelProviders;
    }
}
