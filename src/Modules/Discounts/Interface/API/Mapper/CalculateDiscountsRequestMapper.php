<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Interface\API\Mapper;

use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\GetDiscountsForProductsQuery;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\DiscountCollection;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\DiscountStrategyInterface;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\FixedDiscount;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\PercentageDiscount;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Discount\VolumeDiscount;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Product\Product;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Product\ProductCollection;
use App\Modules\Discounts\Interface\API\Request\CalculateDiscountsRequest;
use App\Modules\Discounts\Interface\API\Request\DiscountRequest;
use App\Modules\Discounts\Interface\API\Request\ProductRequest;
use InvalidArgumentException;

final readonly class CalculateDiscountsRequestMapper
{
    public function toQuery(CalculateDiscountsRequest $request): GetDiscountsForProductsQuery
    {
        return new GetDiscountsForProductsQuery(
            $this->mapProducts($request->products),
            $this->mapDiscounts($request->discounts),
            $request->selectedProducts,
        );
    }

    /**
     * @param ProductRequest[] $products
     */
    private function mapProducts(array $products): ProductCollection
    {
        $mapped = array_map(
            static fn (ProductRequest $product) => new Product($product->code, $product->quantity),
            $products
        );

        return new ProductCollection(...$mapped);
    }

    /**
     * @param DiscountRequest[] $discounts
     */
    private function mapDiscounts(array $discounts): DiscountCollection
    {
        $mapped = array_map(
            fn (DiscountRequest $discount) => $this->mapDiscount($discount),
            $discounts
        );

        return new DiscountCollection(...$mapped);
    }

    private function mapDiscount(DiscountRequest $discount): DiscountStrategyInterface
    {
        if ($discount->type === DiscountRequest::TYPE_FIXED) {
            if ($discount->amountInCents === null) {
                throw new InvalidArgumentException('Fixed discount requires amountInCents');
            }

            return new FixedDiscount($discount->amountInCents);
        }

        if ($discount->type === DiscountRequest::TYPE_PERCENTAGE) {
            if ($discount->percentage === null) {
                throw new InvalidArgumentException('Percentage discount requires percentage');
            }

            return new PercentageDiscount($discount->percentage);
        }

        if ($discount->type === DiscountRequest::TYPE_VOLUME) {
            if ($discount->amountInCents === null) {
                throw new InvalidArgumentException('Volume discount requires amountInCents');
            }

            if ($discount->quantity === null) {
                throw new InvalidArgumentException('Volume discount requires quantity');
            }

            return new VolumeDiscount(
                $discount->amountInCents,
                $discount->quantity
            );
        }

        throw new InvalidArgumentException("Unknown discount type: {$discount->type}");
    }
}
