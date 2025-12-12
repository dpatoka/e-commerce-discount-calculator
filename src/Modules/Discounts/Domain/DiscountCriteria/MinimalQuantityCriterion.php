<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\Quantity;

readonly class MinimalQuantityCriterion implements DiscountCriterionInterface
{
    public function __construct(private Quantity $minimalQuantity)
    {
    }

    public function isSatisfiedBy(Product $product): bool
    {
        return $product->getQuantity() >= $this->minimalQuantity->number;
    }
}
