<?php

/**
 * CORS headers provider
 * todo: add configurable options
 */

namespace MYurasov\RESTAPITools\Silex;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CORSProvider implements ServiceProviderInterface
{
  public function register(Application $app)
  {

    $app->after(
      function (Request $request, Response $response) use ($app) {
        $response->headers->set('Access-Control-Allow-Origin', $request->headers->get('origin', '*'));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Credentials', 'Bearer');
        $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With, X-HTTP-Method-Override, Content-Type, Accept');
      }
    );

    $app->before(function (Request $request) use ($app) {
      if ($request->getMethod() === 'OPTIONS') {
        return new Response('', 200);
      }
    });

  }

  public function boot(Application $app)
  {
  }
}