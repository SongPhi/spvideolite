<?php

/**
* 
*/
class SPVIDEOLITE_PRO_Allmyvideos extends SPVIDEOLITE_CLASS_AbstractProcessor
{
  protected function init() {
    $autoloader = OW::getAutoloader();
    $autoloader->addPackagePointer(
      'SPVIDEOLITE_PRO_ALLMYVIDEOS_CMP',
      $this->getClassPath() . DS . 'components'
    );
    $autoloader->addPackagePointer(
      'SPVIDEOLITE_PRO_ALLMYVIDEOS_CLASS',
      $this->getClassPath() . DS . 'classes'
    );
  }

  public function add() {
    $service = SPVIDEOLITE_BOL_Service::getInstance();
    $uploadToken = md5(OW::getUser()->getEmail().'/'.microtime());
    $this->ctrl->assign('token',$uploadToken);

    OW::getDocument()->addScript( $service->getJsUrl('vendor/blueimp-file-upload/vendor/jquery.ui.widget') );
    OW::getDocument()->addScript( $service->getJsUrl('vendor/blueimp-file-upload/jquery.iframe-transport') );
    OW::getDocument()->addScript( $service->getJsUrl('vendor/blueimp-file-upload/jquery.fileupload') );

    $this->ctrl->assign('uploadDest', OW::getRouter()->getBaseUrl().'spvideo/proxy/Allmyvideos/upload');

    $infoForm = new SPVIDEOLITE_PRO_ALLMYVIDEOS_CMP_Infoform();
    $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/Allmyvideos/postupload' );
    $infoForm->setValues(array(
      'token' => $uploadToken
    ));
    $this->ctrl->addForm($infoForm);
  }

  public function upload() {
    //handle empty post
    if (!isset($_POST['token'])) 
      throw new Exception("Upload file limit exceed!");
      
    $uploadPath = SPVIDEOLITE_DIR_USERFILES . DS . 'allmyvideos' . DS . $_POST['token'] . DS;
    @mkdir($uploadPath, 0777);
    $upload_handler = new SPVIDEOLITE_PRO_ALLMYVIDEOS_CLASS_UploadHandler(array(
        'upload_dir' => $uploadPath,
        'accept_file_types' => '/\.(mp4|m4v|flv|f4v|ogv|ogg|webm|avi|mkv|mov)$/i',
        'param_name' => 'videoClip',
        'max_file_size' => '2000000'
    ));
  }

  public function callback() {
    // http://example.com/spvideo/proxy/Allmyvideos/callback
  }

  public function pending() {
    $this->setTemplate('pending.html');
    
    $tokenQuery = '#Allmyvideos/pending/([a-z0-9]{32})#i';
    $token = array();
    preg_match_all($tokenQuery, $_SERVER['REQUEST_URI'], $token);

    if (isset($token[1][0])) $token = $token[1][0];

    $this->ctrl->set('token', $token);
    echo $this->ctrl->render(); die();
  }

  public function postupload() {
    $this->ctrl->setPageHeading('Video Upload');
    $infoForm = new SPVIDEOLITE_PRO_ALLMYVIDEOS_CMP_Infoform(true);
    $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/Allmyvideos/saveclip' );
    $infoForm->setValues($_POST);
    $this->ctrl->addForm($infoForm);
    $this->setTemplate('postupload.html');
  }

  public function saveclip() {
    if (!OW::getRequest()->isPost()) { 
      throw new Redirect404Exception();
    }

    $infoForm = new SPVIDEOLITE_PRO_ALLMYVIDEOS_CMP_Infoform(true);

    if ( $infoForm->isValid($_POST) ) {
      $language = OW::getLanguage();
      $values = $infoForm->getValues();
      
      $ret = $infoForm->process();
      OW::getFeedback()->info($language->text('video', 'clip_added'));
      $this->ctrl->redirect(OW::getRouter()->urlForRoute('view_clip', array('id' => $ret['id'])));
    } else {
      OW::getFeedback()->error('Error while processing your information');
      $this->ctrl->setPageHeading('Video Upload');
      $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/Allmyvideos/saveclip' );
      $infoForm->setValues($_POST);
      $this->ctrl->addForm($infoForm);
      $this->setTemplate('postupload.html');
    }
  }
}