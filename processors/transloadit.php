<?php

/**
* 
*/
class SPVIDEOLITE_PRO_Transloadit extends SPVIDEOLITE_CLASS_AbstractProcessor
{
  protected function init() {

  }

  public function add() {
    $this->addScript('//assets.transloadit.com/js/jquery.transloadit2-v2-latest.js');
    
    $this->ctrl->assign('uploadDest', OW::getRouter()->getBaseUrl().'spvideo/proxy/Transloadit/postupload');
  }

  public function postupload() {
    echo '<pre>';
    var_dump($_POST);
    var_dump(json_decode($_POST['transloadit']));
    echo '</pre>';
    die('postupload');
  }

  public function notify_callback() {
    die('called');
  }
}