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
 * @package ow_plugins.spvideolite.controllers
 * @since 1.6.1
 */
class SPVIDEOLITE_CTRL_AjaxUpload extends OW_ActionController
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function init()
    {
        parent::init();
        
        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }
        
        if ( !OW::getUser()->isAuthorized('video', 'add') )
        {
            $this->returnResponse(array('status' => self::STATUS_ERROR, 'result' => FALSE, 'msg' => OW::getLanguage()->text('spvideolite', 'auth_upload_permissions')));
        }
    }
    
    protected function getEntity( $params )
    {
        if ( empty($params["entityType"]) || empty($params["entityId"]) )
        {
            $params["entityType"] = "user";
            $params["entityId"] = OW::getUser()->getId();
        }
        
        return array($params["entityType"], $params["entityId"]);
    }
    
    public function ajaxSubmitVideos( $params )
    {
        if (!OW::getRequest()->isAjax()) { 
			throw new Redirect404Exception();
        }
        $language = OW::getLanguage();
        $status = BOL_AuthorizationService::getInstance()->getActionStatus('video', 'add');
        // Check balance video count == balanse count. Delete other video
        if ( $status['status'] != BOL_AuthorizationService::STATUS_AVAILABLE )
        {
            $this->returnResponse(array('result' => FALSE, 'msg' => $status['msg']));
        }
        $userId = OW::getUser()->getId();

		try 
        {
            $redirect_url = OW::getRouter()->urlForRoute('video_list_index');
            
            $form = new SPVIDEOLITE_CLASS_AjaxUploadForm('user', $userId);
        
            if ( !$form->isValid($_POST) )
            {
                $error = $form->getErrors();
                $resp = array('result' => FALSE);
                $resp['msg'] = OW::getLanguage()->text('spvideolite', 'video_add_error');
                $this->returnResponse($resp);
            }else
            {
                $resp = array('result' => TRUE);
                $resp['msg'] = $language->text('video', 'clip_added');
                $resp['url']= $redirect_url;
                list($entityType, $entityId) = $this->getEntity($params);
                $language = OW::getLanguage();
                $values = $form->getValues();
                $resp = $form->process();
                if($resp['result'])
                {
                    $resp['url']= $redirect_url;
                }        
                else
                {
                    $resp = array('result' => FALSE);
                    $resp['msg'] = $language->text('spvideolite', 'video_add_error');
                }
                $this->returnResponse($resp);
                
            }
		} catch (Exception $e) {
            $msg = $e->getMessage();
			$result = array(
				'error' => true,
				'errMsg' => $msg
			);
			exit(json_encode($result));
		}
    }
    
    public function ajaxAdd( array $params = array() )
    {
        $status = BOL_AuthorizationService::getInstance()->getActionStatus('video', 'add');
        // Check balance video count == balanse count. Delete other video
        if ( $status['status'] != BOL_AuthorizationService::STATUS_AVAILABLE )
        {
            $this->returnResponse(array('result' => FALSE, 'msg' => $status['msg']));
        }

        $userId = OW::getUser()->getId();
        $importService = SPVIDEOLITE_CLASS_ImportService::getInstance();		
        $video = $importService->checkClip($_REQUEST['clipUrl']);
        
        $thumbnail = '';

        foreach ($video->thumbnails as $thumb) {
            if (!empty($thumb->url)) {
                $thumbnail = $thumb->url;
                break;
            }
        }
        
        $result = array(
                        'status' => self::STATUS_SUCCESS,
                        'id' => 1,
                        'fileUrl' => $thumbnail,
                        'title' => $video->title,
                        'description' => $video->description,
                        'code' => $video->embedCode,
                        'tags' => implode(',', (array)$video->tags ),
                        'thumbnail' => $thumbnail
                        );
        if ( (!empty($video->embedCode)))
        {
            $this->returnResponse($result);
        }
        else
        {
            $this->returnResponse(array('status' => self::STATUS_ERROR, 'msg' => OW::getLanguage()->text('spvideolite', 'no_video_added')));
        }
    }


    
    public function ajaxDelete( array $params = array() )
    {
        if ( !empty($_POST['id']) )
        {
        }
        exit();
    }
    
    private function returnResponse( $response )
    {
        ob_end_clean();

        exit(json_encode($response));
    }
}
