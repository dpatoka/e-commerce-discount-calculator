<?php

declare(strict_types=1);

namespace App\Tests\TestHelpers\Stubs;

use App\Modules\Discounts\Domain\DiscountInterface;
use App\Modules\Discounts\Domain\ProductCollection;

class ZeroDiscountStub implements DiscountInterface
{
    public function apply(ProductCollection $products): ProductCollection
    {
        return $products;
    }
}
