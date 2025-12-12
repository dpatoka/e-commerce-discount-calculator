<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain;

use App\Modules\Discounts\Domain\DiscountCalculator;
use App\Modules\Discounts\Domain\DiscountFactory;
use App\Modules\Discounts\Domain\DiscountStrategies\DiscountInterface;
use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Percentage;
use App\Modules\Discounts\Domain\Model\Price;
use App\Modules\Discounts\Domain\Model\Product;
use App\Modules\Discounts\Domain\Model\ProductCollection;
use App\Modules\Discounts\Domain\Model\Quantity;
use App\Modules\Discounts\Domain\Port\CurrencyProviderInterface;
use App\SharedKernel\Domain\Currency;
use App\Tests\TestHelpers\Stubs\ZeroDiscountStub;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorTest extends TestCase
{
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
            new Product('PROD1', new Price(new Amount(300), Currency::PLN), new Quantity(1))
        );

        $discount = DiscountFactory::forAnyProduct()
            ->createFixedDiscount(new Amount(100));

        $calc = $this->getDiscountCalculator($discount);

        $price = $calc->apply($products);
        $this->assertEquals(200, $price->getAmount());
    }

    public function testApplyOneDiscountToThreeProducts(): void
    {
        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
            new Product('PROD2', new Price(new Amount(1500), Currency::PLN), new Quantity(1)),
            new Product('PROD3', new Price(new Amount(2000), Currency::PLN), new Quantity(1))
        );

        $discount = DiscountFactory::forAnyProduct()
            ->createPercentageDiscount(new Percentage(10));

        $calc = $this->getDiscountCalculator($discount);
        $price = $calc->apply($products);

        // Original total: 4500, after 10% discount: 4050
        $this->assertEquals(4050, $price->getAmount());
    }

    public function testApplyOneDiscountToOneOfThreeProducts(): void
    {
        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(1)),
            new Product('PROD2', new Price(new Amount(1500), Currency::PLN), new Quantity(1)),
            new Product('PROD3', new Price(new Amount(2000), Currency::PLN), new Quantity(1))
        );

        $discount = DiscountFactory::forSpecifiedProducts('PROD2')
            ->createFixedDiscount(new Amount(200));

        $calc = $this->getDiscountCalculator($discount);
        $price = $calc->apply($products);

        // PROD1: 1000, PROD2: 1300 (1500-200), PROD3: 2000 = 4300
        $this->assertEquals(4300, $price->getAmount());
    }

    public function testApplyThreeDiscountTypesOnThreeProducts(): void
    {
        $products = new ProductCollection(
            new Product('PROD1', new Price(new Amount(1000), Currency::PLN), new Quantity(2)),
            new Product('PROD2', new Price(new Amount(2000), Currency::PLN), new Quantity(1)),
            new Product('PROD3', new Price(new Amount(3000), Currency::PLN), new Quantity(10))
        );

        // Discount 1: 10% off all products
        $percentageDiscount = DiscountFactory::forAnyProduct()
            ->createPercentageDiscount(new Percentage(10));

        // Discount 2: 100 PLN off PROD1
        $fixedDiscount = DiscountFactory::forSpecifiedProducts('PROD1')
            ->createFixedDiscount(new Amount(100));

        // Discount 3: 200 PLN off when quantity >= 5
        $volumeDiscount = DiscountFactory::forAnyProduct()
            ->createVolumeDiscount(new Amount(200), new Quantity(5));

        $calc = $this->getDiscountCalculator(
            $percentageDiscount,
            $fixedDiscount,
            $volumeDiscount
        );

        $price = $calc->apply($products);

        // Expected calculation (discounts chain):
        // After 10% discount: PROD1=900, PROD2=1800, PROD3=2700
        // After 100 PLN off PROD1: PROD1=800, PROD2=1800, PROD3=2700
        // After volume discount (>=5): PROD1=800 (qty=2, no change), PROD2=1800 (qty=1, no change), PROD3=2500 (qty=10, -200)
        // Total: 800 + 1800 + 2500 = 5100
        $this->assertEquals(5100, $price->getAmount());
    }

    private function getDiscountCalculator(DiscountInterface ...$discounts): DiscountCalculator
    {
        $currencyProvider = $this->createMock(CurrencyProviderInterface::class);
        $currencyProvider->method('getCurrency')
            ->willReturn(Currency::PLN);

        return new DiscountCalculator($currencyProvider, ...$discounts);
    }
}
