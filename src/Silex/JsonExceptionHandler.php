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

        $errorCode = $e->getCode();
        $response = new JsonResponse();

        if ($e instanceof HttpException) {
          $response->setStatusCode($e->getStatusCode());
          $response->headers->add($e->getHeaders());

          if (!$errorCode) {
            $errorCode = 'HTTP/' . $e->getStatusCode();
          }

        } else {
          $response->setStatusCode(500);
        }

        $response->setData(new ErrorResult($errorCode, $e->getMessage()));

        return $response;
      }

      return null;

    });
  }
}