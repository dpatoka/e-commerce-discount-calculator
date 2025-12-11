<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain;

use App\SharedKernel\Domain\CollectionInterface;
use App\SharedKernel\Domain\ProductInterface;
use ArrayIterator;
use Iterator;

/**
 * @implements CollectionInterface<ProductInterface>
 */
class ProductCollection implements CollectionInterface
{
    /**
     * @var ProductInterface[]
     */
    private array $items = [];

    public function __construct(ProductInterface ... $products)
    {
        $this->items = $products;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
