<?php
	/*\
	 | ------------------------------------------------------
	 | @file : General.php
	 | @author : fab@c++
	 | @description : functions used everywhere
	 | @version : 3.0
	 | ------------------------------------------------------
	\*/

	namespace System\General;

	use System\AssetManager\AssetManager;
	use System\Cache\Cache;
	use System\Collection\Collection;
	use System\Config\Config;
	use System\Controller\Injector\Form;
	use System\Controller\Injector\Injector;
	use System\Controller\Injector\Orm;
	use System\Cron\Cron;
	use System\Database\Database;
	use System\Define\Define;
	use System\Exception\MissingConfigException;
	use System\Facade\Facade;
	use System\Facade\FacadeEntity;
	use System\Facade\FacadeHelper;
	use System\Form\Validation\Validation;
	use System\Lang\Lang;
	use System\Library\Library;
	use System\Orm\Entity\Multiple;
	use System\Profiler\Profiler;
	use System\Request\Data;
	use System\Request\Request;
	use System\Response\Response;
	use System\Security\Firewall;
	use System\Security\Spam;
	use System\Sql\Sql;
	use System\Template\Template;
	use System\Template\TemplateParser;
	use System\Terminal\Terminal;
	use System\Url\Url;

	/**
	 * Trait resolve
	 * @package System\General
	 */

	trait resolve {

		/**
		 * when you want to use a lang, route, image, template, this method is used to resolve the right path
		 * the method use the instance of \system\config
		 * @access  public
		 * @param $type string : type of the config
		 * @param $data string : ".gcs.lang" ".gcs/template/" "template"
		 * @throws MissingConfigException
		 * @return mixed
		 * @since   3.0
		 * @package System\General
		 */

		protected function resolve($type, $data) {
			return self::resolveStatic($type, $data);
		}

		/**
		 * when you want to use a lang, route, image, template, this method is used to resolve the right path
		 * the method use the instance of \system\config
		 * @access  public
		 * @param $type string : type of the config
		 * @param $data string : ".gcs.lang" ".gcs/template/" "template"
		 * @throws MissingConfigException
		 * @return mixed
		 * @since   3.0
		 * @package System\General
		 */

		protected static function resolveStatic($type, $data) {
			$request = Request::getInstance();
			$config = Config::getInstance();

			if ($type == RESOLVE_ROUTE || $type == RESOLVE_LANG) {
				if (preg_match('#^((\.)([a-zA-Z0-9_-]+)(\.)(.+))#', $data, $matches)) {
					$src = $matches[3];
					$data = preg_replace('#^(\.)(' . preg_quote($src) . ')(\.)#isU', '', $data);
				}
				else {
					$src = $request->src;
				}

				return [$config->config[$type][$src], $data];
			}
			else {
				if (preg_match('#^((\.)([^(\/)]+)([(\/)]*)(.*))#', $data, $matches)) {
					$src = $matches[3];
					$data = $matches[5];
				}
				else {
					if ($request->src != '') {
						$src = $request->src;
					}
					else {
						$src = 'app';
					}
				}

				if ($src == 'vendor') {
					return VENDOR_PATH . $data;
				}
				else {
					if (!isset($config->config[$type][$src])) {
						throw new MissingConfigException('The section "' . $type . '"/".$src." does not exist in configuration');
					}
				}

				return $config->config[$type][$src] . $data;
			}
		}

		/**
		 * when you want to use an image, file only, this method is used to resolve the right path
		 * the method override resolve()
		 * @access  public
		 * @param $type string : type of the config
		 * @param $data string : ".gcs/template/" "template"
		 * @param $php  boolean : because method return path, the framework wants to know if you want the html path or the php path
		 * @return string
		 * @since   3.0
		 * @package System\General
		 */

		protected function path($type, $data = '', $php = false) {
			if ($php == true) {
				return $this->resolve($type, $data);
			}
			else {
				return FOLDER . $this->resolve($type, $data);
			}
		}
	}

	/**
	 * Trait facades
	 * @method Sql Sql
	 * @method Spam Spam
	 * @method Lang Lang
	 * @method Cron Cron
	 * @method Cache Cache
	 * @method Define Define
	 * @method FacadeHelper Helper
	 * @method FacadeEntity Entity
	 * @method Library Library
	 * @method Database Database
	 * @method Collection Collection
	 * @method Validation FormValidation
	 * @method Injector Injector
	 * @method Form FormInjector
	 * @method Orm OrmInjector
	 * @method Firewall Firewall
	 * @method Template Template
	 * @method Terminal Terminal
	 * @method AssetManager AssetManager
	 * @method Validation OrmValidation
	 * @method Multiple EntityMultiple
	 * @method TemplateParser TemplateParser
	 * @method Config Config
	 * @method Request Request
	 * @method Data RequestData
	 * @method Response Response
	 * @method Profiler Profiler
	 * @package System\General
	 */

	trait facades {

		/**
		 * when you want to use a core or helper class, you can use the system of facades. It allow you tu instantiate
		 * @access  public
		 * @param $name      string : name of alias
		 * @param $arguments array
		 * @return object
		 * @since   3.0
		 * @package System\General
		 */

		public function __call($name, $arguments = []) {
			$trace = debug_backtrace(0);
			$params = [];

			foreach ($arguments as $value) {
				array_push($params, $value);
			}

			return Facade::load($name, $params, $trace);
		}

		public function getStackTraceFacade($string) {
			return $string;
		}
	}

	/**
	 * Trait facadesEntity
	 * @package System\General
	 */

	trait facadesEntity {
		protected $entity;
	}

	/**
	 * Trait facadesHelper
	 * @package System\General
	 */

	trait facadesHelper {
		protected $helper;
	}
	
	/**
	 * Trait error
	 * @package System\General
	 */

	trait error {

		/**
		 * add an error in the log
		 * @access public
		 * @param $error string : error
		 * @param $file  string : file with error
		 * @param $line  int : line with error
		 * @param $type  string : type of error
		 * @param $log   string : log file
		 * @return void
		 * @since  3.0
		 */

		public function addError($error, $file = __FILE__, $line = __LINE__, $type = ERROR_INFORMATION, $log = LOG_SYSTEM) {
			if ($log != LOG_HISTORY && $log != LOG_CRONS && $log != LOG_EVENT) {
				if (LOG_ENABLED == true) {
					if ($log == LOG_SQL) {
						$error = preg_replace('#([\t]{2,})#isU', "", $error);
					}

					$data = date("d/m/Y H:i:s : ", time()) . '[' . $type . '] file ' . $file . ' / line ' . $line . ' / ' . $error;
					file_put_contents(APP_LOG_PATH . $log . EXT_LOG, $data . "\n", FILE_APPEND | LOCK_EX);

					if ((DISPLAY_ERROR_FATAL == true && $type == ERROR_FATAL) ||
						(DISPLAY_ERROR_EXCEPTION == true && $type == ERROR_EXCEPTION) || preg_match('#Exception#isU', $type) ||
						(DISPLAY_ERROR_ERROR == true && $type == ERROR_ERROR)
					) {
						if (CONSOLE_ENABLED == MODE_HTTP) {
							echo $data . "\n<br />";
						}
						else {
							echo $data . "\n";
						}
					}
				}
			}
			else {
				file_put_contents(APP_LOG_PATH . $log . EXT_LOG, $error . "\n", FILE_APPEND | LOCK_EX);
			}
		}

		/**
		 * add an hr line in the log
		 * @access public
		 * @param $log string : log file
		 * @return void
		 * @since  3.0
		 */

		public function addErrorHr($log = LOG_SYSTEM) {
			if (LOG_ENABLED == true) {
				file_put_contents(APP_LOG_PATH . $log . EXT_LOG, "#################### END OF EXECUTION OF http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . " ####################\n", FILE_APPEND | LOCK_EX);
			}
		}
	}

	/**
	 * Trait ormFunctions
	 * @package System\General
	 */

	trait ormFunctions {
		/**
		 * transform sql data in entity
		 * @access public
		 * @param $data   array
		 * @param $entity string
		 * @return array
		 * @since  2.4
		 */

		final public function ormToEntity($data = [], $entity = '') {
			$entities = [];

			foreach ($data as $value) {
				if ($entity != '') {
					$entityName = '\entity\\' . $entity;
					$entityObject = new $entityName(Database::getInstance()->db());

					foreach ($value as $key => $value2) {
						$entityObject->$key = $value2;
					}
				}
				else {
					$entityObject = new Multiple($data);
				}

				array_push($entities, $entityObject);
			}

			return $entities;
		}
	}

	/**
	 * Trait langs
	 * @package System\General
	 */

	trait langs {

		/**
		 * @var $lang string
		 */

		protected $lang = LANG;

		/**
		 * get the client language
		 * @access public
		 * @return string
		 * @since  3.0
		 */

		public function getLangClient() {
			if (!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) || !$_SERVER['HTTP_ACCEPT_LANGUAGE']) {
				return LANG;
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
			Request::getInstance()->lang = $lang;
		}

		/**
		 * get lang
		 * @access public
		 * @return string
		 * @since  3.0
		 */

		public function getLang() {
			return Request::getInstance()->lang;
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
			return Lang::getInstance()->lang($lang, $vars, $template);
		}
	}

	/**
	 * Trait singleton
	 * @package System\General
	 */

	trait singleton {
		/**
		 * singleton instance
		 * @var object
		 */

		public static $_instance = null;
	}

	/**
	 * Interface EventListener
	 * @package System\General
	 */

	interface EventListener {
		public function implementedEvents();
	}