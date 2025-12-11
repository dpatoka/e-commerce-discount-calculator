<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\Model;

use App\Modules\Discounts\Domain\Model\Amount;
use App\Modules\Discounts\Domain\Model\Percentage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PercentageTest extends TestCase
{
    public static function ofAmountProvider(): array
    {
        return [
            '10% of $100.00' => [10, 10000, 1000],
            '100% of $100.00' => [100, 10000, 10000],
            '25% of $50.00' => [25, 5000, 1250],

            // Rounding up (>= 0.5)
            '15% of $33.33' => [15, 3333, 500],
            '33% of $10.01' => [33, 1001, 330],
            '1% of $99' => [1, 99, 1],

            // Rounding down (< 0.5)
            '10% of $99.99' => [10, 9999, 1000],
            '7% of $142.85' => [7, 14285, 1000],
            '3% of $33.33' => [3, 3333, 100],

            // Edge cases
            '1% of $1' => [1, 1, 0],
            '50% of $1' => [50, 1, 1],
            '150% of $100.00' => [150, 10000, 15000],
        ];
    }

    public static function invalidPercentageProvider(): array
    {
        return [
            'zero percent' => [0],
            'negative percent' => [-10],
            'large negative percent' => [-100],
        ];
    }

    #[DataProvider('ofAmountProvider')]
    public function testOfAmount(int $percentage, int $amountCents, int $expectedCents): void
    {
        $percentageObj = new Percentage($percentage);
        $amountObj = new Amount($amountCents);

        $result = $percentageObj->ofAmount($amountObj);

        $this->assertEquals($expectedCents, $result->quantity);
    }

    #[DataProvider('invalidPercentageProvider')]
    public function testInvalidPercentageThrowsException(int $invalidPercentage): void
    {
        $this->expectExceptionMessage('Percentage cannot be less or equal 0');

        new Percentage($invalidPercentage);
    }
}
