<?php

/**
* 
*/
class SPVIDEO_CTRL_Spvideo extends OW_ActionController
{

	public function index() {
		
	}
	
	public function ajaxGetClip() {
		if (!OW::getRequest()->isAjax())
			die('Hacking Attempt!');

		require_once(OW::getPluginManager()->getPlugin( 'spvideo' )->getRootDir() . 'libraries' . DS . 'videopian' . DS . 'Videopian.php');

		try {
			$video = Videopian::get($_POST['clipUrl']);

			$this->setTemplate( OW::getPluginManager()->getPlugin( 'spvideo' )->getCmpViewDir() . 'add_form.html' );
			$formHtml = base64_encode( $this->render() );

			$result = array(
				'error' => false,
				'formHtml' => $formHtml
			);
			
			exit(json_encode($result));
		} catch (Exception $e) {
			$result = array(
				'error' => true,
				'errMsg' => $e->getMessage()
			);
			exit(json_encode($result));
		}
		
	}
}