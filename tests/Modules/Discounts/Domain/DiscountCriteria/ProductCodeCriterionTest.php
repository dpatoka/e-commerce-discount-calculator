<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\DiscountCriteria\ProductCodeCriterion;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\TestCase;

class ProductCodeCriterionTest extends TestCase
{
    public function testMatchesSingleProductCode(): void
    {
        $criterion = new ProductCodeCriterion('PROD1');
        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }

    public function testDoesNotMatchDifferentProductCode(): void
    {
        $criterion = new ProductCodeCriterion('PROD1');
        $product = new Product('PROD2', new Price(new Amount(1000), Currency::PLN), new Quantity(1));

        $this->assertFalse($criterion->isSatisfiedBy($product));
    }

    public function testMatchesOneOfMultipleProductCodes(): void
    {
        $criterion = new ProductCodeCriterion('PROD1', 'PROD2', 'PROD3');
        $product = new Product('PROD2', new Price(new Amount(1000), Currency::PLN), new Quantity(1));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }

    public function testDoesNotMatchWhenProductNotInList(): void
    {
        $criterion = new ProductCodeCriterion('PROD1', 'PROD2');
        $product = new Product('PROD3', new Price(new Amount(1000), Currency::PLN), new Quantity(1));

        $this->assertFalse($criterion->isSatisfiedBy($product));
    }
}
