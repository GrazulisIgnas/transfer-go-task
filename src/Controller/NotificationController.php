<?php

declare(strict_types=1);

namespace App\Controller;

use App\NotificationPublisher\Application\Command\SendNotificationCommand;
use App\NotificationPublisher\Application\Handler\SendNotificationHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/notifications', name: 'notifications_')]
class NotificationController extends AbstractController
{
    private SendNotificationHandler $sendNotificationHandler;
    private LoggerInterface $logger;

    public function __construct(
        SendNotificationHandler $sendNotificationHandler,
        LoggerInterface $logger
    ) {
        $this->sendNotificationHandler = $sendNotificationHandler;
        $this->logger = $logger;
    }

    #[Route('/send', name: 'send', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            $command = new SendNotificationCommand(
                $data['user_id'] ?? '',
                $data['recipient']['email'] ?? '',
                $data['message']['subject'] ?? '',
                $data['message']['body'] ?? '',
                $data['channels'] ?? ['email'],
                $data['recipient']['phone'] ?? null,
                $data['recipient']['push_token'] ?? null,
                $data['recipient']['name'] ?? null,
                $data['message']['template_variables'] ?? [],
                $data['message']['template_id'] ?? null,
                $data['metadata'] ?? [],
                isset($data['scheduled_at']) ? new \DateTimeImmutable($data['scheduled_at']) : null
            );

            $notifications = $this->sendNotificationHandler->handle($command);

            return new JsonResponse([
                'success' => true,
                'message' => 'Notifications queued successfully',
                'notifications' => array_map(fn($n) => [
                    'id' => $n->getId(),
                    'channel' => $n->getChannel()->getValue(),
                    'status' => $n->getStatus()->getValue(),
                ], $notifications),
            ]);

        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse([
                'error' => 'Validation error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Throwable $exception) {
            $this->logger->error('Failed to send notification', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => 'Failed to process notification request',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
