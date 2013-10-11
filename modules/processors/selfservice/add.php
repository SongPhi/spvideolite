<?php
require dirname(__FILE__).DS.'components'.DS.'infoform.php';

$this->assign('uploadDest', OW::getRouter()->getBaseUrl().'/spvideo/proxy/selfservice/upload');

$infoForm = new SelfServiceVideoInfoForm();
$infoForm->setAction( OW::getRouter()->getBaseUrl().'/spvideo/proxy/selfservice/postupload' );
$infoForm->setValues(array(
  'token' => $uploadToken
));
$this->addForm($infoForm);
$view = 'add.html';