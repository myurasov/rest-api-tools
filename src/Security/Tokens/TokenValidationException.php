<?php

namespace MYurasov\RESTAPITools\Security\Tokens;

class TokenValidationException extends \RuntimeException
{
  public function __construct($message = 'Token validation has failed', $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}
