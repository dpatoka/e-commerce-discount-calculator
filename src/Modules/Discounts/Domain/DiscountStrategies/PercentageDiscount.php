<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\Model\Percentage;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;

readonly class PercentageDiscount implements DiscountInterface
{
    public function __construct(private Percentage $percentage)
    {
    }

    public function apply(ProductCollection $products): ProductCollection
    {
        $discountedProducts = [];
        /** @var Product $product */
        foreach ($products->getIterator() as $product) {
            $discountedProducts[] = $product->subtractPricePercentage($this->percentage);
        }

        return new ProductCollection(...$discountedProducts);
    }
}
