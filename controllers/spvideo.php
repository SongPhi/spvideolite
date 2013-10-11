<?php

/**
* 
*/
class SPVIDEO_CTRL_Spvideo extends OW_ActionController
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

    public function proxy( array $params ) {
        $module = $params['module'];
        $func = $params['func'];
        $args = isset($params['args'])?$params['args']:'';
        $viewPath = SPVIDEO_DIR_PROCESSORS.DS.$module.DS.'views'.DS;
        $view = $func.'.html';
        include SPVIDEO_DIR_PROCESSORS.DS.$module.DS.$func.'.php';
        $this->assign('mod_content_tpl',$viewPath.$view);
    }
	
	public function ajaxGetClip() {
		if (!OW::getRequest()->isAjax()) { 
			throw new Redirect404Exception();
        }

		$importService = SPVIDEO_CLASS_ImportService::getInstance();		

		try {
			$video = $importService->checkClip($_POST['clipUrl']);

			$this->setTemplate( OW::getPluginManager()->getPlugin( 'spvideo' )->getCmpViewDir() . 'add_form.html' );
			$this->assign('auth_msg', null);

            $spVideoAddForm = new spVideoAddForm();

            $spVideoAddForm->setAction( OW::getRouter()->urlForRoute('spvideo.import') );

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
			);
			
			exit(json_encode($result));
		} catch (Exception $e) {
			$result = array(
				'error' => true,
				'errMsg' => $e->getMessage()
			);
			exit(json_encode($result));
		}
		
	}

    function embed(array $params) {
        
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
        $clip->thumbUrl = $values['thumbnail'];

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
}