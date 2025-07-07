<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Configuration;

class ProviderConfiguration
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getProviderPriority(string $channel, string $providerName): int
    {
        return $this->config['channels'][$channel]['providers'][$providerName]['priority'] ?? 999;
    }

    public function isProviderEnabled(string $channel, string $providerName): bool
    {
        return $this->config['channels'][$channel]['providers'][$providerName]['enabled'] ?? false;
    }

    public function getProviderConfig(string $channel, string $providerName): array
    {
        return $this->config['channels'][$channel]['providers'][$providerName] ?? [];
    }

    public function isChannelEnabled(string $channel): bool
    {
        return $this->config['channels'][$channel]['enabled'] ?? false;
    }

    public function getThrottleLimits(): array
    {
        return $this->config['throttling'] ?? [];
    }
}
