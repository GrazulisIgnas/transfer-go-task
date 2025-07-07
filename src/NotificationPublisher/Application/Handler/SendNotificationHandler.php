<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Handler;

use App\NotificationPublisher\Application\Command\SendNotificationCommand;
use App\NotificationPublisher\Application\Service\NotificationService;
use App\NotificationPublisher\Domain\Entity\Notification;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use App\NotificationPublisher\Domain\ValueObject\Message;
use App\NotificationPublisher\Domain\ValueObject\NotificationChannel;
use App\NotificationPublisher\Domain\ValueObject\Recipient;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\PhoneNumber;
use Ramsey\Uuid\Uuid;

class SendNotificationHandler
{
    private NotificationService $notificationService;
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        NotificationService $notificationService,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->notificationService = $notificationService;
        $this->notificationRepository = $notificationRepository;
    }

    public function handle(SendNotificationCommand $command): array
    {
        $notifications = [];

        foreach ($command->getChannels() as $channelString) {
            $channel = NotificationChannel::fromString($channelString);
            $recipient = $this->createRecipient($command, $channel);
            
            if ($recipient === null) {
                continue; // Skip if no appropriate contact method for this channel
            }

            $message = new Message(
                $command->getSubject(),
                $command->getBody(),
                $command->getTemplateVariables(),
                $command->getTemplateId()
            );

            $notification = new Notification(
                Uuid::uuid4()->toString(),
                $command->getUserId(),
                $recipient,
                $message,
                $channel,
                3, // max retries
                $command->getMetadata(),
                $command->getScheduledAt()
            );

            $this->notificationRepository->save($notification);

            // If not scheduled, send immediately
            if ($notification->isReadyToSend()) {
                $this->notificationService->sendNotification($notification);
            }

            $notifications[] = $notification;
        }

        return $notifications;
    }

    private function createRecipient(SendNotificationCommand $command, NotificationChannel $channel): ?Recipient
    {
        switch ($channel->getValue()) {
            case NotificationChannel::EMAIL:
                if ($command->getRecipientEmail()) {
                    return Recipient::withEmail(
                        new Email($command->getRecipientEmail()),
                        $command->getRecipientName()
                    );
                }
                break;

            case NotificationChannel::SMS:
                if ($command->getRecipientPhone()) {
                    return Recipient::withPhoneNumber(
                        new PhoneNumber($command->getRecipientPhone()),
                        $command->getRecipientName()
                    );
                }
                break;

            case NotificationChannel::PUSH:
                if ($command->getRecipientPushToken()) {
                    return Recipient::withPushToken(
                        $command->getRecipientPushToken(),
                        $command->getRecipientName()
                    );
                }
                break;
        }

        return null;
    }
}
