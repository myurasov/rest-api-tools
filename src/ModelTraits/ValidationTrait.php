<?php

/**
 * Support for validation
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;

trait ValidationTrait
{
  public function validate()
  {
    $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();

    /** @var ConstraintViolationList $violations */
    $violations = $validator->validate($this);
    $message    = [];

    if (count($violations) > 0) {

      /** @var ConstraintViolation $violation */
      foreach ($violations as $violation) {
        $message[] = $violation->getPropertyPath() . ": " . $violation->getMessage();
      }

      throw new ValidatorException("Validation failed. " . join(" ", $message));
    }

    return true;
  }
}
