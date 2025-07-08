<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Provider\Email;

use App\NotificationPublisher\Domain\Service\NotificationProviderInterface;
use App\NotificationPublisher\Domain\Service\NotificationResult;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Throwable;

class AwsSesProvider implements NotificationProviderInterface
{
    private array $config;
    private LoggerInterface $logger;
    private bool $enabled;

    private SesClient $ses;

    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->enabled = $config['enabled'] ?? true;

        try {
            $this->ses = new SesClient([
                'version' => $config['version'] ?? 'latest',
                'region' => $config['region'],
                'credentials' => [
                    'key' => $config['access_key_id'],
                    'secret' => $config['secret_access_key'],
                ],
            ]);
        } catch (Exception $exception) {
            $this->logger->error('AWS SES init error', [
                'message' => $exception->getMessage()
            ]);

            return NotificationResult::failure($exception->getMessage(), $exception->getCode());
        }
    }

    public function getName(): string
    {
        return 'aws_ses';
    }

    public function getChannel(): NotificationChannel
    {
        return NotificationChannel::email();
    }

    public function send(Recipient $recipient, Message $message): NotificationResult
    {
        try {
            if (!$recipient->hasEmail()) {
                return NotificationResult::failure('Recipient has no email address');
            }

            $this->logger->info('Sending email via AWS SES', [
                'to' => $recipient->getEmail()->getValue(),
                'subject' => $message->getSubject(),
            ]);

            $result = $this->ses->sendEmail([
                'Destination' => [
                    'ToAddresses' => [$recipient->getEmail()->getValue()],
                ],
                'Message' => [
                    'Body' => [
                        'Text' => ['Data' => $message->getBody()],
                    ],
                    'Subject' => ['Data' => $message->getSubject()],
                ],
                'Source' => $this->config['from'],
            ]);

            $metaData = $result->get('@metadata');
            if (isset($metaData['statusCode']) && $metaData['statusCode'] === Response::HTTP_OK) {
                return NotificationResult::success('Email sent successfully via AWS SES');
            } else {
                return NotificationResult::failure('AWS SES API error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (AwsException $exception) {
            $this->logger->error('AWS SES error', [
                'message' => $exception->getMessage()
            ]);

            return NotificationResult::failure($exception->getAwsErrorMessage(), $exception->getStatusCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $exception) {
            $this->logger->error('AWS SES provider error', [
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
        // TODO: Implement health check (e.g., ping AWS SES)
        return $this->enabled;
    }
}
