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
cp .env .env.local
```

### 2. Docker Setup

```bash
docker compose build --pull --no-cache
docker compose up -d
```

### 3. Database Setup

```bash
docker compose exec php bin/console doctrine:migrations:migrate
```

## Credits

© 2025 [Ignas Gražulis](https://github.com/GrazulisIgnas). All rights reserved.
