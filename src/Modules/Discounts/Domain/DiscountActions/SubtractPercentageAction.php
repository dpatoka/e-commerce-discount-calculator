<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountActions;

use App\Modules\Discounts\Domain\Model\Percentage;
use App\Modules\Discounts\Domain\Model\Product;

readonly class SubtractPercentageAction implements DiscountActionInterface
{
    public function __construct(
        private Percentage $percentage
    ) {
    }

    public function execute(Product $product): Product
    {
        return $product->subtractPricePercentage($this->percentage);
    }
}
