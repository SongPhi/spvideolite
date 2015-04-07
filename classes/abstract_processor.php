<?php
/**
 * Copyright 2015 SongPhi
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
* 
*/
abstract class SPVIDEOLITE_CLASS_AbstractProcessor
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
    return SPVIDEOLITE_DIR_PROCESSORS . DS .$this->getName();
  }

  public function getName() {
    $className = get_class($this);
    return substr($className, 0-strlen($className)+strlen('SPVIDEOLITE_PRO_'));
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