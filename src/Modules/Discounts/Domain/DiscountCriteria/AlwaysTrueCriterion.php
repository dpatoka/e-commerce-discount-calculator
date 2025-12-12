<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\Model\Product;

class AlwaysTrueCriterion implements DiscountCriterionInterface
{
    public function isSatisfiedBy(Product $product): bool
    {
        return true;
    }
}
