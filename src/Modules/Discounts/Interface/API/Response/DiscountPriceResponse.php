<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Interface\API\Response;

namespace App\Modules\Discounts\Interface\API\Response;

use App\SharedKernel\Domain\PriceInterface;
use JsonSerializable;

final readonly class DiscountPriceResponse implements JsonSerializable
{
    public static function fromPrice(PriceInterface $price): self
    {
        return new self(
            $price->getAmount(),
            $price->getCurrency(),
        );
    }

    public function __construct(
        public int $amount,
        public string $currency,
    ) {
    }

    /**
     * @return array{amount: int, currency: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }
}
