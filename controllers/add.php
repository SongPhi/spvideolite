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
		$this->assign('staticUrl',OW::getPluginManager()->getPlugin( 'spvideo' )->getStaticUrl());
		$this->assign('embedForm', $this->embedForm);
		// call selected module upload template
		$module = 'Selfservice';
		$func = 'add';
		$viewPath = SPVIDEO_BOL_Service::callProcessorFunction($module, 'getViewPath', $this);
		$view = $func.'.html';
		$this->assign('uploadFormTpl', $viewPath. DS . $view);
		SPVIDEO_BOL_Service::callProcessorFunction($module, $func, $this);
	}
}