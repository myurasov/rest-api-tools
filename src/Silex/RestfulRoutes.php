<?php

/**
 * Registers routes to RESTful actions on a controller
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Silex;

use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestfulRoutes
{
  /**
   * Registers routes to RESTful actions on a controller defined as a service
   *
   * @param $app Application|ControllerCollection
   * @param $serviceName string Controller service
   * @param $basePath string
   */
  public static function register($app, $serviceName, $basePath)
  {
    static::registerActions($app, $serviceName, $basePath);
    static::registerRESTfulActions($app, $serviceName, $basePath);
  }

  private static function normalizePath($path)
  {
    if (substr($path, -1) === '/') {
      return substr($path, 0, strlen($path) - 1);
    } else {
      return $path;
    }
  }

  /**
   * Register RESTful actions
   *
   * @param $app Application|ControllerCollection
   * @param $serviceName string Controller service
   * @param $basePath string
   */
  public static function registerRESTfulActions($app, $serviceName, $basePath)
  {
    // remove trailing slash
    $basePath = self::normalizePath($basePath);

    // collection
    $app->get($basePath, $serviceName . ':getCollectionAction');
    $app->put($basePath, $serviceName . ':replaceCollectionAction');
    $app->post($basePath, $serviceName . ':createResourceAction');
    $app->delete($basePath, $serviceName . ':deleteCollectionAction');

    // resource
    $app->get($basePath . '/{id}', $serviceName . ':getResourceAction');
    $app->put($basePath . '/{id}', $serviceName . ':updateOrCreateResourceAction');
    $app->match($basePath . '/{id}', $serviceName . ':updateResourceAction')->method('PATCH');
    $app->delete($basePath . '/{id}', $serviceName . ':deleteResourceAction');
  }

  /**
   * Register actions
   *
   * @param $app Application|ControllerCollection
   * @param $serviceName string Controller service
   * @param $basePath string
   */
  public static function registerActions($app, $serviceName, $basePath)
  {
    // remove trailing slash
    $basePath = self::normalizePath($basePath);

    // action handler
    $actionHandler = function(Request $request, $action) use ($app, $serviceName) {

      $action .= 'Action';

      if (is_callable(array($app[$serviceName], $action))) {

        // call action
        return call_user_func(array($app[$serviceName], $action), $request, $app);

      } else {
        throw new NotFoundHttpException("Action $serviceName:$action not found");
      }

    };

    // actions
    $app->match($basePath . '/{action}.action',  $actionHandler);
    $app->match($basePath . '/{id}/{action}.action',  $actionHandler);
  }
} 