<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\DiscountActions\DiscountActionInterface;
use App\Modules\Discounts\Domain\DiscountCriteria\DiscountCriterionInterface;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;

readonly class ComposableDiscount implements DiscountInterface
{
    public function __construct(
        private DiscountCriterionInterface $criterion,
        private DiscountActionInterface $action
    ) {
    }

    public function apply(ProductCollection $products): ProductCollection
    {
        $discountedProducts = [];

        /** @var Product $product */
        foreach ($products->getIterator() as $product) {
            $consideredProduct = $this->criterion->isSatisfiedBy($product)
                ? $this->action->execute($product)
                : $product;

            $discountedProducts[] = $consideredProduct;
        }

        return new ProductCollection(...$discountedProducts);
    }
}
