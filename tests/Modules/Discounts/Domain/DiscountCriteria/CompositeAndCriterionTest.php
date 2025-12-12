<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\DiscountCriteria;

use App\Modules\Discounts\Domain\DiscountCriteria\AlwaysTrueCriterion;
use App\Modules\Discounts\Domain\DiscountCriteria\CompositeAndCriterion;
use App\Modules\Discounts\Domain\DiscountCriteria\MinimalQuantityCriterion;
use App\Modules\Discounts\Domain\DiscountCriteria\ProductCodeCriterion;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\TestCase;

class CompositeAndCriterionTest extends TestCase
{
    public function testAllCriteriaMustBeSatisfied(): void
    {
        $criterion = new CompositeAndCriterion(
            new ProductCodeCriterion('PROD1', 'PROD2'),
            new MinimalQuantityCriterion(new Quantity(5))
        );

        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(5));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }

    public function testFailsWhenFirstCriterionNotSatisfied(): void
    {
        $criterion = new CompositeAndCriterion(
            new ProductCodeCriterion('PROD1', 'PROD2'),
            new MinimalQuantityCriterion(new Quantity(5))
        );

        $product = new Product('PROD3', new Price(new Amount(1000), Currency::PLN), new Quantity(10));

        $this->assertFalse($criterion->isSatisfiedBy($product));
    }

    public function testFailsWhenSecondCriterionNotSatisfied(): void
    {
        $criterion = new CompositeAndCriterion(
            new ProductCodeCriterion('PROD1', 'PROD2'),
            new MinimalQuantityCriterion(new Quantity(5))
        );

        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(3));

        $this->assertFalse($criterion->isSatisfiedBy($product));
    }

    public function testWorksWithSingleCriterion(): void
    {
        $criterion = new CompositeAndCriterion(
            new ProductCodeCriterion('PROD1')
        );

        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }

    public function testWorksWithThreeCriteria(): void
    {
        $criterion = new CompositeAndCriterion(
            new ProductCodeCriterion('PROD1'),
            new MinimalQuantityCriterion(new Quantity(5)),
            new AlwaysTrueCriterion()
        );

        $product = new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(10));

        $this->assertTrue($criterion->isSatisfiedBy($product));
    }
}
