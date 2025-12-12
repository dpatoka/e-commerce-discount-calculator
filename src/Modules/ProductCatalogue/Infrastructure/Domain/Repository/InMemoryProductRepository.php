<?php

declare(strict_types=1);

namespace App\Modules\ProductCatalogue\Infrastructure\Domain\Repository;

use App\Modules\ProductCatalogue\Domain\Model\Price;
use App\Modules\ProductCatalogue\Domain\Repository\ProductRepositoryInterface;
use App\SharedKernel\Domain\PriceCollection;

/**
 * Implementation just to showcase the idea.
 * The real one will use other storage like DB
 */
class InMemoryProductRepository implements ProductRepositoryInterface
{
    /** @var array<string, array{code: string, price: int, currency: string}> */
    private array $products = [
        'PROD1' => ['code' => 'PROD1', 'price' => 9999, 'currency' => 'PLN'],
        'PROD2' => ['code' => 'PROD2', 'price' => 4999, 'currency' => 'PLN'],
        'PROD3' => ['code' => 'PROD3', 'price' => 14999, 'currency' => 'PLN'],
        'PROD4' => ['code' => 'PROD4', 'price' => 2499, 'currency' => 'PLN'],
        'PROD5' => ['code' => 'PROD5', 'price' => 34999, 'currency' => 'PLN'],
    ];

    public function getPrices(string ...$codes): PriceCollection
    {
        $prices = [];

        foreach ($codes as $code) {
            if (isset($this->products[$code])) {
                $product = $this->products[$code];
                $prices[$code] = new Price($product['price'], $product['currency']);
            }
        }

        return new PriceCollection($prices);
    }
}
