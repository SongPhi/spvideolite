<?php
require dirname(__FILE__).DS.'components'.DS.'infoform.php';

$service = SPVIDEO_BOL_Service::getInstance();

OW::getDocument()->addScript( $service->getJsUrl('fileupload/vendor/jquery.ui.widget') );
OW::getDocument()->addScript( $service->getJsUrl('fileupload/jquery.iframe-transport') );
OW::getDocument()->addScript( $service->getJsUrl('fileupload/jquery.fileupload') );

$this->assign('uploadDest', OW::getRouter()->getBaseUrl().'spvideo/proxy/selfservice/upload');

$infoForm = new SelfServiceVideoInfoForm();
$infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/selfservice/postupload' );
$infoForm->setValues(array(
  'token' => $uploadToken
));
$this->addForm($infoForm);
$view = 'add.html';