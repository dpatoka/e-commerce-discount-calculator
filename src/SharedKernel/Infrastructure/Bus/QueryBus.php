<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Bus;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class QueryBus
{
    public function __construct(
        private readonly MessageBusInterface $queryBus,
    ) {
    }

    /**
     * @template T
     * @param QueryInterface<T> $query
     * @return T
     */
    public function query(QueryInterface $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);

        return $envelope->last(HandledStamp::class)?->getResult();
    }
}
