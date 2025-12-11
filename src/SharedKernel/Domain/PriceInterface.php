<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

interface PriceInterface
{
    public function getAmount(): int;

    public function getCurrency(): string;

    //    public function add(PriceInterface $price): self;
}
