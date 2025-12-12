<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount;

readonly class PercentageDiscount implements DiscountStrategyInterface
{
    public function __construct(public int $percentage)
    {
    }
}
