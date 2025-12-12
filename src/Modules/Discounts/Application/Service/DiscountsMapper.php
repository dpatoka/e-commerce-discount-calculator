<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Service;

use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\DiscountCollection;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\DiscountStrategyInterface;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\FixedDiscount;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\PercentageDiscount;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\VolumeDiscount;
use App\Modules\Discounts\Domain\DiscountFactory;
use App\Modules\Discounts\Domain\DiscountStrategies\ComposableDiscount;
use App\Modules\Discounts\Domain\DiscountStrategies\DiscountInterface;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Percentage;
use App\Modules\Discounts\Domain\Model\Quantity;
use InvalidArgumentException;

readonly class DiscountsMapper
{
    /**
     * @param string[] $selectedProducts
     * @return DiscountInterface[]
     */
    public function map(DiscountCollection $discounts, array $selectedProducts): array
    {
        $factory = empty($selectedProducts)
            ? DiscountFactory::forAnyProduct()
            : DiscountFactory::forSpecifiedProducts(...$selectedProducts);

        $mapped = [];
        foreach ($discounts->getIterator() as $discount) {
            $mapped[] = $this->mapSingle($discount, $factory);
        }

        return $mapped;
    }

    private function mapSingle(DiscountStrategyInterface $discount, DiscountFactory $factory): DiscountInterface
    {
        return match (true) {
            $discount instanceof FixedDiscount => $this->getFixedDiscount($factory, $discount),
            $discount instanceof PercentageDiscount => $this->getPercentageDiscount($factory, $discount),
            $discount instanceof VolumeDiscount => $this->getVolumeDiscount($factory, $discount),
            default => throw new InvalidArgumentException(
                sprintf('Unknown discount type: %s', $discount::class)
            ),
        };
    }

    private function getFixedDiscount(DiscountFactory $factory, FixedDiscount $discount): ComposableDiscount
    {
        return $factory->createFixedDiscount(
            new Amount($discount->amountInCents)
        );
    }

    private function getPercentageDiscount(DiscountFactory $factory, PercentageDiscount $discount): ComposableDiscount
    {
        return $factory->createPercentageDiscount(
            new Percentage($discount->percentage)
        );
    }

    private function getVolumeDiscount(DiscountFactory $factory, VolumeDiscount $discount): ComposableDiscount
    {
        return $factory->createVolumeDiscount(
            new Amount($discount->amountInCents),
            new Quantity($discount->quantity)
        );
    }
}
