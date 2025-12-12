<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\DiscountCriteria\MinimalQuantityCriterion;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\TestCase;

class MinimalQuantityCriterionTest extends TestCase
{
    public function testMatchesWhenQuantityEqualsMinimum(): void
    {
        $criterion = new MinimalQuantityCriterion(new Quantity(5));
        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(5));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }

    public function testMatchesWhenQuantityExceedsMinimum(): void
    {
        $criterion = new MinimalQuantityCriterion(new Quantity(5));
        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(10));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }

    public function testDoesNotMatchWhenQuantityBelowMinimum(): void
    {
        $criterion = new MinimalQuantityCriterion(new Quantity(5));
        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(4));

        $this->assertFalse($criterion->isSatisfiedBy($product));
    }

    public function testMatchesBoundaryQuantityOfOne(): void
    {
        $criterion = new MinimalQuantityCriterion(new Quantity(1));
        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }
}
