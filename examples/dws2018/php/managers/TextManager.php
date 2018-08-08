<?php

class TextManager extends MidoManager {


    /****** LOAD STRINGS JS */
 
    public static $STRINGS = array();

    function stop___init() {
        
     
        add_filter( 'gettext', function($translated_text, $text, $domain){
            
            TextManager::$STRINGS[$text] =  $translated_text;
            return $translated_text;
  
        }, 20, 3 );

    }

    function stop__action_wp_enqueue_scripts_10(){
        wp_localize_script( 'app_js', '__', TextManager::$STRINGS );
    }

  public static function shorten_string_words($string, $wordsreturned) {
      $retval = $string;
      $string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $string);
      $array = explode(" ", $string);
      if (count($array)<=$wordsreturned)
      {
        $retval = $string;
      }
      else
      {
        array_splice($array, $wordsreturned);
        $retval = implode(" ", $array);
      }
      return $retval;
    }

  public static function shorten_string($string,$characters,$fullwords=true) {

      $fullwords = !$fullwords;

      $new_string = self::truncate($string,$characters,array('html' => true,'ending'=>'...', 'exact' => $fullwords));
    

      return $new_string;

     

  }

  public function get_links($string){

      $hasLinks = preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $string, $match);

      if($hasLinks){
        return $match[0][0];
      } else {
        return false;
      }
  }


  public static function removeLineBreaks (&$string){
      $string = str_replace("\n", " ", $string);
      $string = str_replace("<br>", " ", $string);
      return $string;
  }

  public static function startsWith($haystack, $needle){
       $length = strlen($needle);
       return (substr($haystack, 0, $length) === $needle);
  }

  public static function endsWith($haystack, $needle){
      $length = strlen($needle);
      if ($length == 0) {
          return true;
      }

      return (substr($haystack, -$length) === $needle);
  }

  public static function fixDOM($string){

  }

  public static function truncate($text, $length = 100, $options = array()) {
    $default = array(
        'ending' => '...', 'exact' => true, 'html' => false
    );
    $options = array_merge($default, $options);
    extract($options);

    if ($html) {
        if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        $totalLength = mb_strlen(strip_tags($ending));
        $openTags = array();
        $truncate = '';

        preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
        foreach ($tags as $tag) {
            if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                    array_unshift($openTags, $tag[2]);
                } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                    $pos = array_search($closeTag[1], $openTags);
                    if ($pos !== false) {
                        array_splice($openTags, $pos, 1);
                    }
                }
            }
            $truncate .= $tag[1];

            $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
            if ($contentLength + $totalLength > $length) {
                $left = $length - $totalLength;
                $entitiesLength = 0;
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entitiesLength <= $left) {
                            $left--;
                            $entitiesLength += mb_strlen($entity[0]);
                        } else {
                            break;
                        }
                    }
                }

                $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                break;
            } else {
                $truncate .= $tag[3];
                $totalLength += $contentLength;
            }
            if ($totalLength >= $length) {
                break;
            }
        }
    } else {
        if (mb_strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
        }
    }
    if (!$exact) {
        $spacepos = mb_strrpos($truncate, ' ');
        if (isset($spacepos)) {
            if ($html) {
                $bits = mb_substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                if (!empty($droppedTags)) {
                    foreach ($droppedTags as $closingTag) {
                        if (!in_array($closingTag[1], $openTags)) {
                            array_unshift($openTags, $closingTag[1]);
                        }
                    }
                }
            }
            $truncate = mb_substr($truncate, 0, $spacepos);
        }
    }
    $truncate .= $ending;

    if ($html) {
        foreach ($openTags as $tag) {
            $truncate .= '</'.$tag.'>';
        }
    }

    return $truncate;
}
}