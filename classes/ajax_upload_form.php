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
 * 
 * 
 * @author Kairat Bakitow <kainisoft@gmail.com>
 * @package ow_plugins.spvideolite.classes
 * @since 1.6.1
 */
class SPVIDEOLITE_CLASS_AjaxUploadForm extends Form
{
    private $clipService;
    public function __construct( $entityType, $entityId, $url = NULL )
    {
        parent::__construct('ajax-upload');
        $this->clipService = VIDEO_BOL_ClipService::getInstance();
        $this->setAjax(TRUE);
        $this->setAjaxResetOnSuccess(FALSE);
        //$this->setAjaxDataType(Form::AJAX_DATA_TYPE_JSON);
        $this->setAction(OW::getRouter()->urlForRoute('spvideolite.ajax_video_add_submit'));
        $this->bindJsFunction('success', 
            UTIL_JsGenerator::composeJsString('function( data )
            {
                if ( data )
                {
                    if ( !data.result )
                    {
                        if ( data.msg )
                        {
                            OW.error(data.msg);
                        }
                        else
                        {
                            OW.getLanguageText("spvideolite", "video_add_error");
                        }
                    }
                    else
                    {
                        var url = {$url};
                        
                        if ( url )
                        {
                            window.location.href = url;
                        }
                        else if ( data.url )
                        {
                            window.location.href = data.url;
                        }
                    }
                }
                else
                {
                    OW.error("Server error");
                }
            }', array(
                'url' => $url
            ))
        );
        
        $language = OW::getLanguage();

        $userId = OW::getUser()->getId();

        $submit = new Submit('submit');
        $submit->addAttribute('class', 'ow_ic_submit ow_positive');
        $this->addElement($submit);
    }
    
    public function addClip($clipInfo)
    {
        $clip = new VIDEO_BOL_Clip();
        $clip->title = htmlspecialchars($clipInfo["title"]);
        $description = UTIL_HtmlTag::stripJs($clipInfo["desc"]);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $description = nl2br($description, true);
        $clip->description = $description;
        $clip->userId = OW::getUser()->getId();
        $clip->thumbUrl = preg_replace("#(http://|https://)#i", "//",$clipInfo["thumbnail"]);

        $clip->code = UTIL_HtmlTag::stripJs($clipInfo["code"]);

        $prov = new VideoProviders($clip->code);

        $privacy = OW::getEventManager()->call(
            'plugin.privacy.get_privacy',
            array('ownerId' => $clip->userId, 'action' => 'video_view_video')
        );
                    
        $clip->provider = $prov->detectProvider();
        $clip->addDatetime = time();
        $clip->status = 'approved';
        $clip->privacy = mb_strlen($privacy) ? $privacy : 'everybody';

        $eventParams = array('pluginKey' => 'video', 'action' => 'add_video');

        if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === true )
        {
            OW::getEventManager()->call('usercredits.track_action', $eventParams);
        }
        
        if ( $this->clipService->addClip($clip) )
        {
            if(isset($clipInfo['tags']))
            {
                BOL_TagService::getInstance()->updateEntityTags($clip->id, 'video', explode(',', $clipInfo['tags']));
            }
          
            // Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'video',
                'entityType' => 'video_comments',
                'entityId' => $clip->id,
                'userId' => $clip->userId
            ));
            
            OW::getEventManager()->trigger($event);

            return $clip->id;
        }
        return false;
    }
    /**
     * Adds video clip
     *
     * @return boolean
     */
    public function process()
    {
        $language = OW::getLanguage();
        $data = $_POST['data'];
        $resp = array('result' => FALSE);
        $ids = array();
        foreach ($data as $key => $values) 
        {
            $code = VIDEO_BOL_ClipService::getInstance()->validateClipCode($values["code"]);
            if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) )
            {
                return array('result' => false, 'msg' => $language->text('video', 'resource_not_allowed'));
            }
            $clipId = $this->addClip($values);
            if($clipId)
            {
                array_push($ids, $clipId);
            }
        }
        if(count($ids) > 1)
        {
            $resp['msg'] = $language->text('spvideolite', 'clips_added');
        }else
        {
            $resp['msg'] = $language->text('video', 'clip_added');
        }
        if(count($ids))
        {
            $resp['result'] = TRUE;   
            $resp['id'] = $ids; 
            return $resp;            
        }
        return false;
    }
}
