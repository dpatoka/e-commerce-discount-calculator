<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

use App\SharedKernel\Domain\Exception\NotFoundException;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<string, PriceInterface>
 */
final readonly class PriceCollection implements IteratorAggregate, Countable
{
    /** @param array<string, PriceInterface> $items */
    public function __construct(
        private array $items = []
    ) {
    }

    public function get(string $code): PriceInterface
    {
        if (!isset($this->items[$code])) {
            throw NotFoundException::forResource('Price', $code);
        }

        return $this->items[$code];
    }

    public function has(string $code): bool
    {
        return isset($this->items[$code]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
