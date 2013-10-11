<?php

/**
* 
*/
class SPVIDEO_CTRL_Add extends OW_ActionController
{
	/**
	 * @var String embedForm
	 */
	private $embedForm = '';

	public function setEmbedForm($embedForm) {
		$this->embedForm = $embedForm;
	}

	public function index() {
		$uploadToken = md5(OW::getUser()->getEmail().'/'.microtime());
		$this->assign('staticUrl',OW::getPluginManager()->getPlugin( 'spvideo' )->getStaticUrl());
		$this->assign('token',$uploadToken);
		$this->assign('embedForm', $this->embedForm);
		// call selected module upload template
		$module = 'selfservice';
		$func = 'add';
		$viewPath = SPVIDEO_DIR_PROCESSORS.DS.$module.DS.'views'.DS;
		$view = $func.'.html';
		include SPVIDEO_DIR_PROCESSORS.DS.$module.DS.$func.'.php';
		$this->assign('uploadFormTpl', $viewPath.$view);
	}
}