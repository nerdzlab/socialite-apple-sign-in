<?php

namespace Nerdzlab\LaravelSocialiteAppleSignIn\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class AppleSignInException extends BadRequestHttpException
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, $previous, 0);
    }

    public static function invalidToken(Throwable $previous): self
    {
        return new self($previous->getMessage(), $previous);
    }

    public static function invalidKid(): self
    {
        return new self('Invalid public key id.');
    }

    public static function invalidIssuer(): self
    {
        return new self('Invalid token issuer.');
    }

    public static function invalidClientId(): self
    {
        return new self('Invalid client_id.');
    }
}
