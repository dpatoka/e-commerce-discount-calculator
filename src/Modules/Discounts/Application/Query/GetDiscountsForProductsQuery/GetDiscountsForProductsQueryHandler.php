<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery;

use App\Modules\Discounts\Application\Service\DiscountsMapper;
use App\Modules\Discounts\Application\Service\ProductsMapper;
use App\Modules\Discounts\Domain\DiscountCalculator;
use App\Modules\Discounts\Domain\Port\CurrencyProviderInterface;
use App\SharedKernel\Domain\PriceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetDiscountsForProductsQueryHandler
{
    public function __construct(
        private DiscountsMapper $discountsMapper,
        private ProductsMapper $productsMapper,
        private CurrencyProviderInterface $currencyProvider,
    ) {
    }

    public function __invoke(GetDiscountsForProductsQuery $query): PriceInterface
    {
        $discounts = $this->discountsMapper->map($query->discounts, $query->selectedProducts);
        $products = $this->productsMapper->map($query->products);

        $calculator = new DiscountCalculator(
            $this->currencyProvider,
            ...$discounts
        );

        return $calculator->apply($products);
    }
}
