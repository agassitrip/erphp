<?php

declare(strict_types=1);

namespace App\Exceptions;

class DuplicateException extends BaseException
{
    protected string $type = 'duplicate';
}
