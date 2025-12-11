<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain;

interface DiscountInterface
{
    public function apply(ProductCollection $products): ProductCollection;
}
