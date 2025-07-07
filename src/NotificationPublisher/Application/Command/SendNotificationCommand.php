<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

class SendNotificationCommand
{
    private string $userId;
    private string $recipientEmail;
    private ?string $recipientPhone;
    private ?string $recipientPushToken;
    private ?string $recipientName;
    private string $subject;
    private string $body;
    private array $channels;
    private array $templateVariables;
    private ?string $templateId;
    private array $metadata;
    private ?\DateTimeImmutable $scheduledAt;

    public function __construct(
        string $userId,
        string $recipientEmail,
        string $subject,
        string $body,
        array $channels = ['email'],
        ?string $recipientPhone = null,
        ?string $recipientPushToken = null,
        ?string $recipientName = null,
        array $templateVariables = [],
        ?string $templateId = null,
        array $metadata = [],
        ?\DateTimeImmutable $scheduledAt = null
    ) {
        $this->userId = $userId;
        $this->recipientEmail = $recipientEmail;
        $this->recipientPhone = $recipientPhone;
        $this->recipientPushToken = $recipientPushToken;
        $this->recipientName = $recipientName;
        $this->subject = $subject;
        $this->body = $body;
        $this->channels = $channels;
        $this->templateVariables = $templateVariables;
        $this->templateId = $templateId;
        $this->metadata = $metadata;
        $this->scheduledAt = $scheduledAt;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    public function getRecipientPhone(): ?string
    {
        return $this->recipientPhone;
    }

    public function getRecipientPushToken(): ?string
    {
        return $this->recipientPushToken;
    }

    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getChannels(): array
    {
        return $this->channels;
    }

    public function getTemplateVariables(): array
    {
        return $this->templateVariables;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }
}
