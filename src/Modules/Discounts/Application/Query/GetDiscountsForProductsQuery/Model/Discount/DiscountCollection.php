<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount;

use App\SharedKernel\Domain\CollectionInterface;
use ArrayIterator;
use Iterator;

/**
 * @implements CollectionInterface<DiscountStrategyInterface>
 */
class DiscountCollection implements CollectionInterface
{
    /**
     * @var DiscountStrategyInterface[]
     */
    private array $items;

    public function __construct(
        DiscountStrategyInterface ... $discounts
    ) {
        $this->items = $discounts;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    /** @return Iterator<DiscountStrategyInterface> */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->items);
    }
}
