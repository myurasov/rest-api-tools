<?php

namespace MYurasov\RESTAPITools\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

trait HttpMethodCheckTrait
{
  /**
   * Check HTTP method
   *
   * @param Request $request
   * @param string  $method
   */
  protected function requireMethod(Request $request, $method)
  {
    if ($request->getMethod() !== $method) {
      throw new MethodNotAllowedHttpException([$method]);
    }
  }
}