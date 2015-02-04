<?php

/**
* 
*/
class SPVIDEOLITE_CTRL_Add extends OW_ActionController
{
	/**
	 * @var String embedForm
	 */
	private $embedForm = '';

	public function setEmbedForm($embedForm) {
		$this->embedForm = $embedForm;
	}

	public function index() {
		$this->assign('staticUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticUrl());
		$this->assign('embedForm', $this->embedForm);
	}
}