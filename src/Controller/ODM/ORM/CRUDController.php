<?php

/**
 * Controller that implements all CRUD operations
 * Useful for quick prototyping
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Controller\ORM;

use Symfony\Component\HttpFoundation\Request;

class CRUDController extends RESTController
{
  public function getResourceAction(Request $request)
  {
    return parent::getResource($request);
  }

  public function updateOrCreateResourceAction(Request $request)
  {
    return parent::updateOrCreateResource($request);
  }

  public function updateResourceAction(Request $request)
  {
    return parent::updateResource($request);
  }

  public function deleteResourceAction(Request $request)
  {
    return parent::deleteResource($request);
  }

  public function getCollectionAction(Request $request)
  {
    return parent::getCollection($request);
  }

  public function replaceCollectionAction(Request $request)
  {
    return parent::replaceCollection($request);
  }

  public function createResourceAction(Request $request)
  {
    parent::createResource($request);
  }

  public function deleteCollectionAction(Request $request)
  {
    return parent::deleteCollection($request);
  }
}