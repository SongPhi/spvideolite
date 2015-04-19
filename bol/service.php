<?php

/**
* 
*/
class SPVIDEOLITE_BOL_Service
{
  const PLUGIN_NAME = 'spvideolite';
  const PLUGIN_VER = 'v1.2.0';
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

  public static function isPluginInstalled( $key ) {
    try {
      OW::getPluginManager()->getPlugin($key);
      return true;
    } catch (Exception $e) {  }
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

}
