<?php

/**
 * REST API actions
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Controller;

use MYurasov\RESTAPITools\SerializedResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class RESTControllerActions
{
  /**
   * GET /collection/id
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function getResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * PUT /collection/id
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function updateOrCreateResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * PATCH /collection/id
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function updateResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * DELETE /collection/id
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function deleteResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * GET /collection
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function getCollectionAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * PUT /collection
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function replaceCollectionAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * POST /collection
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function createResourceAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }

  /**
   * DELETE /collection
   *
   * @param Request $request
   * @return SerializedResponse
   * @throws HttpException
   */
  public function deleteCollectionAction(Request $request)
  {
    throw new HttpException(501, 'Not implemented');
  }
} 