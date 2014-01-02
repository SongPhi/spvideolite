<?php

/**
* 
*/
class SPVIDEO_PRO_Selfservice extends SPVIDEO_CLASS_AbstractProcessor
{
  protected function init() {
    $autoloader = OW::getAutoloader();
    $autoloader->addPackagePointer(
      'SPVIDEO_PRO_SELFSERVICE_CMP',
      $this->getClassPath() . DS . 'components'
    );
    $autoloader->addPackagePointer(
      'SPVIDEO_PRO_SELFSERVICE_CLASS',
      $this->getClassPath() . DS . 'classes'
    );
  }

  public function add() {
    $service = SPVIDEO_BOL_Service::getInstance();
    $uploadToken = md5(OW::getUser()->getEmail().'/'.microtime());
    $this->ctrl->assign('token',$uploadToken);

    OW::getDocument()->addScript( $service->getJsUrl('fileupload/vendor/jquery.ui.widget') );
    OW::getDocument()->addScript( $service->getJsUrl('fileupload/jquery.iframe-transport') );
    OW::getDocument()->addScript( $service->getJsUrl('fileupload/jquery.fileupload') );

    $this->ctrl->assign('uploadDest', OW::getRouter()->getBaseUrl().'spvideo/proxy/selfservice/upload');

    $infoForm = new SPVIDEO_PRO_SELFSERVICE_CMP_Infoform();
    $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/selfservice/postupload' );
    $infoForm->setValues(array(
      'token' => $uploadToken
    ));
    $this->ctrl->addForm($infoForm);
    $view = 'add.html';
  }
}