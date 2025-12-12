<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Product;

use App\SharedKernel\Domain\CollectionInterface;
use ArrayIterator;
use Iterator;

/**
 * @implements CollectionInterface<Product>
 */
class ProductCollection implements CollectionInterface
{
    /**
     * @var Product[]
     */
    private array $items = [];

    public function __construct(
        Product ... $products
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
}
