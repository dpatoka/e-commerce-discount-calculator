<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\Model;

use Exception;

/**
 * The smallest unit of currency, e.g.: cents in USD
 */
readonly class Amount
{
    public function __construct(
        public int $quantity
    ) {
        if ($quantity < 0) {
            throw new Exception('Amount cannot be less than 0');
        }
    }

    public function add(Amount $amount): self
    {
        $sum = $this->quantity + $amount->quantity;

        return new Amount($sum);
    }

    public function subtract(Amount $amount): self
    {
        $sum = $this->quantity - $amount->quantity;

        return new Amount($sum);
    }
}
