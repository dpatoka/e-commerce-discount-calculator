<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;

readonly class FixedDiscount implements DiscountInterface
{
    public function __construct(public Amount $amount)
    {
    }

    public function apply(ProductCollection $products): ProductCollection
    {
        $discountedProducts = [];
        /** @var Product $product */
        foreach ($products->getIterator() as $product) {
            $discountedProducts[] = $product->subtractPriceAmount($this->amount);
        }

        return new ProductCollection(...$discountedProducts);
    }
}
