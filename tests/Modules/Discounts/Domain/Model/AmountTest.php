<?php

declare(strict_types=1);

namespace App\Tests\Modules\Discounts\Domain\Model;

use App\Modules\Discounts\Domain\Model\Amount;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    public static function constructProvider(): array
    {
        return [
            'positive amount' => [100, false],
            'zero amount' => [0, false],
            'negative amount throws exception' => [-100, true],
        ];
    }

    public static function addProvider(): array
    {
        return [
            'add two positive amounts' => [100, 200, false, 300],
            'negative amount throws exception' => [-100, 200, true, null],
        ];
    }

    public static function subtractProvider(): array
    {
        return [
            'subtract resulting in positive amount' => [100, 99, false, 1],
            'subtract resulting in zero' => [100, 100, false, 0],
            'subtract resulting in negative throws exception' => [100, 101, true, null],
        ];
    }

    #[DataProvider('constructProvider')]
    public function testConstruct(int $quantity, bool $shouldThrow): void
    {
        if ($shouldThrow) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Amount cannot be less than 0');
        }

        $amount = new Amount($quantity);

        if (!$shouldThrow) {
            $this->assertSame($quantity, $amount->quantity);
        }
    }

    #[DataProvider('addProvider')]
    public function testAdd(int $quantity1, int $quantity2, bool $shouldThrow, ?int $expected): void
    {
        if ($shouldThrow) {
            $this->expectException(Exception::class);
        }

        $amount1 = new Amount($quantity1);
        $amount2 = new Amount($quantity2);

        if (!$shouldThrow) {
            $result = $amount1->add($amount2);
            $this->assertSame($expected, $result->quantity);
        }
    }

    #[DataProvider('subtractProvider')]
    public function testSubtract(int $quantity1, int $quantity2, bool $shouldThrow, ?int $expected): void
    {
        if ($shouldThrow) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Amount cannot be less than 0');
        }

        $amount1 = new Amount($quantity1);
        $amount2 = new Amount($quantity2);

        if (!$shouldThrow) {
            $result = $amount1->subtract($amount2);
            $this->assertSame($expected, $result->quantity);
        } else {
            $amount1->subtract($amount2);
        }
    }
}
