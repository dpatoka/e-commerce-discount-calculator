<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Domain\Port;

use App\SharedKernel\Domain\Currency;

interface CurrencyProviderInterface
{
    public function getCurrency(): Currency;
}
