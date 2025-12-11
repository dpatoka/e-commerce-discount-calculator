<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain;

use App\Modules\Discounts\Domain\DiscountCalculator;
use App\Modules\Discounts\Domain\DiscountStrategies\DiscountInterface;
use App\Modules\Discounts\Domain\DiscountStrategies\FixedDiscount;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Port\CurrencyProviderInterface;
use App\SharedKernel\Domain\Currency;
use App\Tests\TestHelpers\Stubs\ZeroDiscountStub;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorTest extends TestCase
{
    /**
     * @return array<string, array{discounts: array<int, mixed>, expectException: bool, exceptionMessage: string}>
     */
    public static function constructProvider(): array
    {
        return [
            'no discounts' => [
                'discounts' => [],
                'expectException' => true,
                'exceptionMessage' => 'No discounts provided'
            ],
            'not discount passed' => [
                'discounts' => [
                    new ZeroDiscountStub(),
                    new class () {},
                ],
                'expectException' => true,
                'exceptionMessage' => 'Argument #2 must be of type'
            ],
            'two discounts' => [
                'discounts' => [
                    new ZeroDiscountStub(),
                    new ZeroDiscountStub(),
                ],
                'expectException' => false,
                'exceptionMessage' => ''
            ],
        ];
    }

    /**
     * @param array<int, DiscountInterface> $discounts
     */
    #[DataProvider('constructProvider')]
    public function testConstruct(
        array $discounts,
        bool $expectException,
        string $exceptionMessage
    ): void {
        if ($expectException) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        $calc = $this->getDiscountCalculator(...$discounts);
        $this->assertInstanceOf(DiscountCalculator::class, $calc);
    }

    public function testApplyToEmptyProductCollection(): void
    {
        $products = new ProductCollection();

        $calc = $this->getDiscountCalculator(new ZeroDiscountStub());
        $price = $calc->apply($products);

        $this->assertEquals(0, $price->getAmount());
    }

    public function testApplyToOneProduct(): void
    {
        $products = new ProductCollection(
            new Product(
                'TEST',
                new Price(
                    new Amount(300),
                    Currency::PLN
                ),
                1
            )
        );

        $calc = $this->getDiscountCalculator(
            new FixedDiscount(
                new Amount(100)
            )
        );

        $price = $calc->apply($products);
        $this->assertEquals(200, $price->getAmount());
    }

    # TODO:
    # apply to 3 items
    # apply to 1 of 3 items
    # apply 3 discount types on 3 items

    private function getDiscountCalculator(DiscountInterface ...$discounts): DiscountCalculator
    {
        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);
        $currencyProvider->method('getCurrency')
            ->willReturn(Currency::PLN);

        return new DiscountCalculator($currencyProvider, ...$discounts);
    }
}
