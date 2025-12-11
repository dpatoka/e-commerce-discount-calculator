<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\Model;

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

    public function __construct(
        ProductInterface ... $products
    ) {
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

    public function getTotalAmount(): Amount
    {
        if ($this->isEmpty()) {
            return new Amount(0);
        }

        $total = array_reduce(
            $this->items,
            static fn (int $carry, ProductInterface $product) => $carry + $product->getPrice()->getAmount(),
            0
        );

        return new Amount($total);
    }
}
