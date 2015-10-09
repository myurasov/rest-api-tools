<?php

namespace MYurasov\RESTAPITools\Security\Tokens;

class TokenExpiredException extends \RuntimeException
{
  public function __construct($message = 'Token has expired', $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}
