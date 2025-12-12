<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\DiscountCriteria\AlwaysTrueCriterion;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\TestCase;

class AlwaysTrueCriterionTest extends TestCase
{
    public function testAlwaysReturnsTrueForAnyProduct(): void
    {
        $criterion = new AlwaysTrueCriterion();
        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }
}
