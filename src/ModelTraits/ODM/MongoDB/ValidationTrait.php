<?php

/**
 * Support for validation for MongoDB ODM
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\ModelTraits\ODM\MongoDB;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

trait ValidationTrait
{
  use \MYurasov\RESTAPITools\ModelTraits\ValidationTrait {
    validate as private _validate;
  }

  /**
   * @ODM\PreFlush()
   */
  public function validate()
  {
    $this->_validate();
  }
}
