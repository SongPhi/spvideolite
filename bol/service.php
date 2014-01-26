<?php

/**
* 
*/
class SPVIDEO_BOL_Service
{
  const PLUGIN_NAME = 'spvideo';
  protected static $classInstance = null;
  protected static $processors = null;

  public static function getInstance() {
    if ( null === self::$classInstance ) {
      self::$classInstance = new self();
    }
    return self::$classInstance;
  }

  /**
   * ============= UTILITIES FUNCTIONS =============
   */
  public static function getPlugin() {
    return OW::getPluginManager()->getPlugin(self::PLUGIN_NAME);
  }

  public static function getJsUrl($filename) {
    return self::getPlugin()->getStaticJsUrl() . $filename . '.js';
  }

  public static function getCssUrl($filename) {
    return self::getPlugin()->getStaticCssUrl() . $filename . '.css';
  }

  public static function getRoute() {
    $route = OW::getRequestHandler()->getHandlerAttributes();
    if (is_array($route)) {
      return $route;
    }
    return false;
  }

  /**
   *
   */
  public static function isRoute( $controller, $action = null ) {
    $route = self::getRoute();

    if ( !$route )
      return false;

    if ( $route["controller"] == $controller ) {
      if ( $route["action"] == $action || !$action ) {
        return true;
      }
    }

    return false;
  }

  /**
   * ============= PROCESSORS FUNCTIONS =============
   */

  public static function registerProcessor($name) {
    if (null == self::$processors) {
      self::$processors = array();
    }

    self::$processors[$name] = array(
      'className' => ('SPVIDEO_PRO_'.$name),
      'instance' => null
    );
  }

  public static function getProcessorInstance($name) {
    if (is_array(self::$processors[$name])) {
      $className = 'SPVIDEO_PRO_'.$name;
      if (empty(self::$processors[$name]['instance'])) {
        self::$processors[$name]['instance'] = $className::getInstance($className);
      }
      $processorInstance = self::$processors[$name]['instance'];
      return $processorInstance;
    } else {
      throw new Exception("Error Processing Request", 1);
    }
  }

  public static function callProcessorFunction($name,$function,&$controller) {
    $processorInstance = self::getProcessorInstance($name);
    $processorInstance->setController($controller);
    return $processorInstance->$function();
  }
}