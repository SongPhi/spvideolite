<?php
/**
 * Copyright 2015 SongPhi
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
if (!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
define('SPVIDEOLITE_DIR_ROOT', dirname(__FILE__));
define('SPVIDEOLITE_DIR_PROCESSORS',SPVIDEOLITE_DIR_ROOT.DS.'processors');
define('SPVIDEOLITE_DIR_IMPORTERS', SPVIDEOLITE_DIR_ROOT . DS . 'importers');
define('SPVIDEOLITE_DIR_USERFILES', OW::getPluginManager()->getPlugin('spvideolite')->getUserFilesDir());
define('SPVIDEOLITE_DIR_PLUGINFILES', OW::getPluginManager()->getPlugin('spvideolite')->getPluginFilesDir());
$spvlConfig = SPVIDEOLITE_BOL_Configs::getInstance();

// Routers declaration
OW::getRouter()->addRoute(new OW_Route('spvideolite.admin', 'admin/plugins/spvideolite', 'SPVIDEOLITE_CTRL_Admin', 'index'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_upload', 'admin/plugins/spvideolite/upload', 'SPVIDEOLITE_CTRL_Admin', 'upload'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_processor', 'admin/plugins/spvideolite/processor', 'SPVIDEOLITE_CTRL_Admin', 'processor'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_help', 'admin/plugins/spvideolite/help', 'SPVIDEOLITE_CTRL_Admin', 'help'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.admin_saveconfig', 'admin/plugins/spvideolite/saveconfig', 'SPVIDEOLITE_CTRL_Admin', 'saveconfig'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.ajax_get_clip', 'spvideo/ajax_get_clip', 'SPVIDEOLITE_CTRL_Spvideo', 'ajaxGetClip'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.base', 'spvideo', 'SPVIDEOLITE_CTRL_Spvideo', 'index'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.my_video', 'spvideo/my', 'SPVIDEOLITE_CTRL_Spvideo', 'myVideo'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.embed', 'spvideo/embed/:videoId', 'SPVIDEOLITE_CTRL_Spvideo', 'embed'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.vidzi', 'spvideo/vidzi/:videoId', 'SPVIDEOLITE_CTRL_Vidzi', 'embed'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.videojs.fbembed', 'spvideo/fbembed/:videoId', 'SPVIDEOLITE_CTRL_Videojs', 'fbEmbed'));
// compatible with older version
OW::getRouter()->addRoute(new OW_Route('spvideolite.videojs.old_fbembed', 'spvideolite/fbembed/:videoId', 'SPVIDEOLITE_CTRL_Videojs', 'fbEmbed'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.import', 'spvideo/import', 'SPVIDEOLITE_CTRL_Spvideo', 'import'));

OW::getRouter()->addRoute(new OW_Route('spvideolite.proxy', 'spvideo/proxy/:module/:func', 'SPVIDEOLITE_CTRL_Spvideo', 'proxy', array('module', 'func', 'args' => '')));

OW::getRouter()->addRoute(new OW_Route('spvideolite.proxy_args', 'spvideo/proxy/:module/:func/:args', 'SPVIDEOLITE_CTRL_Spvideo', 'proxy', array('module', 'func', 'args')));

OW::getRouter()->addRoute(new OW_Route('spvideolite.ajax_video_add', 'spvideolite/ajax-add', 'SPVIDEOLITE_CTRL_AjaxUpload', 'ajaxAdd'));
OW::getRouter()->addRoute(new OW_Route('spvideolite.ajax_video_add_submit', 'spvideolite/ajax-add-submit', 'SPVIDEOLITE_CTRL_AjaxUpload', 'ajaxSubmitVideos'));
OW::getRouter()->addRoute(new OW_Route('spvideolite.ajax_video_delete', 'spvideolite/ajax-video-delete', 'SPVIDEOLITE_CTRL_AjaxUpload', 'ajaxDelete'));

// categories routings
OW::getRouter()->addRoute(
    new OW_Route('spvideolite.categories', 'video/categories','SPVIDEOLITE_CTRL_Categories', 'index')
);
OW::getRouter()->addRoute(
    new OW_Route('spvideolite.category_latest', 'video/category/:slug','SPVIDEOLITE_CTRL_Categories', 'videoList',
        array('slug','listtype'=>'latest'))
);
OW::getRouter()->addRoute(
    new OW_Route('spvideolite.category', 'video/category/:slug/:listtype','SPVIDEOLITE_CTRL_Categories', 'videoList',
        array('slug','listtype'))
);

OW::getRouter()->addRoute(
    new OW_Route('spvideolite.admin_categories', 'admin/plugins/spvideolite/categories', 
        'SPVIDEOLITE_CTRL_Admin', 'categories')
);

try {
    
    // check if base video plugin is installed and active
    OW::getPluginManager()->getPlugin('video');
    
    // Initialize helper instance
    SPVIDEOLITE_BOL_Service::getInstance();
    // Events handling
    $eventHandler = new SPVIDEOLITE_CLASS_EventHandler();

            OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'addCategoriesList'));
    
    OW::getEventManager()->bind('core.after_route', array($eventHandler, 'initServiceHooking'));
    
    if ((!OW::getRequest()->isAjax() || isset($_SERVER['HTTP_X_PJAX'])) && !OW::getRequest()->isPost()) {
        
        if ($spvlConfig->get('tweaks.link_import')) OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'replaceVideoAddView'));
        
        if ($spvlConfig->get('tweaks.desc_show_more')) OW::getEventManager()->bind('video.collect_video_toolbar_items', array($eventHandler, 'showLessVideoDescription'));
        
        if ($spvlConfig->get('tweaks.correct_player_size')) OW::getEventManager()->bind('video.collect_video_toolbar_items', array($eventHandler, 'correctPlayerSize'));
        
        if ($spvlConfig->get('tweaks.player_enlargable')) OW::getEventManager()->bind('video.collect_video_toolbar_items', array($eventHandler, 'addLargerPlayerButton'));
        
        if ($spvlConfig->get('tweaks.fix_long_titles')) OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'fixLongTitles'));

        if ($spvlConfig->get('tweaks.forum_bridge')) OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'integrateForum'));

        if ($spvlConfig->get('tweaks.blog_bridge'))  OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($eventHandler, 'integrateBlog'));

    }
    
    // adding package pointers for importers
    OW::getAutoloader()->addPackagePointer('SPVIDEOLITE_IMP', SPVIDEOLITE_DIR_IMPORTERS);

    // adding package pointers for processors
    OW::getAutoloader()->addPackagePointer('SPVIDEOLITE_PRO', SPVIDEOLITE_DIR_PROCESSORS);

    // registering processors
    SPVIDEOLITE_BOL_Service::registerProcessor('Allmyvideos');
} catch(Exception $err) {
    
    // failed to detect base video plugin
    
    
}

