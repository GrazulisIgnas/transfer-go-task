<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

class PhoneNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = $this->normalize($value);
        
        if (!$this->isValid($normalized)) {
            throw new \InvalidArgumentException(sprintf('Invalid phone number: %s', $value));
        }

        $this->value = $normalized;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function normalize(string $phoneNumber): string
    {
        // Remove all non-digit characters except +
        $normalized = preg_replace('/[^+0-9]/', '', $phoneNumber);
        
        // If it doesn't start with +, assume it's a local number and add + prefix
        if (!str_starts_with($normalized, '+')) {
            $normalized = '+' . $normalized;
        }

        return $normalized;
    }

    private function isValid(string $phoneNumber): bool
    {
        // Basic validation: starts with + and has 7-15 digits
        return preg_match('/^\+[1-9]\d{6,14}$/', $phoneNumber) === 1;
    }
}
