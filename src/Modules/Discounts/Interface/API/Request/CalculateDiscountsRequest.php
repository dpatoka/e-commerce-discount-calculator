<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Interface\API\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CalculateDiscountsRequest
{
    /**
     * @param ProductRequest[] $products
     * @param DiscountRequest[] $discounts
     * @param string[] $selectedProducts
     */
    public function __construct(
        #[Assert\NotNull(message: 'Products are required')]
        #[Assert\Count(min: 1, minMessage: 'At least one product is required')]
        #[Assert\Valid]
        public array $products,
        #[Assert\NotNull(message: 'Discounts are required')]
        #[Assert\Count(min: 1, minMessage: 'At least one discount is required')]
        #[Assert\Valid]
        public array $discounts,
        #[Assert\All([
            new Assert\Type('string'),
        ])]
        public array $selectedProducts,
    ) {
    }
}
