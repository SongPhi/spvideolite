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

 /**
* 
*/
class SPVIDEOLITE_CTRL_Spvideo extends OW_ActionController
{

	public function index() {
		
	}

	public function myVideo() {

	}

    public function import() {
        if (!OW::getRequest()->isPost()) { 
            throw new Redirect404Exception();
        }

        $spVideoAddForm = new spVideoAddForm();

        if ( $spVideoAddForm->isValid($_POST) ) {
            $language = OW::getLanguage();
            $values = $spVideoAddForm->getValues();
            $code = VIDEO_BOL_ClipService::getInstance()->validateClipCode($values['code']);
            
            if ( !BOL_TextFormatService::getInstance()->isCodeResourceValid($code) ) {
                OW::getFeedback()->warning($language->text('video', 'resource_not_allowed'));
                $this->redirect(OW::getRouter()->urlFor('VIDEO_CTRL_Add', 'index'));
            }
            
            $res = $spVideoAddForm->process();
            OW::getFeedback()->info($language->text('video', 'clip_added'));
            $this->redirect(OW::getRouter()->urlForRoute('view_clip', array('id' => $res['id'])));
        } else {
            $this->redirect(OW::getRouter()->urlFor('VIDEO_CTRL_Add', 'index'));
        }
    }

    public function jsTextKeys() {
        ob_clean();
        header("Content-type: text-javascript");
        $language = OW::getLanguage();
        die();
    }

    public function proxy( array $params ) {
        $module = $params['module'];
        $func = $params['func'];
        $args = isset($params['args'])?$params['args']:'';        
        SPVIDEOLITE_BOL_Service::callProcessorFunction($module, $func, $this);
    }
	
	public function ajaxGetClip() {
		if (!OW::getRequest()->isAjax()) { 
			throw new Redirect404Exception();
        }

		$importService = SPVIDEOLITE_CLASS_ImportService::getInstance();		

		try {
			$video = $importService->checkClip($_REQUEST['clipUrl']);

			$this->setTemplate( OW::getPluginManager()->getPlugin( 'spvideolite' )->getCmpViewDir() . 'add_form.html' );
			$this->assign('auth_msg', null);

            $spVideoAddForm = new spVideoAddForm();

            $spVideoAddForm->setAction( OW::getRouter()->urlForRoute('spvideolite.import') );

            $thumbnail = '';

            foreach ($video->thumbnails as $thumb) {
                if (!empty($thumb->url)) {
                    $thumbnail = $thumb->url;
                    break;
                }
            }

            $spVideoAddForm->setValues(array(
            	'title' => $video->title,
            	'description' => $video->description,
                'code' => $video->embedCode,
            	'tags' => implode(',', (array)$video->tags ),
                'thumbnail' => $thumbnail
        	));

            $this->addForm($spVideoAddForm);
            $this->assign('thumbUrl',$thumbnail);

			$formHtml = base64_encode( $this->render() );

			$result = array(
				'error' => false,
				'formHtml' => $formHtml,
                'script' => base64_encode( $spVideoAddForm->getFormJs() )
			);

            if (isset($_REQUEST['external'])) {
                die( json_encode( array (
                    'title' => $video->title,
                    'description' => $video->description,
                    'code' => $video->embedCode,
                    'tags' => implode(',', (array)$video->tags ),
                    'thumbnail' => $thumbnail,
                    'error' => false
                ),true) );
            }

			exit(json_encode($result));
		} catch (Exception $e) {
            $msg = $e->getMessage();
			$result = array(
				'error' => true,
				'errMsg' => $msg
			);
			exit(json_encode($result));
		}
		
	}

    private function getVideoMenu()
    {
        $validLists = array('featured', 'latest', 'toprated', 'categories','tagged');
        $classes = array('ow_ic_push_pin', 'ow_ic_clock', 'ow_ic_star', 'ow_ic_folder','ow_ic_tag');

        if ( !VIDEO_BOL_ClipService::getInstance()->findClipsCount('featured') )
        {
            array_shift($validLists);
            array_shift($classes);
        }

        $language = OW::getLanguage();

        $menuItems = array();

        $order = 0;
        foreach ( $validLists as $type )
        {
            $item = new BASE_MenuItem();
            if ($type!='categories') {
                $item->setLabel($language->text('video', 'menu_' . $type));
                $item->setUrl(OW::getRouter()->urlForRoute('view_list', array('listType' => $type)));
            } else {
                $item->setLabel($language->text('spvideolite', 'menu_' . $type));
                $item->setUrl(OW::getRouter()->urlForRoute('spvideolite.categories'));
            }
            $item->setKey($type);
            $item->setIconClass($classes[$order]);
            $item->setOrder($order);

            array_push($menuItems, $item);

            $order++;
        }

        $menu = new BASE_CMP_ContentMenu($menuItems);

        return $menu;
    }

    public function categories() {
        OW::getDocument()->setHeading(OW::getLanguage()->text('video', 'page_title_browse_video'));
        OW::getDocument()->setHeadingIconClass('ow_ic_video');
        OW::getDocument()->setTitle(OW::getLanguage()->text('spvideolite', 'meta_title_video_categories'));
        $this->addComponent('videoMenu', $this->getVideoMenu());
    }

    function embed(array $params) {
        $dbo = OW::getDbo();
        $sources = $dbo->queryForList("SELECT * FROM `".OW_DB_PREFIX."spvideo_clip_format` WHERE clipId=(SELECT id FROM `".OW_DB_PREFIX."spvideo_clip` WHERE videoId=".$params['videoId'].")");
        $supplied = '';
        foreach ($sources as $index => $value) {
            $value['url'] = str_replace("baseurl:/", OW::getRouter()->getBaseUrl(), $value['url']);
            $sources[$index] = $value;
            $supplied .= ','.$value['format'];
        }
        $supplied = ltrim($supplied,',');
        $this->setTemplate( OW::getPluginManager()->getPlugin( 'spvideolite' )->getCtrlViewDir() . 'spvideo_embed.html' );
        $this->assign('staticUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticUrl());
        $this->assign('staticJsUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticJsUrl());
        $this->assign('staticCssUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticCssUrl());
        $this->assign('sources',$sources);
        $this->assign('supplied',$supplied);
        die($this->render());
    }

    
}

/**
 * SPVideo add form class
 */
class spVideoAddForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('SPVideoAddForm');

        $language = OW::getLanguage();

        // title Field
        $titleField = new TextField('title');
        $titleField->setRequired(true);
        $this->addElement($titleField->setLabel($language->text('video', 'title')));

        // description Field
        $descField = new WysiwygTextarea('description');
        $this->addElement($descField->setLabel($language->text('video', 'description')));

        // code Field
        $codeField = new Textarea('code');
        $codeField->setRequired(true);
        $this->addElement($codeField->setLabel($language->text('video', 'code')));

        $tagsField = new TagsInputField('tags');
        $this->addElement($tagsField->setLabel($language->text('video', 'tags')));

        $thumbnailField = new HiddenField('thumbnail');
        $this->addElement($thumbnailField);

        $submit = new Submit('add');
        $submit->setValue($language->text('video', 'btn_add'));
        $this->addElement($submit);
    }

    /**
     * Adds video clip
     *
     * @return boolean
     */
    public function process()
    {
        $values = $this->getValues();
        $clipService = VIDEO_BOL_ClipService::getInstance();

        $clip = new VIDEO_BOL_Clip();
        $clip->title = htmlspecialchars($values['title']);
        $description = UTIL_HtmlTag::stripJs($values['description']);
        $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        $description = nl2br($description, true);
        $clip->description = $description;
        $clip->userId = OW::getUser()->getId();
        $clip->thumbUrl = preg_replace("#(http://|https://)#i", "//",$values['thumbnail']);

        $clip->code = UTIL_HtmlTag::stripJs($values['code']);

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
        
        if ( $clipService->addClip($clip) )
        {
            BOL_TagService::getInstance()->updateEntityTags($clip->id, 'video', $values['tags']);
            
            // Newsfeed
            $event = new OW_Event('feed.action', array(
                'pluginKey' => 'video',
                'entityType' => 'video_comments',
                'entityId' => $clip->id,
                'userId' => $clip->userId
            ));
            
            OW::getEventManager()->trigger($event);

            return array('result' => true, 'id' => $clip->id);
        }

        return false;
    }

    public function getFormJs() {
        $formElementJS = '';

        /* @var $element FormElement */
        foreach ( $this->elements as $element )
        {
            $formElementJS .= $element->getElementJs() . PHP_EOL;
            $formElementJS .= "form.addElement(formElement);" . PHP_EOL;
        }

        $formInitParams = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'reset' => $this->getAjaxResetOnSuccess(),
            'ajax' => $this->isAjax(),
            'ajaxDataType' => $this->getAjaxDataType(),
            'validateErrorMessage' => $this->emptyElementsErrorMessage,
        );

        $jsString = " var form = new OwForm(" . json_encode($formInitParams) . ");window.owForms[form.name] = form;
            " . PHP_EOL . $formElementJS . "

            if ( form.form ) 
            {
                $(form.form).bind( 'submit', {form:form},
                        function(e){
                            return e.data.form.submitForm();
                        }
                );
                        }
                        
                        OW.trigger('base.onFormReady.' + form.name, [], form);
                        OW.trigger('base.onFormReady', [form]);
        ";

        foreach ( $this->bindedFunctions as $bindType => $binds )
        {
            if ( empty($binds) )
            {
                continue;
            }

            foreach ( $binds as $function )
            {
                $jsString .= "form.bind('" . trim($bindType) . "', " . $function . ");";
            }
        }

        return $jsString;
    }
}