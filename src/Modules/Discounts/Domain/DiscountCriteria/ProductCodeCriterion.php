<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\Model\Product;

readonly class ProductCodeCriterion implements DiscountCriterionInterface
{
    /** @var string[] */
    private array $codes;

    public function __construct(string ...$codes)
    {
        $this->codes = $codes;
    }

    public function isSatisfiedBy(Product $product): bool
    {
        return in_array($product->getCode(), $this->codes, true);
    }
}
