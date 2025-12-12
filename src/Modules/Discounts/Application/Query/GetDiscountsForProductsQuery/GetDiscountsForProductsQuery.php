<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery;

use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\DiscountCollection;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Product\ProductCollection;

readonly class GetDiscountsForProductsQuery
{
    /**
     * @param string[] $selectedProducts
     */
    public function __construct(
        public ProductCollection $products,
        public DiscountCollection $discounts,
        public array $selectedProducts,
    ) {
    }
}
