<?php

/**
 *
 */
class SPVIDEO_CTRL_Admin extends ADMIN_CTRL_Abstract {

	private $menu = null;

	function __construct() {
		$this->menu = $this->getMenu();
		$this->addComponent( 'menu', $this->menu );
		parent::__construct();
	}

	function setPageHeading( $heading ) {
		$heading = $heading . ' - SPVideo';

		return parent::setPageHeading( $heading );
	}

	function index() {
		$this->setPageHeading( 'General - Settings' );
	}

	function getMenu() {
		$language = OW::getLanguage();

		$menu = new BASE_CMP_ContentMenu();
		$menuItems = array();

		$item = new BASE_MenuItem();
		$item->setLabel( $language->text( 'spvideo', 'adm_menu_general' ) );
		$item->setUrl( OW::getRouter()->urlForRoute( 'spvideo.admin' ) );
		$item->setKey( 'general' );
		$item->setIconClass( 'ow_ic_gear_wheel' );
		$item->setOrder( 0 );
		$menuItems[] = $item;

		$item = new BASE_MenuItem();
		$item->setLabel( $language->text( 'spvideo', 'adm_menu_quota' ) );
		$item->setUrl( OW::getRouter()->urlForRoute( 'spvideo.admin_quota' ) );
		$item->setKey( 'quota' );
		$item->setIconClass( 'ow_ic_dashboard' );
		$item->setOrder( 1 );
		$menuItems[] = $item;

		$item = new BASE_MenuItem();
		$item->setLabel( $language->text( 'spvideo', 'adm_menu_processor' ) );
		$item->setUrl( OW::getRouter()->urlForRoute( 'spvideo.admin_processor' ) );
		$item->setKey( 'quota' );
		$item->setIconClass( 'ow_ic_files' );
		$item->setOrder( 2 );
		$menuItems[] = $item;

		$item = new BASE_MenuItem();
		$item->setLabel( $language->text( 'spvideo', 'adm_menu_categories' ) );
		$item->setUrl( OW::getRouter()->urlForRoute( 'spvideo.admin_categories' ) );
		$item->setKey( 'categories' );
		$item->setIconClass( 'ow_ic_folder' );
		$item->setOrder( 3 );
		$menuItems[] = $item;

		$item = new BASE_MenuItem();
		$item->setLabel( $language->text( 'spvideo', 'adm_menu_tweaks' ) );
		$item->setUrl( OW::getRouter()->urlForRoute( 'spvideo.admin_tweaks' ) );
		$item->setKey( 'quota' );
		$item->setIconClass( 'ow_ic_star' );
		$item->setOrder( 4 );
		$menuItems[] = $item;

		$item = new BASE_MenuItem();
		$item->setLabel( $language->text( 'spvideo', 'adm_menu_help' ) );
		$item->setUrl( OW::getRouter()->urlForRoute( 'spvideo.admin_help' ) );
		$item->setKey( 'quota' );
		$item->setIconClass( 'ow_ic_help' );
		$item->setOrder( 5 );
		$menuItems[] = $item;

		$menu->setMenuItems( $menuItems );
		$menu->deactivateElements();

		return $menu;
	}

	function quota() {
		// $userService = BOL_UserService::getInstance();

        $roleService = BOL_AuthorizationService::getInstance();

        $roles = $roleService->findNonGuestRoleList();

        $total = 0;

        foreach ( $roles as $role )
        {
            $userCount = $roleService->countUserByRoleId($role->getId());

            $list[$role->getId()] = array(
                'dto' => $role,
                'userCount' => $userCount,
            );

            $total += $userCount;
        }

        $this->assign('set', $list);

        // $this->assign('roles',$roles);

        $this->setPageHeading( 'Quota - Settings' );
	}

	function processor() {
		$this->setPageHeading( 'Processor- Settings' );
	}

	function tweaks() {
		$this->setPageHeading( 'Tweaks - Settings' );
	}

	function help() {
		$this->setPageHeading( 'Help and Support' );
	}

	public function categories() {
    $this->setPageHeading( 'Video Categories' );    
  }

}
