<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Interface\API\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class DiscountRequest
{
    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_VOLUME = 'volume';

    public function __construct(
        #[Assert\NotBlank(message: 'Discount type is required')]
        #[Assert\Choice(
            choices: [self::TYPE_FIXED, self::TYPE_PERCENTAGE, self::TYPE_VOLUME],
            message: 'Invalid discount type. Allowed: fixed, percentage, volume'
        )]
        public string $type,
        #[Assert\When(
            expression: 'this.type in ["fixed", "volume"]',
            constraints: [
                new Assert\NotNull(message: 'amountInCents is required for fixed/volume discounts'),
                new Assert\Type('integer'),
                new Assert\Positive(message: 'amountInCents must be positive'),
            ]
        )]
        public ?int $amountInCents = null,
        #[Assert\When(
            expression: 'this.type === "percentage"',
            constraints: [
                new Assert\NotNull(message: 'percentage is required for percentage discounts'),
                new Assert\Type('integer'),
                new Assert\Range(min: 1, max: 100, notInRangeMessage: 'Percentage must be between 1 and 100'),
            ]
        )]
        public ?int $percentage = null,
        #[Assert\When(
            expression: 'this.type === "volume"',
            constraints: [
                new Assert\NotNull(message: 'quantity is required for volume discounts'),
                new Assert\Type('integer'),
                new Assert\Positive(message: 'quantity must be positive'),
            ]
        )]
        public ?int $quantity = null,
    ) {
    }
}
