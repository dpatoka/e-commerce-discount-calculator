<?php

declare(strict_types=1);

namespace App\Modules\ProductCatalogue\Domain\Repository;

use App\SharedKernel\Domain\PriceCollection;

interface ProductRepositoryInterface
{
    public function getPrices(string ... $codes): PriceCollection;
}
