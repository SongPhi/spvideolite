<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * AJAX Upload video component
 *
 * @author Pustak Sadan <pustaksadan.india@gmail.com>
 * @package ow.plugin.spvideolite.components
 * @since 1.6.1
 */
class SPVIDEOLITE_CMP_AjaxUpload extends OW_Component
{
    public function __construct( $url = NULL )
    {
        if ( !OW::getUser()->isAuthorized('video', 'add') )
        {
            $this->setVisible(FALSE);
            
            return;
        }
        
        $userId = OW::getUser()->getId();
        $document = OW::getDocument();
        
        $plugin = OW::getPluginManager()->getPlugin('spvideolite');
        $document->addStyleSheet($plugin->getStaticCssUrl() . 'addvideo.css');
        $document->addScript($plugin->getStaticJsUrl() . 'jQueryRotate.min.js');
        $document->addScript($plugin->getStaticJsUrl() . 'codemirror.min.js');
        $document->addScript($plugin->getStaticJsUrl() . 'addvideo.js');
        
        $document->addScriptDeclarationBeforeIncludes(
            UTIL_JsGenerator::composeJsString(';window.ajaxAddVideoParams = {};
                Object.defineProperties(ajaxAddVideoParams, {
                    actionUrl: {
                        value: {$url},
                        writable: false,
                        enumerable: true
                    },
                    deleteAction: {
                        value: {$deleteAction},
                        writable: false,
                        enumerable: true
                    },
                    submitUrl: {
                        value: {$submitUrl},
                        writable: false,
                        enumerable: true
                    },
                });',
                array(
                    'url' => OW::getRouter()->urlForRoute('spvideolite.ajax_video_add'),
                    'deleteAction' => OW::getRouter()->urlForRoute('spvideolite.ajax_video_delete'),
                    'submitUrl' => OW::getRouter()->urlForRoute('spvideolite.ajax_video_add_submit'),
                )
            )
        );
        $document->addOnloadScript(';window.ajaxVideoAdder.init();');
        
        $this->addForm(new SPVIDEOLITE_CLASS_AjaxUploadForm('user', $userId, $url));
        
        $language = OW::getLanguage();
        $language->addKeyForJs('spvideolite', 'not_all_videos_added');
        $language->addKeyForJs('spvideolite', 'dnd_support');
        $language->addKeyForJs('spvideolite', 'dnd_not_support');
        $language->addKeyForJs('spvideolite', 'drop_here');
        $language->addKeyForJs('spvideolite', 'please_wait');
        $language->addKeyForJs('spvideolite', 'describe_video');
        $language->addKeyForJs('spvideolite', 'video_add_error');
    }
}
