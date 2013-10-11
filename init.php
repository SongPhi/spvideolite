<?php

define('SPVIDEO_DIR_ROOT',dirname(__FILE__));
define('SPVIDEO_DIR_PROCESSORS',SPVIDEO_DIR_ROOT.DS.'modules'.DS.'processors');
define('SPVIDEO_DIR_USERFILES',OW::getPluginManager()->getPlugin('spvideo')->getUserFilesDir());

// Overloading default video clip service instance
SPVIDEO_CLASS_ClipService::getInstance();

// Routers declaration
OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.admin',
		'admin/plugins/spvideo',
		'SPVIDEO_CTRL_Admin',
		'index'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.admin_quota',
		'admin/plugins/spvideo/quota',
		'SPVIDEO_CTRL_Admin',
		'quota'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.admin_processor',
		'admin/plugins/spvideo/processor',
		'SPVIDEO_CTRL_Admin',
		'processor'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.admin_tweaks',
		'admin/plugins/spvideo/tweaks',
		'SPVIDEO_CTRL_Admin',
		'tweaks'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.admin_help',
		'admin/plugins/spvideo/help',
		'SPVIDEO_CTRL_Admin',
		'help'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.base',
		'spvideo',
		'SPVIDEO_CTRL_Spvideo',
		'index'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.ajax_get_clip',
		'spvideo/ajax_get_clip',
		'SPVIDEO_CTRL_Spvideo',
		'ajaxGetClip'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.my_video',
		'spvideo/my',
		'SPVIDEO_CTRL_Spvideo',
		'myVideo'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.embed',
		'spvideo/embed/:videoId',
		'SPVIDEO_CTRL_Spvideo',
		'embed'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.import',
		'spvideo/import',
		'SPVIDEO_CTRL_Spvideo',
		'import'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.proxy',
		'spvideo/proxy/:module/:func',
		'SPVIDEO_CTRL_Spvideo',
		'proxy',
		array('module','func','args'=>'')
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.proxy_args',
		'spvideo/proxy/:module/:func/:args',
		'SPVIDEO_CTRL_Spvideo',
		'proxy',
		array('module','func','args')
	)
);

// Events handling
$eventHandler = new SPVIDEO_CLASS_EventHandler();
OW::getEventManager()->bind( OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array( $eventHandler, 'replaceVideoAddView' ) );
OW::getEventManager()->bind( 'base.add_main_console_item', array( $eventHandler, 'on_add_console_item' ) );
