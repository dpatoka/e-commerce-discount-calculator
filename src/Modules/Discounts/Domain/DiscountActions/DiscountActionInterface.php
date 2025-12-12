<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountActions;

use App\Modules\Discounts\Domain\Model\Product;

interface DiscountActionInterface
{
    /**
     * Apply a discount action to a product
     */
    public function execute(Product $product): Product;
}
