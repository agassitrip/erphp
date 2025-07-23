<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected string $type;

    public function getType(): string
    {
        return $this->type;
    }
}
