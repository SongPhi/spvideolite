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
		if ( !$this->route )
			$this->route = OW::getRouter()->route();
		return $this->route;
	}

	/**
	 * 
	 */
	private function isRoute( $controller, $action = null ) {
		$this->getRoute();
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

		$doc = OW::getDocument();

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

		OW::getDocument()->addStyleSheet(
			OW::getPluginManager()
			->getPlugin( 'spvideo' )
			->getStaticCssUrl().'spvideo.css'
		);

		$embedForm = $doc->getBody();
		$matches = array();
		preg_match_all( "/<form.*<\/form>/is", $embedForm, $matches );
		$embedForm = $matches[0][0];

		$spVideoCtrl = new SPVIDEO_CTRL_Add();
		$spVideoCtrl->setTemplate( OW::getPluginManager()->getPlugin( 'spvideo' )->getCtrlViewDir() . 'add_index.html' );
		$spVideoCtrl->setEmbedForm( $embedForm );

		$spVideoCtrl->index();

		$doc->setBody( $spVideoCtrl->render() );

	}

	/**
	 *
	 */
	function on_add_console_item( BASE_CLASS_EventCollector $event ) {
		$event->add( array( 'label' => 'My Videos', 'url' => OW_Router::getInstance()->urlForRoute( 'spvideo.my_video' ) ) );
	}
}
