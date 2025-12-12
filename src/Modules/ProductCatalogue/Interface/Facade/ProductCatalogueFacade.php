<?php

declare(strict_types=1);

namespace App\Modules\ProductCatalogue\Interface\Facade;

use App\Modules\ProductCatalogue\Domain\Repository\ProductRepositoryInterface;
use App\SharedKernel\Domain\PriceCollection;

readonly class ProductCatalogueFacade implements ProductCatalogueFacadeInterface
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function getPricesForCodes(string ...$codes): PriceCollection
    {
        return $this->productRepository->getPrices(... $codes);
    }
}
