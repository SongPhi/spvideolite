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
	
	public function ajaxGetClip() {
		if (!OW::getRequest()->isAjax())
			die('Hacking Attempt!');

		$importService = SPVIDEO_CLASS_ImportService::getInstance();

		

		try {
			$video = $importService->checkClip($_POST['clipUrl']);

			$this->setTemplate( OW::getPluginManager()->getPlugin( 'spvideo' )->getCmpViewDir() . 'add_form.html' );
			$this->assign('auth_msg', null);

            $spVideoAddForm = new spVideoAddForm();

            $spVideoAddForm->setAction( OW::getRouter()->urlForRoute('spvideo.import') );

            $spVideoAddForm->setValues(array(
            	'title' => $video->title,
            	'description' => $video->description,
            	'tags' => implode(',', (array)$video->tags ),
        	));

            $this->addForm($spVideoAddForm);

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
        // $values = $this->getValues();
        // $clipService = VIDEO_BOL_ClipService::getInstance();

        // $clip = new VIDEO_BOL_Clip();
        // $clip->title = htmlspecialchars($values['title']);
        // $description = UTIL_HtmlTag::stripJs($values['description']);
        // $description = UTIL_HtmlTag::stripTags($description, array('frame', 'style'), array(), true);
        // $clip->description = $description;
        // $clip->userId = OW::getUser()->getId();

        // $clip->code = UTIL_HtmlTag::stripJs($values['code']);

        // $prov = new VideoProviders($clip->code);

        // $privacy = OW::getEventManager()->call(
        //     'plugin.privacy.get_privacy', 
        //     array('ownerId' => $clip->userId, 'action' => 'video_view_video')
        // );
                    
        // $clip->provider = $prov->detectProvider();
        // $clip->addDatetime = time();
        // $clip->status = 'approved';
        // $clip->privacy = mb_strlen($privacy) ? $privacy : 'everybody';

        // $eventParams = array('pluginKey' => 'video', 'action' => 'add_video');

        // if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === true )
        // {
        //     OW::getEventManager()->call('usercredits.track_action', $eventParams);
        // }
        
        // if ( $clipService->addClip($clip) )
        // {
        //     BOL_TagService::getInstance()->updateEntityTags($clip->id, 'video', $values['tags']);
            
        //     // Newsfeed
        //     $event = new OW_Event('feed.action', array(
        //         'pluginKey' => 'video',
        //         'entityType' => 'video_comments',
        //         'entityId' => $clip->id,
        //         'userId' => $clip->userId
        //     ));
            
        //     OW::getEventManager()->trigger($event);

        //     return array('result' => true, 'id' => $clip->id);
        // }

        return false;
    }
}