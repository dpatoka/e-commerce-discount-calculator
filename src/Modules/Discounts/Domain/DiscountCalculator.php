<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain;

use App\Modules\Discounts\Domain\Port\CurrencyProviderInterface;
use App\SharedKernel\Domain\PriceInterface;
use DomainException;

readonly class DiscountCalculator
{
    /**
     * @var DiscountInterface[]
     */
    private array $discounts;

    public function __construct(
        private CurrencyProviderInterface $currencyProvider,
        DiscountInterface ... $discounts
    ) {
        if (empty($discounts)) {
            throw new DomainException('No discounts provided');
        }

        $this->discounts = $discounts;
    }

    public function apply(ProductCollection $products): PriceInterface
    {
        if ($products->isEmpty()) {
            return new Price(0, $this->currencyProvider->getCurrency());
        }
    }
}
