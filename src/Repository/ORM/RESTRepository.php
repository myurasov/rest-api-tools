<?php

/**
 * ORM REST Repository
 * @copyright 2014 Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\ORM;

use Doctrine\ORM\EntityRepository;
use MYurasov\RESTAPITools\Repository\RESTRepositoryInterface;

class RESTRepository extends EntityRepository implements RESTRepositoryInterface
{
  protected $skip = 0;
  protected $limit = 100;

  public function searchAll()
  {
    return $this->createQueryBuilder('resources')
      ->setFirstResult($this->skip)
      ->setMaxResults($this->limit)
      ->getQuery()
      ->getResult();
  }

  public function searchByIds($ids = array())
  {
    return $this->findBy(array('id' => $ids), null, $this->limit, $this->skip);
  }

  public function setLimit($limit)
  {
    $this->limit = $limit;
  }

  public function setSkip($skip)
  {
    $this->skip = $skip;
  }
}