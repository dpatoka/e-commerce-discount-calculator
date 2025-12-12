<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\Model\Product;

readonly class CompositeAndCriterion implements DiscountCriterionInterface
{
    /** @var DiscountCriterionInterface[] */
    private array $criteria;

    public function __construct(DiscountCriterionInterface ...$criteria)
    {
        $this->criteria = $criteria;
    }

    public function isSatisfiedBy(Product $product): bool
    {
        foreach ($this->criteria as $criterion) {
            if ($criterion->isSatisfiedBy($product) === false) {
                return false;
            }
        }

        return true;
    }
}
