<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\DiscountStrategies\VolumeDiscount;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\TestCase;

class VolumeDiscountTest extends TestCase
{
    public function testApplyDiscountToProductMeetingMinimalQuantity(): void
    {
        $discount = new VolumeDiscount(new Amount(100), new Quantity(5));
        $product = new Product(
            'PROD1',
            new Price(new Amount(1000), Currency::PLN),
            new Quantity(5)
        );
        $collection = new ProductCollection($product);

        $result = $discount->apply($collection);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(900, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testApplyDiscountToProductExceedingMinimalQuantity(): void
    {
        $discount = new VolumeDiscount(new Amount(200), new Quantity(3));
        $product = new Product(
            'PROD1',
            new Price(new Amount(5000), Currency::PLN),
            new Quantity(10)
        );
        $collection = new ProductCollection($product);

        $result = $discount->apply($collection);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(4800, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testExcludeProductBelowMinimalQuantity(): void
    {
        $discount = new VolumeDiscount(new Amount(50), new Quantity(5));
        $product = new Product(
            'PROD1',
            new Price(new Amount(1000), Currency::PLN),
            new Quantity(4)
        );
        $collection = new ProductCollection($product);

        $result = $discount->apply($collection);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(1000, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testApplyDiscountToMixedQuantities(): void
    {
        $discount = new VolumeDiscount(new Amount(100), new Quantity(5));
        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(3)),
            new Product('PROD2', new Price(new Amount(2000), Currency::PLN), new Quantity(5)),
            new Product('PROD3', new Price(new Amount(1500), Currency::PLN), new Quantity(10)),
            new Product('PROD4', new Price(new Amount(3000), Currency::PLN), new Quantity(4))
        );

        $result = $discount->apply($products);

        $product2 = $result->toArray()[0];
        $this->assertEquals('PROD1', $product2->getCode());
        $this->assertEquals(1000, $product2->getPrice()->getAmount());

        $product2 = $result->toArray()[1];
        $this->assertEquals('PROD2', $product2->getCode());
        $this->assertEquals(1900, $product2->getPrice()->getAmount());

        $product3 = $result->toArray()[2];
        $this->assertEquals('PROD3', $product3->getCode());
        $this->assertEquals(1400, $product3->getPrice()->getAmount());

        $product4 = $result->toArray()[3];
        $this->assertEquals('PROD4', $product4->getCode());
        $this->assertEquals(3000, $product4->getPrice()->getAmount());
    }

    public function testApplyDiscountToEmptyCollection(): void
    {
        $discount = new VolumeDiscount(new Amount(100), new Quantity(5));
        $collection = new ProductCollection();

        $result = $discount->apply($collection);

        $this->assertTrue($result->isEmpty());
    }
}
