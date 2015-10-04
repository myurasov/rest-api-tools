<?php

/**
 * REST API controller
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use MYurasov\RESTAPITools\Repository\RESTRepositoryInterface;
use MYurasov\RESTAPITools\SerializedResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractRESTController extends RESTControllerActions
{
  /**
   * @var ObjectManager
   */
  protected $om;

  /**
   * @var RESTRepositoryInterface
   */
  protected $repository;

  /**
   * Method to get last modification date
   * @var string
   */
  protected $modificationDateMethodName = 'getUpdatedAt';

  /**
   * @var SerializedResponse
   */
  protected $response;

  /**
   * Default collection output limit
   * @var int
   */
  protected $defaultLimit = 10;

  /**
   * Maximum collection output limit
   * @var int
   */
  protected $maxLimit = 100;

  /**
   * @var PropertyAccessor
   */
  protected $propertyAccessor;

  //<editor-fold desc="actions">

  /**
   * @inheritdoc
   */
  public function getResource(Request $request)
  {
    // load resource
    $resource = $this->load($request->attributes->get('id'), true /* required */);

    // set last-modified header
    if (method_exists($resource, $this->modificationDateMethodName)) {
      $this->response->setLastModified(call_user_func(array($resource, $this->modificationDateMethodName)));
    }

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  public function createResource(Request $request)
  {
    // create new resource
    $resource = $this->create();
    $this->om->persist($resource);

    // update with poseted data
    $this->update($resource, $request->request->all());

    // save
    $this->om->flush($resource);

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  public function updateOrCreateResource(Request $request)
  {
    // load existing resource
    $resource = $this->load($request->attributes->get('id'), false /* not required */);

    if (!$resource) {
      // create new if not found
      $resource = $this->create();
      $this->om->persist($resource);
    }

    // update with request
    $this->update($resource, $request->request->all());

    // save
    $this->om->flush($resource);

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  public function updateResource(Request $request)
  {
    // load existing resource
    $resource = $this->load($request->attributes->get('id'), true /* required */);

    // update with request
    $this->update($resource, $request->request->all());

    // save
    $this->om->flush($resource);

    // return resource
    $this->response->setData($resource);
    return $this->response;
  }

  /**
   * Get collection
   * Limit/skip are set on repository
   *
   * ?limit
   * ?skip
   *
   * @param Request $request
   * @return SerializedResponse
   */
  public function getCollection(Request $request)
  {
    $this->setLimits($request);
    $this->response->setData($this->search($request));
    return $this->response;
  }

  public function deleteResource(Request $request)
  {
    // load existing resource
    $resource = $this->load($request->attributes->get('id'), true /* required */);

    // remove
    $this->om->remove($resource);

    // save
    $this->om->flush($resource);

    //

    $this->response->setData(array(
        'message' => 'ok'
      ));

    return $this->response;
  }

  public function replaceCollection(Request $request)
  {
    // insert new resources

    $collectionData = $request->request->all();

    if (is_array($collectionData)) {
      foreach ($collectionData as $resourceData) {
        // create
        $resource = $this->create();
        $this->om->persist($resource);

        // update
        $this->update($resource, $resourceData);
      }
    }

    // delete all
    $this->deleteAll();

    // save new
    $this->om->flush();

    //

    $this->response->setData(array(
        'message' => 'ok'
      ));

    return $this->response;
  }

  public function deleteCollection(Request $request)
  {
    $this->deleteAll();

    //

    $this->response->setData(array(
        'message' => 'ok'
      ));

    return $this->response;
  }

  //</editor-fold>

  /**
   * Delete all items in collection
   * Changes saved to database immideately
   */
  abstract protected function deleteAll();

  /**
   * Create new instance of the resource
   *
   * @return object
   */
  protected function create()
  {
    $className = $this->getRepository()->getClassName();
    return new $className;
  }

  /**
   * Load resource
   *
   * @param $id
   * @param bool $required
   * @return mixed
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  protected function load($id, $required = true)
  {
    $resource = $this->getRepository()->find($id);

    if ($required && is_null($resource)) {
      throw new NotFoundHttpException();
    }

    return $resource;
  }

  /**
   * Sets skip/limit on repository from query parameters
   * @param Request $request
   */
  protected function setLimits(Request $request)
  {
    $limit = min($this->maxLimit, $request->query->getInt('limit', $this->defaultLimit));
    $skip = min(0, $request->query->getInt('skip', 0));

    $this->repository->setLimit($limit);
    $this->repository->setSkip($skip);
  }

  /**
   * Search resources
   *
   * @param Request $request
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   * @return array
   */
  protected function search(Request $request)
  {
    if ($request->query->has('id')) {

      $ids = $request->query->get('id');

      if (!is_array($ids)) {
        throw new BadRequestHttpException();
      }

      return $this->repository->searchByIds($ids);
    }

    return $this->repository->searchAll();
  }

  /**
   * Update resource from array
   *
   * @param $resource object
   * @param $input array
   */
  protected function update(&$resource, array $input)
  {
    if (is_array($input)) {
      $this->walkInputArray($input, function ($path, $value) use (&$resource) {
        $this->updateField($resource, $path, $value);
      });
    }
  }

  /**
   * Walks through input array
   *
   * @param        $array
   * @param        $callback callable function($path, $value)
   * @param null   $iterator
   * @param string $prefix
   */
  private function walkInputArray($array, $callback, $iterator = null, $prefix = '')
  {
    if (!$iterator) {
      $iterator = new \RecursiveArrayIterator($array);
    }

    while ($iterator->valid()) {
      if ($iterator->hasChildren()) {
        $this->walkInputArray(null, $callback, $iterator->getChildren(), $prefix . '.' . $iterator->key());
      } else {
        call_user_func(
          $callback,
          ltrim($prefix . '.' . $iterator->key(), '.'),
          $iterator->current()
        );
      }

      $iterator->next();
    }
  }

  /**
   * Update field
   *
   * @param $resource object
   * @param $path string
   * @param $value
   */
  protected function updateField(&$resource, $path, $value)
  {
    // create property accessor
    if (!$this->propertyAccessor) {
      $this->propertyAccessor = new PropertyAccessor();
    }

    $this->propertyAccessor->setValue($resource, $path, $value);
  }

  //<editor-fold desc="accessors">

  public function getOm()
  {
    return $this->om;
  }

  public function setOm(ObjectManager $om)
  {
    $this->om = $om;
    return $this;
  }

  public function getRepository()
  {
    return $this->repository;
  }

  public function setRepository(RESTRepositoryInterface $repository)
  {
    $this->repository = $repository;
    return $this;
  }

  public function setResponse(SerializedResponse $response)
  {
    $this->response = $response;
    return $this;
  }

  public function getResponse()
  {
    return $this->response;
  }

  public function setModificationDateMethodName($modificationDateMethodName)
  {
    $this->modificationDateMethodName = $modificationDateMethodName;
    return $this;
  }

  public function getModificationDateMethodName()
  {
    return $this->modificationDateMethodName;
  }

  public function setMaxLimit($maxLimit)
  {
    $this->maxLimit = $maxLimit;
    return $this;
  }

  public function getMaxLimit()
  {
    return $this->maxLimit;
  }

  public function setDefaultLimit($defaultLimit)
  {
    $this->defaultLimit = $defaultLimit;
    return $this;
  }

  public function getDefaultLimit()
  {
    return $this->defaultLimit;
  }

  //</editor-fold>
}