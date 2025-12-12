<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain;

use App\Modules\Discounts\Domain\DiscountActions\SubtractAmountAction;
use App\Modules\Discounts\Domain\DiscountActions\SubtractPercentageAction;
use App\Modules\Discounts\Domain\DiscountCriteria\AlwaysTrueCriterion;
use App\Modules\Discounts\Domain\DiscountCriteria\CompositeAndCriterion;
use App\Modules\Discounts\Domain\DiscountCriteria\MinimalQuantityCriterion;
use App\Modules\Discounts\Domain\DiscountCriteria\ProductCodeCriterion;
use App\Modules\Discounts\Domain\DiscountStrategies\ComposableDiscount;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Percentage;
use App\Modules\Discounts\Domain\Model\Quantity;

readonly class DiscountFactory
{
    /** @var string[]  */
    private array $productCodes;

    public static function forAnyProduct(): self
    {
        return new self();
    }

    public static function forSpecifiedProducts(string ...$productCodes): self
    {
        return new self(...$productCodes);
    }

    private function __construct(string... $productCodes)
    {
        $this->productCodes = $productCodes;
    }

    public function createFixedDiscount(Amount $amount): ComposableDiscount
    {
        $criterion = empty($this->productCodes)
            ? new AlwaysTrueCriterion()
            : new ProductCodeCriterion(...$this->productCodes);

        return new ComposableDiscount(
            $criterion,
            new SubtractAmountAction($amount)
        );
    }

    public function createPercentageDiscount(Percentage $percentage): ComposableDiscount
    {
        $criterion = empty($this->productCodes)
            ? new AlwaysTrueCriterion()
            : new ProductCodeCriterion(...$this->productCodes);

        return new ComposableDiscount(
            $criterion,
            new SubtractPercentageAction($percentage)
        );
    }

    public function createVolumeDiscount(Amount $amount, Quantity $minimalQuantity): ComposableDiscount
    {
        $criterion = empty($this->productCodes)
            ? new MinimalQuantityCriterion($minimalQuantity)
            : new CompositeAndCriterion(
                new ProductCodeCriterion(...$this->productCodes),
                new MinimalQuantityCriterion($minimalQuantity)
            );

        return new ComposableDiscount(
            $criterion,
            new SubtractAmountAction($amount)
        );
    }
}
