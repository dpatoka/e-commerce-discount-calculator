<?php

declare(strict_types=1);

namespace App\Modules\Discounts;

use App\Modules\Discounts\Domain\Port\CurrencyProviderInterface;
use App\SharedKernel\Domain\Currency;

/**
 * This is a naive implementation to show concept only
 */
readonly class ConfigCurrencyProvider implements CurrencyProviderInterface
{
    private const Currency CONFIG = Currency::PLN;

    public function getCurrency(): Currency
    {
        return self::CONFIG;
    }
}
