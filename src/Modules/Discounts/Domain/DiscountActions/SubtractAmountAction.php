<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountActions;

use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Product;

readonly class SubtractAmountAction implements DiscountActionInterface
{
    public function __construct(
        private Amount $amount
    ) {
    }

    public function execute(Product $product): Product
    {
        return $product->subtractPriceAmount($this->amount);
    }
}
