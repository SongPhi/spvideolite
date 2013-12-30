<?php

/**
* 
*/
class SPVIDEO_BOL_Service
{
  const PLUGIN_NAME = 'spvideo';
  protected static $classInstance = null;
  protected static $currentRoute = null;

  public static function getInstance() {
    if ( null === self::$classInstance ) {
      self::$classInstance = new self();
    }
    return self::$classInstance;
  }

  protected function __construct() {
    self::$currentRoute = self::getRoute();
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
    try {
      return OW::getRouter()->route();
    } catch ( Exception $e ) {
      return false;
    }
  }

  public static function isRoute( $controller, $action = null ) {
    $route = self::$currentRoute;

    if ( !$route )
      return false;

    if ( $route["controller"] == $controller ) {
      if ( empty($action) || $route["action"] == $action ) {
        return true;
      }
    }
    return false;
  }
}