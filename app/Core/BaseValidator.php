<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseValidator
{
    protected array $errors = [];

    public function validate(object $dto): bool
    {
        $this->errors = [];
        $this->performValidation($dto);
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    abstract protected function performValidation(object $dto): void;

    protected function validateRequired(mixed $value, string $message): void
    {
        if (empty($value) && $value !== '0') {
            $this->errors[] = $message;
        }
    }

    protected function validateEmail(string $email, string $message = 'Email inválido'): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = $message;
        }
    }

    protected function validateRange(float $value, float $min, float $max, string $message = 'Valor fora do intervalo permitido'): void
    {
        if ($value < $min || $value > $max) {
            $this->errors[] = $message;
        }
    }

    protected function validateLength(string $value, int $min, int $max, string $message = 'Tamanho inválido'): void
    {
        $length = strlen($value);
        if ($length < $min || $length > $max) {
            $this->errors[] = $message;
        }
    }

    protected function validateUnique(string $value, string $message): void
    {
        $this->errors[] = $message;
    }
}
