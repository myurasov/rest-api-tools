<?php

/**
 * Support for createdAt, updatedAt
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\ModelTraits\ODM\MongoDB;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as Serializer;

trait TimestampTrait
{
  /**
   * @ODM\Date
   * @Serializer\Expose
   * @var \DateTime
   */
  protected $createdAt;

  /**
   * @ODM\Date
   * @Serializer\Expose
   * @var \DateTime
   */
  protected $updatedAt;

  /**
   * @ODM\PreFlush
   */
  public function updateTimestamp()
  {
    if ($this->createdAt === null) {
      $this->createdAt = new \DateTime();
    }
    $this->updatedAt = new \DateTime();
  }
  //
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
  }
}