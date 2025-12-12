<?php

declare(strict_types=1);

namespace App\Modules\Discounts\Interface\API;

use App\Modules\Discounts\Application\Query\GetDiscountsForProductsQuery\GetDiscountsForProductsQueryHandler;
use App\Modules\Discounts\Interface\API\Mapper\CalculateDiscountsRequestMapper;
use App\Modules\Discounts\Interface\API\Request\CalculateDiscountsRequest;
use App\Modules\Discounts\Interface\API\Response\DiscountPriceResponse;
use App\SharedKernel\Domain\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final class GetDiscountsController extends AbstractController
{
    public function __construct(
        private readonly GetDiscountsForProductsQueryHandler $queryHandler,
        private readonly CalculateDiscountsRequestMapper $requestMapper,
    ) {
    }

    #[Route('/discounts/calculate', name: 'app_discounts_calculate', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] CalculateDiscountsRequest $request,
    ): JsonResponse {
        try {
            $query = $this->requestMapper->toQuery($request);
            $price = ($this->queryHandler)($query);
            $response = DiscountPriceResponse::fromPrice($price);

            return $this->json($response, Response::HTTP_OK);
        } catch (NotFoundException $exception) {
            return $this->json(
                ['errors' => ['request' => $exception->getMessage()]],
                Response::HTTP_NOT_FOUND
            );
        } catch (Throwable $exception) {
            return $this->json(
                ['errors' => ['request' => 'Server error']],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
