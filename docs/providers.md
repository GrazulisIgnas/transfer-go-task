# Notification Providers Documentation

## AWS SES Provider

### Initial Setup

1. AWS Account Setup
    - Create an AWS account if you don't have one
    - Navigate to AWS SES (Simple Email Service)
    - Verify your email domain or at least one email address
    - Create IAM user with SES permissions
    - Get your credentials:
        - Access Key ID
        - Secret Access Key

2. Configure Environment Variables
   Add these to your `.env` file:
   ```
   AWS_ACCESS_KEY_ID=your_access_key_id
   AWS_SECRET_ACCESS_KEY=your_secret_access_key
   AWS_DEFAULT_REGION=eu-north-1
   AWS_SENDER_EMAIL=your_verified_email@example.com
   ```

### Testing Email Sending

1. Basic Requirements
    - Verified sender email in AWS SES
    - Proper IAM permissions configured
    - Environment variables set in `.env`

2. Run the Test
   ```bash
   docker compose exec php vendor/bin/phpunit tests/Unit/NotificationPublisher/Infrastructure/Provider/Email/AwsSesProviderTest.php
   ```

### Debugging Tests

1. IDE Setup (same as Pushy setup)
    - Configure PHP server in your IDE
    - Server name: `be-evaluation-task`
    - Set up path mappings

2. Run Test with Debug
   ```bash
   docker compose exec -e XDEBUG_TRIGGER=1 -e PHP_IDE_CONFIG="serverName=be-evaluation-task" php ./vendor/bin/phpunit tests/Unit/NotificationPublisher/Infrastructure/Provider/Email/AwsSesProviderTest.php
   ```

### Troubleshooting

1. Email Verification
    - Ensure sender email is verified in AWS SES
    - In sandbox mode, recipient emails also need verification
    - Check AWS SES console for verification status

2. IAM Permissions
    - Verify IAM user has `ses:SendEmail` and `ses:SendRawEmail` permissions
    - Check AWS credentials are correct in `.env`

3. Environment Variables
    - After updating `.env`, restart Docker:
      ```bash
      docker compose down
      docker compose up -d
      ```

4. Common Errors
    - "Email address not verified" - Verify email in AWS SES
    - "Invalid credentials" - Check AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY
    - "Region error" - Verify AWS_DEFAULT_REGION matches your SES region


## PUSHY Provider

### Initial Setup

1. Create Pushy Account
   - Register at https://pushy.me
   - Create a new project
   - You will receive two important credentials:
     - API Key
     - APP ID

2. Configure Environment Variables
   Add these to your `.env` file:
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
   Add it to your `.env` file:
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
   - After changing `.env`, restart Docker containers:
     ```bash
     docker compose down
     docker compose up -d
     ```
