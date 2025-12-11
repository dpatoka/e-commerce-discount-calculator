<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\Model\ProductCollection;

interface DiscountInterface
{
    public function apply(ProductCollection $products): ProductCollection;
}
