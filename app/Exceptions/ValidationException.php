<?php

declare(strict_types=1);

namespace App\Exceptions;

class ValidationException extends BaseException
{
    protected string $type = 'validation';
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Dados inválidos');
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
