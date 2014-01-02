<?php

/**
* 
*/
abstract class SPVIDEO_CLASS_AbstractProcessor
{
  protected static $classInstance = null;
  protected $ctrl = null;

  public static function getInstance($className) {
    if ( null == self::$classInstance ) {
      self::$classInstance = new $className();
    }

    return self::$classInstance;
  }

  protected function __construct() {
    $this->init();
  }

  /**
   * ====================== Abstract members ======================
   */

  abstract protected function init();
  abstract public function add();

  /**
   * ====================== Utilities ======================
   */
  public function setController(&$ctrl) {
    $this->ctrl = $ctrl;
  }

  public function getClassPath() {
    return SPVIDEO_DIR_PROCESSORS . DS .$this->getName();
  }

  public function getName() {
    $className = get_class($this);
    return substr($className, 0-strlen($className)+strlen('SPVIDEO_PRO_'));
  }

  public function getViewPath() {
    return $this->getClassPath() . DS . 'views';
  }

  public function setTemplate($filename) {
    $this->ctrl->setTemplate($this->getViewPath() . DS . $filename);
  }

  public function addScript($script) {
    OW::getDocument()->addScript($script);
  }
  
}