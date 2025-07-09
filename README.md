# Notification Service Documentation

## Overview

The Notification Service is a robust, multichannel notification system built with Symfony 5.4. It provides:

- **Multi-channel support**: Email, SMS, Push notifications
- **Provider abstraction**: Multiple providers per channel with automatic failover
- **Configuration-driven**: Easy to enable/disable channels and providers
- **Retry mechanism**: Automatic retry for failed notifications

## Quick Start

### 1. Environment Setup

Copy the environment file and configure your providers:

```bash
cp .env.example .env.local
```

Set your provider credentials and other configurations. Read [Providers Documentation](./docs/providers.md) for detailed setup instructions for each provider.

### 2. Install Dependencies

```bash 
docker compose exec php composer install
```

### 3. Docker Setup

```bash
docker compose build --pull --no-cache
docker compose up -d
```

### 4. Database Setup

```bash
docker compose exec php bin/console doctrine:migrations:migrate
```

### 5. Configure Providers

Open the [config/packages/parameters.yaml](./config/packages/parameters.yaml) file and configure your providers.

## Testing

### Run Unit Tests

```bash
docker compose exec php vendor/bin/phpunit tests/Unit
```

### Run Functional Tests

```bash
docker compose exec php vendor/bin/phpunit tests/Functional
```

### Manual Testing

```bash
curl -X POST https://localhost/api/notifications/send \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": "test-user",
    "recipient": {
      "email": "test@example.com",
      "push_token": "test"
    },
    "message": {
      "subject": "Test",
      "body": "This is a test message"
    },
    "channels": ["email", "push"]
  }'
```

## Credits

© 2025 [Ignas Gražulis](https://github.com/GrazulisIgnas). All rights reserved.
