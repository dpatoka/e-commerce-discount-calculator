<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\Model;

use Exception;

readonly class Percentage
{
    public function __construct(
        public int $number
    ) {
        if ($number <= 0) {
            throw new Exception('Percentage cannot be less or equal 0');
        }
    }

    public function ofAmount(Amount $amount): Amount
    {
        $percentageOfAmount = ($amount->quantity * $this->number) / 100;
        $rounded = (int) round($percentageOfAmount);

        return new Amount($rounded);
    }
}
