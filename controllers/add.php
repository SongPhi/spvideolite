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
class SPVIDEOLITE_CTRL_Add extends OW_ActionController
{
	/**
	 * @var String embedForm
	 */
	private $embedForm = '';

	public function setEmbedForm($embedForm) {
		$this->embedForm = $embedForm;
	}

	public function index() {
		$this->assign('staticUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticUrl());
		$this->assign('embedForm', $this->embedForm);
        // $this->addComponent('dragDropCmp', OW::getClassInstance('SPVIDEOLITE_CMP_AjaxUpload'));

		// call selected module upload template
		$module = SPVIDEOLITE_BOL_Configs::getInstance()->get('processor');
		$func = 'add';
		if (SPVIDEOLITE_BOL_Configs::getInstance()->get('features.upload_video')) {
			$viewPath = SPVIDEOLITE_BOL_Service::callProcessorFunction($module, 'getViewPath', $this);
			$view = $func.'.html';
			$this->assign('uploadFormTpl', $viewPath. DS . $view);
			SPVIDEOLITE_BOL_Service::callProcessorFunction($module, $func, $this);
		}
	}

}