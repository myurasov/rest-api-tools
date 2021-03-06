<?php

/**
 * REST Controller for Doctrine ORM
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Controller\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use MYurasov\RESTAPITools\Controller\AbstractRESTController;
use MYurasov\RESTAPITools\ORM\RESTRepository;

class RESTController extends AbstractRESTController
{
  /**
   * @var EntityManager
   */
  protected $em;

  /**
   * @var RESTRepository
   */
  protected $repository;

  public function setOm(ObjectManager $om)
  {
    $this->em = $this->om = $om;
    return $this;
  }

  protected function deleteAll()
  {
    $this->repository->createQueryBuilder('resource')
      ->delete()
      ->getQuery()
      ->execute();
  }
}