<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Exception;

use RuntimeException;

class NotFoundException extends RuntimeException
{
    public static function forResource(string $type, string $identifier): self
    {
        return new self(sprintf('%s "%s" not found', $type, $identifier));
    }
}
