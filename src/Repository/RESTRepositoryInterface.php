<?php

/**
 * REST Repository interface
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

interface RESTRepositoryInterface extends ObjectRepository
{
  /**
   * @param $skip int
   * @return $this
   */
  public function setSkip($skip);

  /**
   * @param $limit int
   * @return $this
   */
  public function setLimit($limit);

  /**
   * Get all resources
   * Limit/skip values are used
   *
   * @return array
   */
  public function searchAll();

  /**
   * Search by list od IDs
   *
   * @param array $ids
   * @return array
   */
  public function searchByIds($ids = array());
}