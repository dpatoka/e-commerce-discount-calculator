<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

interface ProductInterface
{
    public function getCode(): string;

    public function getPrice(): PriceInterface;

    public function getQuantity(): int;
}
