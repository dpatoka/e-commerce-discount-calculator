<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\Model;

use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProductCollectionTest extends TestCase
{
    public static function countProvider(): array
    {
        return [
            '0 products' => [
                new ProductCollection(),
                0
            ],
            '2 products' => [
                new ProductCollection(
                    new Product('CODE1', new Price(new Amount(100), Currency::PLN), new Quantity(1)),
                    new Product('CODE2', new Price(new Amount(200), Currency::PLN), new Quantity(1))
                ),
                2
            ],
        ];
    }

    public static function isEmptyProvider(): array
    {
        return [
            '0 products' => [
                new ProductCollection(),
                true
            ],
            '1 product' => [
                new ProductCollection(
                    new Product('CODE1', new Price(new Amount(100), Currency::PLN), new Quantity(1))
                ),
                false
            ],
        ];
    }

    public static function totalPriceProvider(): array
    {
        return [
            '0 products' => [
                new ProductCollection(),
                0,
            ],
            '2 products' => [
                new ProductCollection(
                    new Product('CODE1', new Price(new Amount(100), Currency::PLN), new Quantity(1)),
                    new Product('CODE2', new Price(new Amount(250), Currency::PLN), new Quantity(1))
                ),
                350,
            ],
        ];
    }

    #[DataProvider('countProvider')]
    public function testCount(ProductCollection $collection, int $expectedCount): void
    {
        $this->assertEquals($expectedCount, $collection->count());
    }

    #[DataProvider('isEmptyProvider')]
    public function testIsEmpty(ProductCollection $collection, bool $expectedIsEmpty): void
    {
        $this->assertEquals($expectedIsEmpty, $collection->isEmpty());
    }

    #[DataProvider('totalPriceProvider')]
    public function testGetTotalPrice(ProductCollection $collection, int $expectedAmount): void
    {
        $totalAmount = $collection->getTotalAmount();
        $this->assertEquals($expectedAmount, $totalAmount->quantity);
    }
}
