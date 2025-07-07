<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

class Message
{
    private string $subject;
    private string $body;
    private array $templateVariables = [];
    private ?string $templateId = null;

    public function __construct(
        string $subject,
        string $body,
        array $templateVariables = [],
        ?string $templateId = null
    ) {
        if (empty(trim($subject))) {
            throw new \InvalidArgumentException('Message subject cannot be empty');
        }

        if (empty(trim($body))) {
            throw new \InvalidArgumentException('Message body cannot be empty');
        }

        $this->subject = $subject;
        $this->body = $body;
        $this->templateVariables = $templateVariables;
        $this->templateId = $templateId;
    }

    public static function create(string $subject, string $body): self
    {
        return new self($subject, $body);
    }

    public static function fromTemplate(string $templateId, array $variables = []): self
    {
        return new self('', '', $variables, $templateId);
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getTemplateVariables(): array
    {
        return $this->templateVariables;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function isTemplate(): bool
    {
        return $this->templateId !== null;
    }

    public function renderWithVariables(): self
    {
        $renderedSubject = $this->renderString($this->subject, $this->templateVariables);
        $renderedBody = $this->renderString($this->body, $this->templateVariables);

        return new self($renderedSubject, $renderedBody, $this->templateVariables, $this->templateId);
    }

    private function renderString(string $template, array $variables): string
    {
        $rendered = $template;
        foreach ($variables as $key => $value) {
            $rendered = str_replace("{{$key}}", (string) $value, $rendered);
        }

        return $rendered;
    }
}
