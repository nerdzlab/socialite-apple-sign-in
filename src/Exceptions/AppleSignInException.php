<?php

namespace Nerdzlab\SocialiteAppleSignIn\Exceptions;

use Exception;
use Throwable;

class AppleSignInException extends Exception
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
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
