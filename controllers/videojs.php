<?php

/**
* 
*/
class SPVIDEOLITE_CTRL_Videojs extends OW_ActionController
{

	public function embed() {
		
	}

    public function fbEmbed( array $params ) {

        $clip = SPVIDEOLITE_IMP_Facebook::getClipDetailByIdentifier($params['videoId']);

        $clipSources = array();

        $clipSources[] = array_pop($clip->files);
        $poster = array_pop($clip->thumbnails);
        $poster = $poster->url;

        $this->assign('staticUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticUrl());
        $this->assign('staticJsUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticJsUrl());
        $this->assign('staticCssUrl',OW::getPluginManager()->getPlugin( 'spvideolite' )->getStaticCssUrl());
        $this->assign('clipSources',$clipSources);

        $this->assign('poster',$poster);


        $this->setTemplate( OW::getPluginManager()->getPlugin( 'spvideolite' )->getCtrlViewDir() . 'videojs_fb_embed.html' );
        die($this->render());
    }
}

