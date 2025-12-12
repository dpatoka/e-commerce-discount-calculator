<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Interface\API;

use App\Modules\Discounts\Interface\API\Mapper\CalculateDiscountsRequestMapper;
use App\Modules\Discounts\Interface\API\Request\CalculateDiscountsRequest;
use App\Modules\Discounts\Interface\API\Response\DiscountPriceResponse;
use App\SharedKernel\Infrastructure\Bus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class GetDiscountsController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CalculateDiscountsRequestMapper $requestMapper,
    ) {
    }

    #[Route('api/discounts/calculate', name: 'app_discounts_calculate', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] CalculateDiscountsRequest $request,
    ): JsonResponse {
        $query = $this->requestMapper->toQuery($request);
        $price = $this->queryBus->query($query);

        return $this->json(DiscountPriceResponse::fromPrice($price), Response::HTTP_OK);
    }
}
