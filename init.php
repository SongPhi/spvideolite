<?php

define('SPVIDEO_DIR_ROOT',dirname(__FILE__));
define('SPVIDEO_DIR_PROCESSORS',SPVIDEO_DIR_ROOT.DS.'processors');
define('SPVIDEO_DIR_IMPORTERS',SPVIDEO_DIR_ROOT.DS.'importers');
define('SPVIDEO_DIR_USERFILES',OW::getPluginManager()->getPlugin('spvideo')->getUserFilesDir());
define('SPVIDEO_DIR_PLUGINFILES',OW::getPluginManager()->getPlugin('spvideo')->getPluginFilesDir());

// Initialize helper instance
SPVIDEO_BOL_Service::getInstance();

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
		'spvideo.admin_categories',
		'admin/plugins/spvideo/categories',
		'SPVIDEO_CTRL_Admin',
		'categories'
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
		'spvideo.admin_saveconfig',
		'admin/plugins/spvideo/saveconfig',
		'SPVIDEO_CTRL_Admin',
		'saveconfig'
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

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.categories',
		'spvideo/categories',
		'SPVIDEO_CTRL_Spvideo',
		'categories'
	)
);

if ( !OW::getRequest()->isAjax() && !OW::getRequest()->isPost() ) {
	// Events handling
	$eventHandler = new SPVIDEO_CLASS_EventHandler();
	OW::getEventManager()->bind( OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array( $eventHandler, 'replaceVideoAddView' ) );
	OW::getEventManager()->bind( OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array( $eventHandler, 'addCategoriesList' ) );
	OW::getEventManager()->bind( 'base.add_main_console_item', array( $eventHandler, 'on_add_console_item' ) );
	OW::getEventManager()->bind( 'video.collect_video_toolbar_items', array($eventHandler,'showLessVideoDescription') );
	OW::getEventManager()->bind( 'video.collect_video_toolbar_items', array($eventHandler,'correctPlayerSize') );
	OW::getEventManager()->bind( 'video.collect_video_toolbar_items', array($eventHandler,'addLargerPlayerButton') );
	OW::getEventManager()->bind( OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array( $eventHandler, 'fixLongTitles' ) );
}

// adding package pointers for importers
$autoloader = OW::getAutoloader();
$autoloader->addPackagePointer('SPVIDEO_IMP', SPVIDEO_DIR_IMPORTERS);
// adding package pointers for processors
$autoloader->addPackagePointer('SPVIDEO_PRO', SPVIDEO_DIR_PROCESSORS);

// registering processors
SPVIDEO_BOL_Service::registerProcessor('Selfservice');
SPVIDEO_BOL_Service::registerProcessor('Transloadit');
// SPVIDEO_BOL_Service::getProcessorInstance('Selfservice');