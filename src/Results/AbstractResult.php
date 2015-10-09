<?php

/**
 * Abstract API result
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Results;

abstract class AbstractResult
{
  public $error = 0;
  public $message = 0;
  public $success = false;

  /**
   * @param int $error
   * @param string $message
   */
  public function __construct($error = 0, $message = '')
  {
    $this->error = $error;
    $this->message = $message;
  }

  public function toArray()
  {
    return get_object_vars($this);
  }

  public function getError()
  {
    return $this->error;
  }

  /**
   * @param int $error
   * @return ErrorResult
   */
  public function setError($error)
  {
    $this->error = $error;
    return $this;
  }

  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @param int|string $message
   * @return ErrorResult
   */
  public function setMessage($message)
  {
    $this->message = $message;
    return $this;
  }

  public function getSuccess()
  {
    return $this->success;
  }

  /**
   * @param boolean $success
   * @return ErrorResult
   */
  public function setSuccess($success)
  {
    $this->success = $success;
    return $this;
  }
}