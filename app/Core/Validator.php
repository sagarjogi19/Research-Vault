<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{
    private array $errors = [];

    public function __construct(
        private readonly array $data
    ) {
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function required(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = $this->data[$field] ?? null;

        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->errors[$field][] = "{$label} is required.";
        }

        return $this;
    }

    public function email(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$label} must be a valid email address.";
        }

        return $this;
    }

    public function min(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && mb_strlen($value) < $min) {
            $this->errors[$field][] = "{$label} must be at least {$min} characters.";
        }

        return $this;
    }

    public function max(string $field, int $max, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && mb_strlen($value) > $max) {
            $this->errors[$field][] = "{$label} must not exceed {$max} characters.";
        }

        return $this;
    }

    public function confirmed(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = $this->data[$field] ?? null;
        $confirm = $this->data[$field . '_confirmation'] ?? null;

        if ($value !== $confirm) {
            $this->errors[$field][] = "{$label} confirmation does not match.";
        }

        return $this;
    }

    public function numeric(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = $this->data[$field] ?? null;

        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->errors[$field][] = "{$label} must be a number.";
        }

        return $this;
    }

    public function in(string $field, array $allowed, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $value = $this->data[$field] ?? null;

        if ($value !== null && $value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field][] = "{$label} is invalid.";
        }

        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstErrors(): array
    {
        $first = [];
        foreach ($this->errors as $field => $messages) {
            $first[$field] = $messages[0];
        }

        return $first;
    }
}
