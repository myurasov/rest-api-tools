<?php

/**
 * Success API result
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Results;

class SuccessResult extends AbstractResult
{
  public function __construct($error = 0, $message = 'OK')
  {
    $this->setSuccess(true);
    parent::__construct($error, $message);
  }
}