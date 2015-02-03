<?php

/**
* 
*/
class SPVIDEOLITE_PRO_Selfservice extends SPVIDEOLITE_CLASS_AbstractProcessor
{
  protected function init() {
    $autoloader = OW::getAutoloader();
    $autoloader->addPackagePointer(
      'SPVIDEOLITE_PRO_SELFSERVICE_CMP',
      $this->getClassPath() . DS . 'components'
    );
    $autoloader->addPackagePointer(
      'SPVIDEOLITE_PRO_SELFSERVICE_CLASS',
      $this->getClassPath() . DS . 'classes'
    );

    spl_autoload_register(function($class_name)
    {
      $parts = explode('\\', $class_name);
      $namespace = array_shift($parts);
      if($namespace === 'PHPVideoToolkit')
      {
        $class = str_replace('_', DS, array_pop($parts));
        $path = SPVIDEOLITE_DIR_ROOT . DS . 'libs' . DS.'PHPVideoToolkit'.DS.ltrim(implode(DS, $parts).DS, DS).$class.'.php';
        if(is_file($path) === true)
        {
          require_once $path;
        }
      }
    });
  }

  public function add() {
    $service = SPVIDEOLITE_BOL_Service::getInstance();
    $uploadToken = md5(OW::getUser()->getEmail().'/'.microtime());
    $this->ctrl->assign('token',$uploadToken);

    OW::getDocument()->addScript( $service->getJsUrl('fileupload/vendor/jquery.ui.widget') );
    OW::getDocument()->addScript( $service->getJsUrl('fileupload/jquery.iframe-transport') );
    OW::getDocument()->addScript( $service->getJsUrl('fileupload/jquery.fileupload') );

    $this->ctrl->assign('uploadDest', OW::getRouter()->getBaseUrl().'spvideo/proxy/Selfservice/upload');

    $infoForm = new SPVIDEOLITE_PRO_SELFSERVICE_CMP_Infoform();
    $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/Selfservice/postupload' );
    $infoForm->setValues(array(
      'token' => $uploadToken
    ));
    $this->ctrl->addForm($infoForm);
  }

  public function upload() {
    $uploadPath = SPVIDEOLITE_DIR_PLUGINFILES . DS . $_POST['token'] . DS;
    @mkdir($uploadPath, 0777);
    $upload_handler = new SPVIDEOLITE_PRO_SELFSERVICE_CLASS_UploadHandler(array(
        'upload_dir' => $uploadPath,
        'accept_file_types' => '/\.(mp4|m4v|flv|f4v|ogv|ogg|webm)$/i',
        'param_name' => 'videoClip',
        'max_file_size' => '500000000'
    ));
  }

  public function postupload() {
    $this->ctrl->setPageHeading('Video Upload');
    $infoForm = new SPVIDEOLITE_PRO_SELFSERVICE_CMP_Infoform(true);
    $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/Selfservice/saveclip' );
    $infoForm->setValues($_POST);
    $this->ctrl->addForm($infoForm);
    $this->setTemplate('postupload.html');
  }

  public function saveclip() {
    if (!OW::getRequest()->isPost()) { 
      throw new Redirect404Exception();
    }

    $infoForm = new SPVIDEOLITE_PRO_SELFSERVICE_CMP_Infoform(true);

    if ( $infoForm->isValid($_POST) ) {
      $language = OW::getLanguage();
      $values = $infoForm->getValues();
      
      $ret = $infoForm->process();
      OW::getFeedback()->info($language->text('video', 'clip_added'));
      $this->ctrl->redirect(OW::getRouter()->urlForRoute('view_clip', array('id' => $ret['id'])));
    } else {
      OW::getFeedback()->error('Error while processing your information');
      $this->ctrl->setPageHeading('Video Upload');
      $infoForm->setAction( OW::getRouter()->getBaseUrl().'spvideo/proxy/Selfservice/saveclip' );
      $infoForm->setValues($_POST);
      $this->ctrl->addForm($infoForm);
      $this->setTemplate('postupload.html');
    }
  }
}