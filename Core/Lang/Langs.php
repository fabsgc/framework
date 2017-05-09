<?php
/*\
 | ------------------------------------------------------
 | @file : Langs.php
 | @author : Fabien Beaujean
 | @description : Langs trait
 | @version : 3.0
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Lang;

use Gcs\Framework\Core\Config\Config;
use Gcs\Framework\Core\Http\Request\Request;

/**
 * Langs trait
 * @package Gcs\Framework\Core\Lang
 */
trait Langs {

    /**
     * @var $lang string
     */

    protected $lang = 'fr';

    /**
     * get the client language
     * @access public
     * @return string
     * @since  3.0
     * @package Gcs\Framework\Core\Lang
     */

    public function getLangClient() {
        if (!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) || !$_SERVER['HTTP_ACCEPT_LANGUAGE']) {
            return Config::config()['user']['output']['lang'];
        }
        else {
            $langcode = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
            $langcode = (!empty($langcode)) ? explode(";", $langcode) : $langcode;
            $langcode = (!empty($langcode['0'])) ? explode(",", $langcode['0']) : $langcode;
            $langcode = (!empty($langcode['0'])) ? explode("-", $langcode['0']) : $langcode;

            return $langcode['0'];
        }
    }

    /**
     * get lang
     * @access public
     * @return string
     * @since  3.0
     * @package Gcs\Framework\Core\Lang
     */

    public function getLang() {
        return Request::instance()->lang;
    }

    /**
     * set lang
     * @access public
     * @param string lang
     * @return void
     * @since  3.0
     * @package Gcs\Framework\Core\Lang
     */

    public function setLang($lang = '') {
        Request::instance()->lang = $lang;
    }

    /**
     * @access public
     * @param  $lang
     * @param $vars array : vars
     * @param int $template : use template syntax or not
     * @return string
     * @since  3.0
     * @package Gcs\Framework\Core\Lang
     */

    final public function useLang($lang, $vars = [], $template = Lang::USE_NOT_TPL) {
        return Lang::instance()->lang($lang, $vars, $template);
    }
}