<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain;

use DomainException;

class DiscountCalculator
{
    /**
     * @var DiscountInterface[]
     */
    private array $discounts = [];

    public function __construct(
        DiscountInterface ... $discounts
    ) {
        if (empty($discounts)) {
            throw new DomainException('No discounts provided');
        }

        $this->discounts = $discounts;
    }
}
