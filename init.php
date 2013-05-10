<?php

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
		'spvideo.admin_encoder',
		'admin/plugins/spvideo/encoder',
		'SPVIDEO_CTRL_Admin',
		'encoder'
	)
);

OW::getRouter()->addRoute(
	new OW_Route(
		'spvideo.admin_storage',
		'admin/plugins/spvideo/storage',
		'SPVIDEO_CTRL_Admin',
		'storage'
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



// Events handling
$eventHandler = new SPVIDEO_CLASS_EventHandler();
OW::getEventManager()->bind( OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array( $eventHandler, 'replaceVideoAddView' ) );
OW::getEventManager()->bind( 'base.add_main_console_item', array( $eventHandler, 'on_add_console_item' ) );
