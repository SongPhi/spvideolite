<?php

/**
 *
 */
class SPVIDEO_CLASS_EventHandler {

	private $route = null;

	/**
	 * 
	 */
	private function getRoute() {
		try {
			return OW::getRouter()->route();
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * 
	 */
	private function isRoute( $controller, $action = null ) {
		$this->route = $this->getRoute();

		if ($this->route == false) {
			return false;
		}

		if ( $this->route["controller"] == $controller ) {
			if ( $this->route["action"] == $action || $action==null ) {
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 */
	public function replaceVideoAddView( $event ) {
		if ( !$this->isRoute( 'VIDEO_CTRL_Add', 'index' ) )
			return;
		if ( OW::getRequest()->isPost() )
			return;
		if ( OW::getRequest()->isAjax() )
			return;

		$doc = OW::getDocument();

		// add scripts
		OW::getDocument()->addScript(
			OW::getPluginManager()
			->getPlugin( 'spvideo' )
			->getStaticUrl().'js/base64.js'
		);
		OW::getDocument()->addScript(
			OW::getPluginManager()
			->getPlugin( 'spvideo' )
			->getStaticUrl().'js/spvideo.js'
		);
		OW::getDocument()->addScript(
			OW::getPluginManager()
			->getPlugin( 'spvideo' )
			->getStaticUrl().'js/jquery.easing.min.js'
		);

		// add stylesheets
		OW::getDocument()->addStyleSheet(
			OW::getPluginManager()
			->getPlugin( 'spvideo' )
			->getStaticCssUrl().'spvideo.css'
		);

		// inject & modify the adding form
		$embedForm = $doc->getBody();
		$matches = array();
		preg_match_all( "/<form.*<\/form>/is", $embedForm, $matches );
		$embedForm = $matches[0][0];
		$importService = SPVIDEO_CLASS_ImportService::getInstance();
		$spVideoCtrl = new SPVIDEO_CTRL_Add();
		$spVideoCtrl->setTemplate( OW::getPluginManager()->getPlugin( 'spvideo' )->getCtrlViewDir() . 'add_index.html' );
		$spVideoCtrl->setEmbedForm( $embedForm );

		$spVideoCtrl->index();

		// commit body changes
		$doc->setBody( $spVideoCtrl->render() );

	}

	/**
	 *
	 */
	function on_add_console_item( BASE_CLASS_EventCollector $event ) {
		$event->add( array( 'label' => 'My Videos', 'url' => OW_Router::getInstance()->urlForRoute( 'spvideo.my_video' ) ) );
	}
}
