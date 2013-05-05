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
		$this->assign('embedForm', $this->embedForm);
	}
}