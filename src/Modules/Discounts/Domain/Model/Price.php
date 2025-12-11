<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\Model;

use App\SharedKernel\Domain\Currency;
use App\SharedKernel\Domain\PriceInterface;

readonly class Price implements PriceInterface
{
    public static function createWithZero(Currency $currency): self
    {
        return new self(
            new Amount(0),
            $currency
        );
    }

    public function __construct(
        private Amount $amount,
        private Currency $currency,
    ) {
    }

    public function getAmount(): int
    {
        return $this->amount->quantity;
    }

    public function getAmountAsValueObject(): Amount
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency->value;
    }

    public function subtractAmount(Amount $amount): self
    {
        $sum = $this->amount->subtract($amount);

        return new self($sum, $this->currency);
    }
}
