<?php

namespace Nerdzlab\LaravelAppleSignIn\Exceptions;

use Illuminate\Http\Response;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class AuthFailedException extends RuntimeException implements HttpExceptionInterface
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function invalidToken(Throwable $previous = null): self
    {
        return new self('Invalid JWT token.', $previous);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
