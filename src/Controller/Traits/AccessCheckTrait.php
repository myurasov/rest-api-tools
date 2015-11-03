<?php

namespace MYurasov\RESTAPITools\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait AccessCheckTrait
{
  /**
   * Check if user can access this resource
   */
  protected function checkAccess(Request $request, $allowedUserId = null)
  {
    // auth data, populated by autorization handler
    $auth = $request->attributes->get('auth');

    if (isset($auth->isAdmin) && $auth->isAdmin) {
      return true;
    }

    if (!is_null($allowedUserId) && $auth->userId === $allowedUserId) {
      return true;
    }

    throw new AccessDeniedHttpException('Access to this resource is not allowed');
  }
}