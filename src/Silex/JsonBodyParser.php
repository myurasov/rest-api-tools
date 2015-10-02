<?php

/**
 * Json body parser
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Silex;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class JsonBodyParser
{
  /**
   * @param Application $app
   */
  public static function register(Application $app)
  {
    $app->before(function (Request $request) {
      if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
      }
    });
  }
}