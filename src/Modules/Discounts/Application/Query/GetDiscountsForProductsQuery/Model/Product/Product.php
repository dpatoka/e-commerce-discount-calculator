<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Product;

readonly class Product
{
    public function __construct(
        public string $code,
        public int $quantity
    ) {
    }
}
