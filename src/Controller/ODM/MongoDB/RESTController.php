<?php

/**
 * REST Controller for Doctrine MongoDB ODM
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Controller\ODM\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use MYurasov\RESTAPITools\Controller\AbstractRESTController;
use MYurasov\RESTAPITools\Repository\ODM\MongoDB\RESTRepository;

class RESTController extends AbstractRESTController
{
  /**
   * @var DocumentManager
   */
  protected $dm;

  /**
   * @var RESTRepository
   */
  protected $repository;

  public function setOm(ObjectManager $om)
  {
    $this->dm = $this->om = $om;
  }

  protected function deleteAll()
  {
    $this->repository->createQueryBuilder()
      ->remove()
      ->getQuery()
      ->execute();
  }
}