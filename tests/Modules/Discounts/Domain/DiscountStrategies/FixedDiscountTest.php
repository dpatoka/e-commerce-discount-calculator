<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\DiscountStrategies;

use App\Modules\Discounts\Domain\DiscountStrategies\FixedDiscount;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\SharedKernel\Domain\Currency;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FixedDiscountTest extends TestCase
{
    public static function discountProvider(): array
    {
        return [
            'empty list' => [
                'discountAmount' => 100,
                'products' => new ProductCollection(),
                'expectedTotal' => 0,
            ],
            'accurate amount with one item' => [
                'discountAmount' => 300,
                'products' => new ProductCollection(
                    new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1))
                ),
                'expectedTotal' => 700,
            ],
            'accurate amount with two items' => [
                'discountAmount' => 300,
                'products' => new ProductCollection(
                    new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
                    new Product('PROD2', new Price(new Amount(1500), Currency::PLN), new Quantity(1))
                ),
                'expectedTotal' => 1900,
            ],
        ];
    }

    public static function discountTooLargeProvider(): array
    {
        return [
            'too big amount with one item' => [
                'discountAmount' => 1500,
                'products' => new ProductCollection(
                    new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1))
                ),
            ],
        ];
    }

    #[DataProvider('discountProvider')]
    public function testApply(int $discountAmount, ProductCollection $products, int $expectedTotal): void
    {
        $discount = new FixedDiscount(
            new Amount($discountAmount)
        );

        $result = $discount->apply($products);
        $this->assertEquals($expectedTotal, $result->getTotalAmount()->quantity);
    }

    #[DataProvider('discountTooLargeProvider')]
    public function testApplyWithTooLargeDiscount(int $discountAmount, ProductCollection $products): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Amount cannot be less than 0');

        $discount = new FixedDiscount(
            new Amount($discountAmount)
        );

        $discount->apply($products);
    }
}
