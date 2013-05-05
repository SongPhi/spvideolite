<?php

// Overloading default video clip service instance
SPVIDEO_CLASS_ClipService::getInstance();

// Routers declaration
OW::getRouter()->addRoute(new OW_Route('spvideo.ajax_get_clip', 'spvideo/ajax_get_clip', 'SPVIDEO_CTRL_Spvideo', 'ajaxGetClip'));

// Events handling
$eventHandler = new SPVIDEO_CLASS_EventHandler();
OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'replaceVideoAddView'));
