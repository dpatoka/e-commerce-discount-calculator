<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Infrastructure\Domain\Port;

use App\Modules\Discounts\Domain\Port\ProductPriceProviderInterface;
use App\Modules\ProductCatalogue\Interface\Facade\ProductCatalogueFacadeInterface;
use App\SharedKernel\Domain\PriceCollection;

readonly class ProductPriceProviderAdapter implements ProductPriceProviderInterface
{
    public function __construct(private ProductCatalogueFacadeInterface $productCatalogueFacade)
    {
    }

    public function getForCodes(string ...$codes): PriceCollection
    {
        return $this->productCatalogueFacade->getPricesForCodes(... $codes);
    }
}
