<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain;

use App\Modules\Discounts\Domain\DiscountFactory;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Percentage;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\TestCase;

class DiscountFactoryTest extends TestCase
{
    // ========== FIXED DISCOUNT TESTS ==========

    public function testFixedDiscountOnEmptyCollection(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createFixedDiscount(new Amount(100));

        $result = $discount->apply(new ProductCollection());

        $this->assertTrue($result->isEmpty());
    }

    public function testFixedDiscountOnSingleProduct(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createFixedDiscount(new Amount(300));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1))
        );

        $result = $discount->apply($products);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(700, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testFixedDiscountOnMultipleProducts(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createFixedDiscount(new Amount(300));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
            new Product('PROD2', new Price(new Amount(1500), Currency::PLN), new Quantity(1))
        );

        $result = $discount->apply($products);

        $this->assertEquals(2, $result->count());
        $this->assertEquals(700, $result->toArray()[0]->getPrice()->getAmount());
        $this->assertEquals(1200, $result->toArray()[1]->getPrice()->getAmount());
    }

    public function testFixedDiscountOnlyForSpecifiedProduct(): void
    {
        $discount = DiscountFactory::forSpecifiedProducts('PROD1')
            ->createFixedDiscount(new Amount(300));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
            new Product('PROD2', new Price(new Amount(1500), Currency::PLN), new Quantity(1))
        );

        $result = $discount->apply($products);

        $this->assertEquals(2, $result->count());
        $this->assertEquals(700, $result->toArray()[0]->getPrice()->getAmount());
        $this->assertEquals(1500, $result->toArray()[1]->getPrice()->getAmount()); // No discount
    }

    public function testFixedDiscountForMultipleSpecifiedProducts(): void
    {
        $discount = DiscountFactory::forSpecifiedProducts('PROD1', 'PROD3')
            ->createFixedDiscount(new Amount(200));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
            new Product('PROD2', new Price(new Amount(1500), Currency::PLN), new Quantity(1)),
            new Product('PROD3', new Price(new Amount(2000), Currency::PLN), new Quantity(1))
        );

        $result = $discount->apply($products);

        $this->assertEquals(800, $result->toArray()[0]->getPrice()->getAmount());
        $this->assertEquals(1500, $result->toArray()[1]->getPrice()->getAmount()); // No discount
        $this->assertEquals(1800, $result->toArray()[2]->getPrice()->getAmount());
    }

    // ========== PERCENTAGE DISCOUNT TESTS ==========

    public function testPercentageDiscountOnSingleProduct(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createPercentageDiscount(new Percentage(20));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1))
        );

        $result = $discount->apply($products);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(800, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testPercentageDiscountOnMultipleProducts(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createPercentageDiscount(new Percentage(10));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
            new Product('PROD2', new Price(new Amount(2000), Currency::PLN), new Quantity(2)),
            new Product('PROD3', new Price(new Amount(500), Currency::PLN), new Quantity(3))
        );

        $result = $discount->apply($products);

        $this->assertEquals(3, $result->count());
        $this->assertEquals(900, $result->toArray()[0]->getPrice()->getAmount());
        $this->assertEquals(1800, $result->toArray()[1]->getPrice()->getAmount());
        $this->assertEquals(450, $result->toArray()[2]->getPrice()->getAmount());
    }

    public function testPercentageDiscountOnEmptyCollection(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createPercentageDiscount(new Percentage(15));

        $result = $discount->apply(new ProductCollection());

        $this->assertTrue($result->isEmpty());
    }

    public function testPercentageDiscountOnlyForSpecifiedProducts(): void
    {
        $discount = DiscountFactory::forSpecifiedProducts('PROD1', 'PROD3')
            ->createPercentageDiscount(new Percentage(25));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
            new Product('PROD2', new Price(new Amount(2000), Currency::PLN), new Quantity(1)),
            new Product('PROD3', new Price(new Amount(800), Currency::PLN), new Quantity(1))
        );

        $result = $discount->apply($products);

        $this->assertEquals(750, $result->toArray()[0]->getPrice()->getAmount());
        $this->assertEquals(2000, $result->toArray()[1]->getPrice()->getAmount()); // No discount
        $this->assertEquals(600, $result->toArray()[2]->getPrice()->getAmount());
    }

    // ========== VOLUME DISCOUNT TESTS ==========

    public function testVolumeDiscountForProductMeetingMinimalQuantity(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createVolumeDiscount(new Amount(100), new Quantity(5));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(5))
        );

        $result = $discount->apply($products);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(900, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testVolumeDiscountForProductExceedingMinimalQuantity(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createVolumeDiscount(new Amount(200), new Quantity(3));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(5000), Currency::PLN), new Quantity(10))
        );

        $result = $discount->apply($products);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(4800, $result->toArray()[0]->getPrice()->getAmount());
    }

    public function testVolumeDiscountExcludesProductBelowMinimalQuantity(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createVolumeDiscount(new Amount(50), new Quantity(5));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(4))
        );

        $result = $discount->apply($products);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(1000, $result->toArray()[0]->getPrice()->getAmount()); // No discount
    }

    public function testVolumeDiscountOnMixedQuantities(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createVolumeDiscount(new Amount(100), new Quantity(5));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(3)),
            new Product('PROD2', new Price(new Amount(2000), Currency::PLN), new Quantity(5)),
            new Product('PROD3', new Price(new Amount(1500), Currency::PLN), new Quantity(10)),
            new Product('PROD4', new Price(new Amount(3000), Currency::PLN), new Quantity(4))
        );

        $result = $discount->apply($products);

        $this->assertEquals('PROD1', $result->toArray()[0]->getCode());
        $this->assertEquals(1000, $result->toArray()[0]->getPrice()->getAmount()); // No discount

        $this->assertEquals('PROD2', $result->toArray()[1]->getCode());
        $this->assertEquals(1900, $result->toArray()[1]->getPrice()->getAmount());

        $this->assertEquals('PROD3', $result->toArray()[2]->getCode());
        $this->assertEquals(1400, $result->toArray()[2]->getPrice()->getAmount());

        $this->assertEquals('PROD4', $result->toArray()[3]->getCode());
        $this->assertEquals(3000, $result->toArray()[3]->getPrice()->getAmount()); // No discount
    }

    public function testVolumeDiscountOnEmptyCollection(): void
    {
        $discount = DiscountFactory::forAnyProduct()
            ->createVolumeDiscount(new Amount(100), new Quantity(5));

        $result = $discount->apply(new ProductCollection());

        $this->assertTrue($result->isEmpty());
    }

    public function testVolumeDiscountOnlyForSpecifiedProductsMeetingQuantity(): void
    {
        $discount = DiscountFactory::forSpecifiedProducts('PROD1', 'PROD2')
            ->createVolumeDiscount(new Amount(150), new Quantity(5));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(2000), Currency::PLN), new Quantity(6)),
            new Product('PROD2', new Price(new Amount(1500), Currency::PLN), new Quantity(3)),
            new Product('PROD3', new Price(new Amount(3000), Currency::PLN), new Quantity(10))
        );

        $result = $discount->apply($products);

        $this->assertEquals(1850, $result->toArray()[0]->getPrice()->getAmount()); // Discount applied
        $this->assertEquals(1500, $result->toArray()[1]->getPrice()->getAmount()); // Not enough quantity
        $this->assertEquals(3000, $result->toArray()[2]->getPrice()->getAmount()); // Not in product list
    }

    public function testVolumeDiscountForSpecifiedProductsRequiresBothConditions(): void
    {
        $discount = DiscountFactory::forSpecifiedProducts('PROD1')
            ->createVolumeDiscount(new Amount(100), new Quantity(5));

        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(2000), Currency::PLN), new Quantity(5)), // Both match
            new Product('PROD2', new Price(new Amount(2000), Currency::PLN), new Quantity(10)), // Quantity match, wrong product
            new Product('PROD1', new Price(new Amount(2000), Currency::PLN), new Quantity(3))  // Product match, not enough quantity
        );

        $result = $discount->apply($products);

        $this->assertEquals(1900, $result->toArray()[0]->getPrice()->getAmount()); // Discount
        $this->assertEquals(2000, $result->toArray()[1]->getPrice()->getAmount()); // No discount
        $this->assertEquals(2000, $result->toArray()[2]->getPrice()->getAmount()); // No discount
    }
}
