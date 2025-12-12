<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Interface\API\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Product code is required')]
        #[Assert\Type('string')]
        public string $code,
        #[Assert\NotNull(message: 'Quantity is required')]
        #[Assert\Type('integer')]
        #[Assert\Positive(message: 'Quantity must be positive')]
        public int $quantity,
    ) {
    }
}
