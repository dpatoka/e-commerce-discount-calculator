<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\DiscountStrategies\PercentageDiscount;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Percentage;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\TestCase;

class PercentageDiscountTest extends TestCase
{
    public function testApplyDiscountToSingleProduct(): void
    {
        $discount = new PercentageDiscount(new Percentage(20));
        $product = new Product(
            'PROD1',
            new Price(new Amount(1000), Currency::PLN),
            1
        );
        $collection = new ProductCollection($product);

        $result = $discount->apply($collection);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(800, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testApplyDiscountToMultipleProducts(): void
    {
        $discount = new PercentageDiscount(new Percentage(10));
        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), 1),
            new Product('PROD2', new Price(new Amount(2000), Currency::PLN), 2),
            new Product('PROD3', new Price(new Amount(500), Currency::PLN), 3)
        );

        $result = $discount->apply($products);

        $this->assertEquals(3, $result->count());
        $this->assertEquals(900, $result->toArray()[0]->getPrice()->getAmount());
        $this->assertEquals(1800, $result->toArray()[1]->getPrice()->getAmount());
        $this->assertEquals(450, $result->toArray()[2]->getPrice()->getAmount());
    }

    public function testApplyDiscountToEmptyCollection(): void
    {
        $discount = new PercentageDiscount(new Percentage(15));
        $collection = new ProductCollection();

        $result = $discount->apply($collection);

        $this->assertTrue($result->isEmpty());
    }
}
