<?php

declare(strict_types=1);

namespace App\Modules\ProductCatalogue\Interface\Facade;

use App\SharedKernel\Domain\PriceCollection;

interface ProductCatalogueFacadeInterface
{
    public function getPricesForCodes(string ...$codes): PriceCollection;
}
