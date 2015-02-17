<?php
define('SPVIDEOLITE_DIR_ROOT', dirname(__FILE__));
define('SPVIDEOLITE_DIR_IMPORTERS', SPVIDEOLITE_DIR_ROOT . DS . 'importers');
define('SPVIDEOLITE_DIR_USERFILES', OW::getPluginManager()->getPlugin('spvideolite')->getUserFilesDir());
define('SPVIDEOLITE_DIR_PLUGINFILES', OW::getPluginManager()->getPlugin('spvideolite')->getPluginFilesDir());
$spvlConfig = SPVIDEOLITE_BOL_Configs::getInstance();

// Routers declaration
OW::getRouter()->addRoute(new OW_Route('spvideolite.admin', 'admin/plugins/spvideolite', 'SPVIDEOLITE_CTRL_Admin', 'index'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_quota', 'admin/plugins/spvideolite/quota', 'SPVIDEOLITE_CTRL_Admin', 'quota'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_processor', 'admin/plugins/spvideolite/processor', 'SPVIDEOLITE_CTRL_Admin', 'processor'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_categories', 'admin/plugins/spvideolite/categories', 'SPVIDEOLITE_CTRL_Admin', 'categories'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_tweaks', 'admin/plugins/spvideolite/tweaks', 'SPVIDEOLITE_CTRL_Admin', 'tweaks'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_help', 'admin/plugins/spvideolite/help', 'SPVIDEOLITE_CTRL_Admin', 'help'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_saveconfig', 'admin/plugins/spvideolite/saveconfig', 'SPVIDEOLITE_CTRL_Admin', 'saveconfig'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.base', 'spvideolite', 'SPVIDEOLITE_CTRL_Spvideo', 'index'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.ajax_get_clip', 'spvideolite/ajax_get_clip', 'SPVIDEOLITE_CTRL_Spvideo', 'ajaxGetClip'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.my_video', 'spvideolite/my', 'SPVIDEOLITE_CTRL_Spvideo', 'myVideo'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.embed', 'spvideolite/embed/:videoId', 'SPVIDEOLITE_CTRL_Spvideo', 'embed'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.import', 'spvideolite/import', 'SPVIDEOLITE_CTRL_Spvideo', 'import'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.proxy', 'spvideolite/proxy/:module/:func', 'SPVIDEOLITE_CTRL_Spvideo', 'proxy', array('module', 'func', 'args' => '')));

OW::getRouter()->addRoute(new OW_Route('spvideolite.proxy_args', 'spvideolite/proxy/:module/:func/:args', 'SPVIDEOLITE_CTRL_Spvideo', 'proxy', array('module', 'func', 'args')));

OW::getRouter()->addRoute(new OW_Route('spvideolite.categories', 'spvideolite/categories', 'SPVIDEOLITE_CTRL_Spvideo', 'categories'));

try {
    
    // check if base video plugin is installed and active
    OW::getPluginManager()->getPlugin('video');
    
    // Initialize helper instance
    SPVIDEOLITE_BOL_Service::getInstance();
    
    if (!OW::getRequest()->isAjax() && !OW::getRequest()->isPost()) {
        
        // Events handling
        $eventHandler = new SPVIDEOLITE_CLASS_EventHandler();
        
        OW::getEventManager()->bind('core.after_route', array($eventHandler, 'initServiceHooking'));
        
        if ($spvlConfig->get('tweaks.link_import')) OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'replaceVideoAddView'));
        
        if ($spvlConfig->get('tweaks.desc_show_more')) OW::getEventManager()->bind('video.collect_video_toolbar_items', array($eventHandler, 'showLessVideoDescription'));
        
        if ($spvlConfig->get('tweaks.correct_player_size')) OW::getEventManager()->bind('video.collect_video_toolbar_items', array($eventHandler, 'correctPlayerSize'));
        
        if ($spvlConfig->get('tweaks.player_enlargable')) OW::getEventManager()->bind('video.collect_video_toolbar_items', array($eventHandler, 'addLargerPlayerButton'));
        
        if ($spvlConfig->get('tweaks.fix_long_titles')) OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'fixLongTitles'));
    }
    
    // adding package pointers for importers
    OW::getAutoloader()->addPackagePointer('SPVIDEOLITE_IMP', SPVIDEOLITE_DIR_IMPORTERS);
} catch(Exception $err) {
    
    // failed to detect base video plugin
    
    
}

