<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain;

use App\SharedKernel\Domain\Currency;
use App\SharedKernel\Domain\PriceInterface;

readonly class Price implements PriceInterface
{
    public function __construct(
        private int $amount,
        private Currency $currency,
    ) {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency->value;
    }
}
