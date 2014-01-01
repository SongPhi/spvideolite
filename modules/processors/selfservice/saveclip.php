<?php
if (!OW::getRequest()->isPost()) { 
  throw new Redirect404Exception();
}

require dirname(__FILE__).DS.'components'.DS.'infoform.php';

$infoForm = new SelfServiceVideoInfoForm(true);

if ( $infoForm->isValid($_POST) ) {
  $language = OW::getLanguage();
  $values = $infoForm->getValues();
  
  $ret = $infoForm->process();
  OW::getFeedback()->info($language->text('video', 'clip_added'));
  $this->redirect(OW::getRouter()->urlForRoute('view_clip', array('id' => $ret['id'])));
} else {
  OW::getFeedback()->error('Error while processing your information');
  $this->setPageHeading('Video Upload');
  $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/selfservice/saveclip' );
  $infoForm->setValues($_POST);
  $this->addForm($infoForm);
  $view = 'postupload.html';
}