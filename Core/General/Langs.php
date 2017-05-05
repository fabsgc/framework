<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Langs.php
	 | @author : Fabien Beaujean
	 | @description : Langs trait
	 | @version : 3.0
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\General;

	use Gcs\Framework\Core\Config\Config;
	use Gcs\Framework\Core\Request\Request;
	use Gcs\Framework\Core\Lang\Lang;

	/**
	 * Langs trait
	 * @package Gcs\Framework\Core\General
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
		 * set lang
		 * @access public
		 * @param string lang
		 * @return void
		 * @since  3.0
		 */

		public function setLang($lang = '') {
			Request::instance()->lang = $lang;
		}

		/**
		 * get lang
		 * @access public
		 * @return string
		 * @since  3.0
		 */

		public function getLang() {
			return Request::instance()->lang;
		}

		/**
		 * @access public
		 * @param  $lang
		 * @param $vars array : vars
		 * @param int $template : use template syntax or not
		 * @return string
		 * @since  3.0
		 */

		final public function useLang($lang, $vars = [], $template = Lang::USE_NOT_TPL) {
			return Lang::instance()->lang($lang, $vars, $template);
		}
	}