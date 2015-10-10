<?php

namespace MYurasov\RESTAPITools\Silex;

use MYurasov\RESTAPITools\Security\Tokens\TokensService;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthorizationHandler implements ServiceProviderInterface
{
  /**
   * @var Application
   */
  private $app;

  public function register(Application $app, array $config = [])
  {
    $this->app = $app;
    $this->applyOptions($config);
    $this->addMiddleware();
  }

  public function boot(Application $app)
  {
  }

  private function applyOptions(array $config)
  {
    $defauls = [
      'authorization_handler.ignore_paths' => [],
      'authorization_handler.jwt_subject' => 'auth'
    ];

    foreach ($defauls as $k /** @var string $k */ => $defaultValue) {
      // apply default value
      $this->app[$k] = isset($this->app[$k]) ? $this->app[$k]: $defaultValue;

      // apply config value
      $this->app[$k] = isset($config[$k]) ? $config[$k] : $this->app[$k];
    }
  }

  /**
   * Check if the path mathes within ignore list
   *
   * @param string $path
   * @return bool
   */
  private function ignorePath($path)
  {
    foreach ($this->app['authorization_handler.ignore_paths'] as $i => $regex)
    {
      if (preg_match($regex, $path)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Add middleware
   */
  private function addMiddleware()
  {
    $this->app->before(
      function (Request $request) {

        // path
        $path = $request->getPathInfo();

        if (!$this->ignorePath($path)) {

          // Authorization token
          $token = '';

          // look for Authorization header

          // bearer Authorization header is stripped away by Symfony, so read it from the web server
          $headers = getallheaders();

          foreach ($headers as $name => $value) {
            if (strcasecmp($name, 'authorization') === 0) {
              $token = $value;
              break;
            }
          }

          // look elsewhere in request fields

          if (empty($token)) {
            $token = $request->get('authorization');
          }

          if (!empty($token)) {

            // remove "Bearer " prefix
            if (substr($token, 0, 7) === 'Bearer ') {
              $token = substr($token, 7);
            }

            /** @var TokensService $tokenService */
            $tokenService = $this->app['authorization_handler.tokens_service'];

            // decode JWT token, exception is thrown on invalid one
            $tokenData = $tokenService->decodeToken($token, $this->app['authorization_handler.jwt_subject']);

            // add token data to request
            $request->attributes->set('auth', $tokenData);

          } else {
            // HTTP 401, force login prompt
            throw new UnauthorizedHttpException('Bearer', 'Authorization is required');
          }
        }
      }
    );
  }
}