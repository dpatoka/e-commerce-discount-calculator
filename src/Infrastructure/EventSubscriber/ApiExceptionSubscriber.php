<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use App\SharedKernel\Domain\Exception\NotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            return;
        }

        $response = match (true) {
            $exception instanceof UnprocessableEntityHttpException => $this->handleValidationException($exception),
            $exception instanceof HandlerFailedException => $this->handleMessengerException($exception),
            $exception instanceof NotFoundException => $this->notFound($exception->getMessage()),
            default => $this->serverError(),
        };

        $event->setResponse($response);
    }

    private function handleValidationException(UnprocessableEntityHttpException $exception): JsonResponse
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof ValidationFailedException) {
            $errors = [];
            foreach ($previous->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(
                ['errors' => $errors],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return new JsonResponse(
            ['errors' => ['request' => $exception->getMessage()]],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    private function handleMessengerException(HandlerFailedException $exception): JsonResponse
    {
        $nested = $exception->getWrappedExceptions()[0] ?? $exception->getPrevious();

        return match (true) {
            $nested instanceof NotFoundException => $this->notFound($nested->getMessage()),
            default => $this->serverError(),
        };
    }

    private function notFound(string $message): JsonResponse
    {
        return new JsonResponse(
            ['errors' => ['request' => $message]],
            Response::HTTP_NOT_FOUND
        );
    }

    private function serverError(): JsonResponse
    {
        return new JsonResponse(
            ['errors' => ['request' => 'Server error']],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
