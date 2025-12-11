<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use Iterator;

/**
 * @template-covariant TValue
 */
interface CollectionInterface
{
    public function isEmpty(): bool;

    /** @return TValue[] */
    public function toArray(): array;

    public function getIterator(): Iterator;
}
