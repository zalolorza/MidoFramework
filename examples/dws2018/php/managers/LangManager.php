<?php

class LangManager {

    public static function get_other(){
        if(!function_exists('icl_get_languages')) return false;
        $langs = icl_get_languages('skip_missing=0');
		$langs = array_filter($langs,function($lan){
				if(!$lan['active']) return $lan;
		});
		
		$lang = reset($langs);

		return array(
				'name' => $lang['native_name'],
				'link' => $lang['url'],
				'ICL_LANGUAGE_CODE' => ICL_LANGUAGE_CODE
			);
    }

    public static function get_short_texts(){

        return array(
            "comingsoon" => __('Coming Soon','lemeridien'),
            "prevelement" => __('Previous element','lemeridien'),
            "nextelement" => __('Next element','lemeridien'),
            "clickanddrag" => __('Click and drag','lemeridien'),
            "replay" => __('Replay','lemeridien')
        );

    }

    public static function is_default(){
        if(!function_exists('icl_get_languages')) return true;
        global $sitepress;
        $sitepress->get_default_language();
        if(ICL_LANGUAGE_CODE == $sitepress->get_default_language()){
            return true;
        } else {
            return false;
        }
    }
}