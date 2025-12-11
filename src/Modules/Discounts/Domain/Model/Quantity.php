<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\Model;

use Exception;

readonly class Quantity
{
    public function __construct(
        public int $number
    ) {
        if ($number < 1) {
            throw new Exception('Quantity cannot be less than 1');
        }
    }
}
