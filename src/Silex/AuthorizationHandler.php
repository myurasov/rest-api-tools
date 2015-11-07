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
  private function ignorePath($path, $method)
  {
    foreach ($this->app['authorization_handler.ignore_paths'] as $i => $rule)
    {
      $methods = substr($rule, 0, strpos($rule, ' '));
      $regex = substr($rule, strlen($methods) + 1);
      $methods = explode(',', $methods);

      if (in_array($method, $methods, true) && preg_match($regex, $path)) {
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

        $path = $request->getPathInfo();
        $method = $request->getMethod();

        if (!$this->ignorePath($path, $method)) {

          // authorization token
          $token = '';

          // look for Authorization header

          if (function_exists('apache_request_headers')) {

            // Apache

            // bearer Authorization header is stripped away by Symfony, so read it from the web server
            $headers = apache_request_headers();

            if (isset($headers['authorization'])) {
              $token = $headers['authorization'];
            } else if (isset($headers['Authorization'])) {
              $token = $headers['Authorization'];
            }

          } else {
            $token = $request->headers->get('authorization', '');
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

            // restore Authorization header, wiped out by Symfony (why? dunno)
            $request->headers->set('authorization', 'Bearer ' . $token);

          } else {
            // HTTP 401, force login prompt
            throw new UnauthorizedHttpException('Bearer', 'Authorization is required');
          }
        }
      }
    );
  }
}