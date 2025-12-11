<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain;

use App\Modules\Discounts\Domain\DiscountCalculator;
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
                'exceptionMessage' => 'Argument #2 must be of type App\Modules\Discounts\Domain\DiscountInterface'
            ],
            'two discounts' => [
                'discounts' => [
                    new ZeroDiscountStub(),
                    new ZeroDiscountStub(),
                ],
                'expectException' => false,
                'exceptionMessage' => null
            ],
        ];
    }

    #[DataProvider('constructProvider')]
    public function testConstruct(array $discounts, bool $expectException, ?string $exceptionMessage): void
    {
        if ($expectException) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        $calc = new DiscountCalculator(...$discounts);
        $this->assertInstanceOf(DiscountCalculator::class, $calc);
    }

    # TODO:
    # apply to empty collection
    # apply to one item
    # apply to 3 items
    # apply to 1 of 3 items
    # apply 3 discount types on 3 items
}
