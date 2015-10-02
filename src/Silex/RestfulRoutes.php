<?php

/**
 * Registers routes to RESTful actions on a controller
 *
 * @author Mikhail Yurasov <me@yurasov.me>
 */

namespace MYurasov\RESTAPITools\Silex;

use Silex\ControllerCollection;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestfulRoutes
{
  /**
   * Registers routes to RESTful actions on a controller defined as a service
   *
   * @param $app Application|ControllerCollection
   * @param $service string Controller service
   * @param $path string
   */
  public static function register($app, $service, $path)
  {
    static::registerActions($app, $service, $path);
    static::registerRESTfulActions($app, $service, $path);
  }

  public static function registerRESTfulActions(Application $app, $service, $path)
  {
    // collection
    $app->get($path, $service . ':getCollectionAction');
    $app->put($path, $service . ':replaceCollectionAction');
    $app->post($path, $service . ':createResourceAction');
    $app->delete($path, $service . ':deleteCollectionAction');

    // resource
    $app->get($path . '/{id}', $service . ':getResourceAction');
    $app->put($path . '/{id}', $service . ':updateOrCreateResourceAction');
    $app->match($path . '/{id}', $service . ':updateResourceAction')->method('PATCH');
    $app->delete($path . '/{id}', $service . ':deleteResourceAction');
  }

  public static function registerActions($app, $service, $path)
  {

    // action handler
    $actionHandler = function(Request $request, $action) use ($app, $service) {

      $action = $action . 'Action';

      if (is_callable(array($app[$service], $action))) {

        // call action
        return call_user_func(array($app[$service], $action), $request, $app);

      } else {
        throw new NotFoundHttpException("Action $service:$action not found");
      }

    };

    // actions
    $app->match($path . '/{action}.action',  $actionHandler);
    $app->match($path . '/{id}/{action}.action',  $actionHandler);
  }
} 