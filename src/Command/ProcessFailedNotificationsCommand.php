<?php

namespace App\Command;

use App\NotificationPublisher\Application\Command\RetryFailedNotificationCommand;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use App\NotificationPublisher\Application\Handler\RetryFailedNotificationHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessFailedNotificationsCommand extends Command
{
    protected static $defaultName = 'app:process-failed-notifications';
    protected static $defaultDescription = 'Process all failed notifications that can be retried';

    private NotificationRepositoryInterface $notificationRepository;
    private RetryFailedNotificationHandler $retryHandler;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        RetryFailedNotificationHandler $retryHandler
    ) {
        parent::__construct();
        $this->notificationRepository = $notificationRepository;
        $this->retryHandler = $retryHandler;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Processing failed notifications');

        try {
            $failedNotifications = $this->notificationRepository->findFailedNotificationsForRetry();

            if (empty($failedNotifications)) {
                $io->success('No failed notifications to process');
                return Command::SUCCESS;
            }

            $processedCount = 0;
            $failedCount = 0;

            foreach ($failedNotifications as $notification) {
                try {
                    if ($notification->canRetry()) {
                        $command = new RetryFailedNotificationCommand($notification->getId());
                        $this->retryHandler->handle($command);
                        $processedCount++;
                        $io->writeln(sprintf('Successfully processed notification ID: %s', $notification->getId()));
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $io->error(sprintf(
                        'Failed to process notification ID: %s. Error: %s',
                        $notification->getId(),
                        $e->getMessage()
                    ));
                }
            }

            $io->success(sprintf(
                'Finished processing notifications. Successful: %d, Failed: %d',
                $processedCount,
                $failedCount
            ));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
