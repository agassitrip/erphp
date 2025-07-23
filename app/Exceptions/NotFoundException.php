<?php

declare(strict_types=1);

namespace App\Exceptions;

class NotFoundException extends BaseException
{
    protected string $type = 'not_found';
}
