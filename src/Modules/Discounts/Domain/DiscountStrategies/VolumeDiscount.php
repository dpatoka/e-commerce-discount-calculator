<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Model\Quantity;

readonly class VolumeDiscount implements DiscountInterface
{
    public function __construct(
        private Amount $amount,
        private Quantity $minimalQuantity
    ) {
    }

    public function apply(ProductCollection $products): ProductCollection
    {
        $discountedProducts = [];
        /** @var Product $product */
        foreach ($products->getIterator() as $product) {
            $consideredProduct = $product;

            if ($product->getQuantity() >= $this->minimalQuantity->number) {
                $consideredProduct = $product->subtractPriceAmount($this->amount);
            }

            $discountedProducts[] = $consideredProduct;
        }

        return new ProductCollection(...$discountedProducts);
    }
}
