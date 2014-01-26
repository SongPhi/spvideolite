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
    $language = OW::getLanguage();
		$this->setPageHeading( 'General - Settings' );

    OW::getDocument()->addStyleSheet( SPVIDEO_BOL_Service::getCssUrl('spvideo_admin') );
    OW::getDocument()->addStyleSheet( SPVIDEO_BOL_Service::getCssUrl('toggles-light') );
    OW::getDocument()->addScript( SPVIDEO_BOL_Service::getJsUrl('jquery.toggles.min') );

    OW::getDocument()->addOnloadScript("
      $('#features input[type=checkbox]').each(function(index,obj){
        var togglerId = $(obj).attr('id')+'_toggler';
        $(obj).parent().append('<div class=\"toggle-light\" id=\"'+togglerId+'\"></div>');
        $('#'+togglerId).toggles({
          drag: true,
          width: 55,
          text: {
            on: '".$language->text( 'spvideo', 'chk_on' )."',
            off: '".$language->text( 'spvideo', 'chk_off' )."'
          },
          on: $(obj).is(':checked'),
          checkbox: $(obj)
        });
        $(obj).hide();
        $('#'+togglerId).on('toggle',function(e,active){
          var thisKey = $(this).attr('id');
          var configKey = 'features.' + $(this).attr('id').replace('_toggler','');
          var formKey = $(this).attr('id').replace('_toggler','') + '_cfg_form';

          var postData = { key : configKey, value : active };
          if (active) {
            postData.value = 1;
            $('#'+formKey).slideDown('fast');
            $('#'+thisKey).parents('table').find('tr').first().removeClass('ow_tr_last');
          } else {
            postData.value = 0;
            $('#'+formKey).slideUp('fast',function(){
              $('#'+formKey).hide();
              $('#' + thisKey).parents('table').find('tr').first().addClass('ow_tr_last');
            });            
          }
          $.post(
            '".OW::getRouter()->urlForRoute('spvideo.admin_saveconfig')."',
            postData,
            function( data ) {
            },
            'text'
          );
          
        });

        if (!$('#upload_video').is(':checked')) {
          $('#upload_video_cfg_form').hide();
        } else {
          $('#upload_video').parents('table').find('tr').first().removeClass('ow_tr_last');
        }

        if (!$('#categories').is(':checked')) {
          $('#categories_cfg_form').hide();
        } else {
          $('#categories').parents('table').find('tr').first().removeClass('ow_tr_last');
        }

        if (!$('#importers').is(':checked')) {
          $('#importers_cfg_form').hide();
        } else {
          $('#importers').parents('table').find('tr').first().removeClass('ow_tr_last');
        }
      });
    ");
    $features = SPVIDEO_BOL_Configs::getInstance()->searchKey('#^features\..+?$#im');
    $featuresConfig = array();
    foreach ($features as $feature) {
      $featuresConfig[substr($feature, 9)] = SPVIDEO_BOL_Configs::getInstance()->get($feature);
    }
    $this->assign('features',$featuresConfig);
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

        $this->setPageHeading( 'Quota' );
	}

	function processor() {
		$this->setPageHeading( 'Processor' );
	}

	function tweaks() {
		$language = OW::getLanguage();
		$this->setPageHeading( 'Tweaks' );

    OW::getDocument()->addStyleSheet( SPVIDEO_BOL_Service::getCssUrl('toggles-light') );
    OW::getDocument()->addScript( SPVIDEO_BOL_Service::getJsUrl('jquery.toggles.min') );

    OW::getDocument()->addOnloadScript("
	  	$('.tweaksForm input[type=checkbox]').each(function(index,obj){
	      var togglerId = $(obj).attr('id')+'_toggler';
	      $(obj).parent().append('<div class=\"toggle-light\" id=\"'+togglerId+'\"></div>');
	      $('#'+togglerId).toggles({
	        drag: true,
	        width: 55,
	        text: {
	          on: '".$language->text( 'spvideo', 'chk_on' )."',
	          off: '".$language->text( 'spvideo', 'chk_off' )."'
	        },
	        on: $(obj).is(':checked'),
	        checkbox: $(obj)
	      });
	      $(obj).hide();
	      $('#'+togglerId).on('toggle',function(e,active){
	      	var configKey = 'tweaks.' + $(this).attr('id').replace('_toggler','');
	      	var postData = { key : configKey, value : active };
	      	if (active)
	      		postData.value = 1;
	      	else
	      		postData.value = 0;
	      	$.post(
	      		'".OW::getRouter()->urlForRoute('spvideo.admin_saveconfig')."',
	      		postData,
		      	function( data ) {
						},
						'text'
					);
	      });
	    });
    ");
    $tweaks = SPVIDEO_BOL_Configs::getInstance()->searchKey('#^tweaks\..+?$#im');
    $tweaksConfig = array();
    foreach ($tweaks as $tweak) {
    	$tweaksConfig[substr($tweak, 7)] = SPVIDEO_BOL_Configs::getInstance()->get($tweak);
    }
    $this->assign('tweaks',$tweaksConfig);
	}

	function help() {
		$this->setPageHeading( 'Help and Support' );
	}

	public function categories() {
    $this->setPageHeading( 'Categories' );    
    // http://ow16.dev/admin/languages?prefix=spvideo&search=category_
  }

  public function saveconfig( array $params) {
  	SPVIDEO_BOL_Configs::getInstance()->set($_POST['key'],$_POST['value']);
  	die();
  }

}

