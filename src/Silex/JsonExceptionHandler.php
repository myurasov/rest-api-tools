<?php

/**
 * JSON Ecxeption handler
 * Handles Exceptions and converts them to JSON response
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Silex;

use MYurasov\RESTAPITools\Results\ErrorResult;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonExceptionHandler
{
  /**
   * @param Application $app
   * @param bool $force
   */
  public static function register(Application $app, $force = false)
  {
    $app->error(function (\Exception $e) use ($app, $force) {

      /** @var Request $request */ $request =  $app['request'];

      if ($force || $request->headers->has('accept')
          && 0 === strpos($request->headers->get('accept'),
                          'application/json')) {

        $response = new JsonResponse();

        if ($e instanceof HttpException) {
          $response->setStatusCode($e->getStatusCode());
          $response->headers->add($e->getHeaders());
        } else {
          $response->setStatusCode(500);
        }

        // error code

        $errorCode = $e->getCode();

        if (!$errorCode) {
          if (method_exists($e, 'getStatusCode')) {
            $errorCode = 'HTTP/' . $e->getStatusCode();
          }
        }

        $response->setData(new ErrorResult($errorCode, $e->getMessage()));

        return $response;
      }

      return null;

    });
  }
}