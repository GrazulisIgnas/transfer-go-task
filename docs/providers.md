# Notification Providers Documentation

## PUSHY Provider

### Initial Setup

1. Create Pushy Account
   - Register at https://pushy.me
   - Create a new project
   - You will receive two important credentials:
     - API Key
     - APP ID

2. Configure Environment Variables
   Add these to your `.env.local` file:
   ```
   PUSHY_API_KEY=your_api_key
   PUSHY_APP_ID=your_app_id
   ```

### Getting Device Token

1. Access Test Page
   - Open http://localhost/pushy-test in your browser
   - Allow notifications when prompted

2. Get Device Token
   - Wait for the token to appear on the page
   - If you don't see the token, check the browser's DevTools console for errors
   - Note: SSL-related errors may appear if Docker is running with HTTPS enabled

3. Save Device Token
   Add it to your `.env.local` file:
   ```
   PUSHY_DEVICE_TOKEN=your_device_token
   ```

### Running Tests

#### Basic Test
Run the Pushy provider test:
```bash
docker compose exec php vendor/bin/phpunit tests/Unit/NotificationPublisher/Infrastructure/Provider/Push/PushyProviderTest.php
```

#### Debugging Tests

1. IDE Setup
   - Add a PHP server in your IDE
   - Server name must be: `be-evaluation-task`
   - Set up path mappings if needed

2. Run Test with Debug
   ```bash
   docker compose exec -e XDEBUG_TRIGGER=1 -e PHP_IDE_CONFIG="serverName=be-evaluation-task" php ./vendor/bin/phpunit tests/Unit/NotificationPublisher/Infrastructure/Provider/Push/PushyProviderTest.php
   ```

### Troubleshooting

1. Environment Variables
   - After changing `.env.local`, restart Docker containers:
     ```bash
     docker compose down
     docker compose up -d
     ```
