<?php

namespace MYurasov\RESTAPITools\Controller\Traits;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ParametersCheckTrait
{
  /**
   * Check if all required parameters are present
   *
   * @param ParameterBag $parameterBag
   * @param array        $parameters
   * @param bool         $deepSearch Deep search
   */
  protected function requireParameters(ParameterBag $parameterBag, array $parameters = [], $deepSearch = false)
  {
    foreach ($parameters as $parameter) {

      if (!$parameterBag->has($parameter)) {

        // search deep in the array
        // some.value looked in $input['some.value'] and $input['some']['value']
        if ($deepSearch && strstr($parameter, '.') !== -1) {

          $input = $parameterBag->all();
          $parts = explode('.', $parameter);

          foreach ($parts as $part) {

            if (!isset($input[$part])) {
              throw new BadRequestHttpException('Missing required ' . $parameter . ' parameter');
            }

            $input = $input[$part];
          }
        } else {
          throw new BadRequestHttpException('Missing required ' . $parameter . ' parameter');
        }
      }
    }
  }
}