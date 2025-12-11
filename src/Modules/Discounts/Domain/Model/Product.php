<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\Model;

use App\SharedKernel\Domain\PriceInterface;
use App\SharedKernel\Domain\ProductInterface;

readonly class Product implements ProductInterface
{
    public function __construct(
        private string $code,
        private Price $price,
        private Quantity $quantity
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity->number;
    }

    public function subtractPriceAmount(Amount $amount): self
    {
        $newPrice = $this->price->subtractAmount($amount);

        return $this->createWithNewPrice($newPrice);
    }

    public function subtractPricePercentage(Percentage $percentage): self
    {
        $subtrahend = $percentage->ofAmount($this->price->getAmountAsValueObject());

        return $this->subtractPriceAmount($subtrahend);
    }

    private function createWithNewPrice(Price $price): Product
    {
        return new self(
            $this->code,
            $price,
            $this->quantity
        );
    }
}
