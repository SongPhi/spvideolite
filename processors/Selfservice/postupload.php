<?php

require dirname(__FILE__).DS.'components'.DS.'infoform.php';
$this->setPageHeading('Video Upload');
$infoForm = new SPVIDEO_PRO_Selfservice_CMP_Infoform(true);
$infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/selfservice/saveclip' );
$infoForm->setValues($_POST);
$this->addForm($infoForm);