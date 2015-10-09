<?php

/**
 * Error API result
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Results;

class ErrorResult extends AbstractResult
{
  public function __construct($error = -1, $message = 'Error occured')
  {
    $this->setSuccess(false);
    parent::__construct($error, $message);
  }
}