<?php

define('SPVIDEOLITE_DIR_ROOT',dirname(__FILE__));
define('SPVIDEOLITE_DIR_PROCESSORS',SPVIDEOLITE_DIR_ROOT.DS.'processors');
define('SPVIDEOLITE_DIR_IMPORTERS',SPVIDEOLITE_DIR_ROOT.DS.'importers');
define('SPVIDEOLITE_DIR_USERFILES',OW::getPluginManager()->getPlugin('spvideolite')->getUserFilesDir());
define('SPVIDEOLITE_DIR_PLUGINFILES',OW::getPluginManager()->getPlugin('spvideolite')->getPluginFilesDir());

// Initialize helper instance
SPVIDEOLITE_BOL_Service::getInstance();

// Overloading default video clip service instance
SPVIDEOLITE_CLASS_ClipService::getInstance();

// Routers declaration
OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.admin',
		'admin/plugins/spvideo',
		'SPVIDEOLITE_CTRL_Admin',
		'index'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.admin_quota',
		'admin/plugins/spvideo/quota',
		'SPVIDEOLITE_CTRL_Admin',
		'quota'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.admin_processor',
		'admin/plugins/spvideo/processor',
		'SPVIDEOLITE_CTRL_Admin',
		'processor'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.admin_categories',
		'admin/plugins/spvideo/categories',
		'SPVIDEOLITE_CTRL_Admin',
		'categories'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.admin_tweaks',
		'admin/plugins/spvideo/tweaks',
		'SPVIDEOLITE_CTRL_Admin',
		'tweaks'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.admin_help',
		'admin/plugins/spvideo/help',
		'SPVIDEOLITE_CTRL_Admin',
		'help'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.admin_saveconfig',
		'admin/plugins/spvideo/saveconfig',
		'SPVIDEOLITE_CTRL_Admin',
		'saveconfig'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.base',
		'spvideolite',
		'SPVIDEOLITE_CTRL_Spvideo',
		'index'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.ajax_get_clip',
		'spvideo/ajax_get_clip',
		'SPVIDEOLITE_CTRL_Spvideo',
		'ajaxGetClip'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.my_video',
		'spvideo/my',
		'SPVIDEOLITE_CTRL_Spvideo',
		'myVideo'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.embed',
		'spvideo/embed/:videoId',
		'SPVIDEOLITE_CTRL_Spvideo',
		'embed'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.import',
		'spvideo/import',
		'SPVIDEOLITE_CTRL_Spvideo',
		'import'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.proxy',
		'spvideo/proxy/:module/:func',
		'SPVIDEOLITE_CTRL_Spvideo',
		'proxy',
		array('module','func','args'=>'')
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.proxy_args',
		'spvideo/proxy/:module/:func/:args',
		'SPVIDEOLITE_CTRL_Spvideo',
		'proxy',
		array('module','func','args')
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideolite.categories',
		'spvideo/categories',
		'SPVIDEOLITE_CTRL_Spvideo',
		'categories'
	)
);

if ( !OW::getRequest()->isAjax() && !OW::getRequest()->isPost() ) {
	// Events handling
	$eventHandler = new SPVIDEOLITE_CLASS_EventHandler();
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
$autoloader->addPackagePointer('SPVIDEOLITE_IMP', SPVIDEOLITE_DIR_IMPORTERS);
// adding package pointers for processors
$autoloader->addPackagePointer('SPVIDEOLITE_PRO', SPVIDEOLITE_DIR_PROCESSORS);

// registering processors
SPVIDEOLITE_BOL_Service::registerProcessor('Selfservice');
SPVIDEOLITE_BOL_Service::registerProcessor('Transloadit');
// SPVIDEOLITE_BOL_Service::getProcessorInstance('Selfservice');