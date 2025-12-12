<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Service;

use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Product\Product as QueryProduct;
use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\Model\Product\ProductCollection as QueryProductCollection;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\Modules\Discounts\Domain\Port\ProductPriceProviderInterface;
use App\SharedKernel\Domain\Currency;
use App\SharedKernel\Domain\PriceInterface;

readonly class ProductsMapper
{
    public function __construct(
        private ProductPriceProviderInterface $priceProvider,
    ) {
    }

    public function map(QueryProductCollection $products): ProductCollection
    {
        $codes = $this->getCodes($products);
        $prices = $this->priceProvider->getForCodes(...$codes);

        $mapped = [];
        foreach ($products->getIterator() as $product) {
            $price = $prices->get($product->code);
            $mapped[] = $this->mapSingle($product, $price);
        }

        return new ProductCollection(...$mapped);
    }

    private function mapSingle(QueryProduct $product, PriceInterface $priceInterface): Product
    {
        $price = new Price(
            new Amount($priceInterface->getAmount()),
            Currency::from($priceInterface->getCurrency())
        );

        return new Product(
            $product->code,
            $price,
            new Quantity($product->quantity)
        );
    }

    /**
     * @return array|string[]
     */
    private function getCodes(QueryProductCollection $products): array
    {
        return array_map(
            static fn (QueryProduct $product) => $product->code,
            iterator_to_array($products->getIterator())
        );
    }
}
