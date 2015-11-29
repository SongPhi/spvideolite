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


class SPVIDEOLITE_CTRL_Categories extends OW_ActionController
{
	private $menu;
	private $showAddButton;

	function __construct() {
		$this->menu = $this->getMenu();
		$this->menu = $this->menu->render();
		$this->showAddButton = true;

		// check authentication
		$status = BOL_AuthorizationService::getInstance()->getActionStatus('video', 'add');

        if ( $status['status'] == BOL_AuthorizationService::STATUS_AVAILABLE )
        {
            $script = '$("#btn-add-new-video").click(function(){
                document.location.href = ' . json_encode(OW::getRouter()->urlFor('VIDEO_CTRL_Add', 'index')) . ';
            });';

            OW::getDocument()->addOnloadScript($script);
        }
        else if ( $status['status'] == BOL_AuthorizationService::STATUS_PROMOTED )
        {
            $script = '$("#btn-add-new-video").click(function(){
                OW.authorizationLimitedFloatbox('.json_encode($status['msg']).');
            });';

            OW::getDocument()->addOnloadScript($script);
        }
        else
        {
            $this->showAddButton = false;
        }
	}

	/**
     * Returns menu component
     *
     * @return BASE_CMP_ContentMenu
     */
    private function getMenu()
    {
        $validLists = array('featured', 'latest', 'toprated', 'categories', 'tagged');
        $classes = array('ow_ic_push_pin', 'ow_ic_clock', 'ow_ic_star', 'ow_ic_folder', 'ow_ic_tag');

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

	public function index() {
		$this->assign('videoMenu', $this->menu);
		$this->assign('showAddButton', $this->showAddButton);
		OW::getDocument()->setHeading(OW::getLanguage()->text('video', 'page_title_browse_video'));
        OW::getDocument()->setHeadingIconClass('ow_ic_video');
	}

	public function videoList() {
		$this->assign('videoMenu', $this->menu);
		$this->assign('showAddButton', $this->showAddButton);
		OW::getDocument()->setHeading(OW::getLanguage()->text('video', 'page_title_browse_video'));
        OW::getDocument()->setHeadingIconClass('ow_ic_video');
	}
}