<?php

declare(strict_types=1);

namespace App\Validators;

use App\Exceptions\ValidationException;

class BaseValidator
{
    private array $errors = [];

    /**
     * Validate that a field is required (non-empty).
     */
    public function required(string $field, mixed $value, string $label = ''): static
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$field][] = ($label ?: $field) . ' es obligatorio.';
        }

        return $this;
    }

    /**
     * Validate string minimum length.
     */
    public function minLength(string $field, string $value, int $min, string $label = ''): static
    {
        if (mb_strlen($value) < $min) {
            $this->errors[$field][] = ($label ?: $field) . " debe tener al menos {$min} caracteres.";
        }

        return $this;
    }

    /**
     * Validate string maximum length.
     */
    public function maxLength(string $field, string $value, int $max, string $label = ''): static
    {
        if (mb_strlen($value) > $max) {
            $this->errors[$field][] = ($label ?: $field) . " no debe exceder {$max} caracteres.";
        }

        return $this;
    }

    /**
     * Validate email format.
     */
    public function email(string $field, string $value): static
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'El formato del email no es válido.';
        }

        return $this;
    }

    /**
     * Validate numeric value.
     */
    public function numeric(string $field, mixed $value): static
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->errors[$field][] = ($field) . ' debe ser un valor numérico.';
        }

        return $this;
    }

    /**
     * Validate a value is within a set of allowed values.
     */
    public function inList(string $field, mixed $value, array $allowed, string $label = ''): static
    {
        if (!empty($value) && !in_array($value, $allowed, true)) {
            $allowedStr = implode(', ', $allowed);
            $this->errors[$field][] = ($label ?: $field) . " debe ser uno de: {$allowedStr}.";
        }

        return $this;
    }

    /**
     * Check if validation passed.
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Validate and throw if invalid.
     */
    public function validate(): void
    {
        if (!$this->passes()) {
            throw new ValidationException('Error de validación', $this->errors);
        }
    }

    /**
     * Reset errors for reuse.
     */
    public function reset(): static
    {
        $this->errors = [];
        return $this;
    }
}
