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

	use System\Facade\Facade;
	use System\Lang\Lang;
	use System\Config\Config;
	use System\Request\Request;
	use System\Orm\Entity\Multiple;
	use System\Exception\MissingConfigException;
	use System\Database\Database;

	trait resolve{

		/**
		 * when you want to use a lang, route, image, template, this method is used to resolve the right path
		 * the method use the instance of \system\config
		 * @access public
		 * @param $type string : type of the config
		 * @param $data string : ".gcs.lang" ".gcs/template/" "template"
		 * @throws MissingConfigException
		 * @return mixed
		 * @since 3.0
		 * @package System\General
		*/

		protected function resolve($type, $data){
			$request = Request::getInstance();
			$config  = Config::getInstance();
			
			if($type == RESOLVE_ROUTE || $type == RESOLVE_LANG){
				if(preg_match('#^((\.)([a-zA-Z0-9_-]+)(\.)(.+))#', $data, $matches)){
					$src = $matches[3];
					$data = preg_replace('#^(\.)('.preg_quote($src).')(\.)#isU', '', $data);
				}
				else{
					$src = $request->src;
				}

				return [$config->config[$type][$src], $data];
			}
			else{
				if(preg_match('#^((\.)([^(\/)]+)([(\/)]*)(.*))#', $data, $matches)){
					$src = $matches[3];
					$data = $matches[5];
				}
				else{
					if($request->src != '') {
						$src = $request->src;
					}
					else{
						$src = 'app';
					}
				}

				if($src == 'vendor'){
					return VENDOR_PATH.$data;
				}
				else{
					if(!isset($config->config[$type][$src])){
						throw new MissingConfigException('The section "'.$type.'"/".$src." does not exist in configuration');
					}
				}

				return $config->config[$type][$src].$data;
			}
		}

		/**
		 * when you want to use an image, file only, this method is used to resolve the right path
		 * the method override resolve()
		 * @access public
		 * @param $type string : type of the config
		 * @param $data string : ".gcs/template/" "template"
		 * @param $php boolean : because method return path, the framework wants to know if you want the html path or the php path
		 * @return string
		 * @since 3.0
		 * @package System\General
		*/

		protected function path($type, $data = '', $php = false){
			if($php == true){

				return $this->resolve($type, $data);
			}
			else{
				return FOLDER.$this->resolve($type, $data);
			}
		}
	}

	/**
	 * @method \System\Sql\Sql Sql
	 * @method \System\Security\Spam Spam
	 * @method \System\Lang\Lang Lang
	 * @method \System\Cron\Cron Cron
	 * @method \System\Cache\Cache Cache
	 * @method \System\Define\Define Define
	 * @method \System\Facade\FacadeHelper Helper
	 * @method \System\Facade\FacadeEntity Entity
	 * @method \System\Library\Library Library
	 * @method \System\Database\Database Database
	 * @method \System\Collection\Collection Collection
	 * @method \System\Form\Validation\Validation FormValidation
	 * @method \System\Controller\Injector\Injector Injector
	 * @method \System\Controller\Injector\Form FormInjector
	 * @method \System\Controller\Injector\Orm OrmInjector
	 * @method \System\Security\Firewall Firewall
	 * @method \System\Template\Template Template
	 * @method \System\Terminal\Terminal Terminal
	 * @method \System\AssetManager\AssetManager AssetManager
	 * @method \System\Orm\Validation\Validation OrmValidation
	 * @method \System\Orm\Entity\Multiple EntityMultiple
	 * @method \System\Template\TemplateParser TemplateParser
	 * @method \System\Config\Config Config
	 * @method \System\Request\Request Request
	 * @method \System\Request\Data RequestData
	 * @method \System\Response\Response Response
	 * @method \System\Profiler\Profiler Profiler
	 */
	trait facades{
		/**
		 * when you want to use a core or helper class, you can use the system of facades. It allow you tu instantiate
		 * @access public
		 * @param $name string : name of alias
		 * @param $arguments array
		 * @return object
		 * @since 3.0
		 * @package System\General
		*/

		public function __call($name, $arguments = []){
			$trace = debug_backtrace(0);

			$params = [];

			foreach ($arguments as $value) {
				array_push($params, $value);
			}

			return Facade::load($name, $params, $trace);
		}

		

		public function getStackTraceFacade($string){
			return $string;
		}
	}

	trait facadesEntity{
		protected $entity;
	}

	trait facadesHelper{
		protected $helper;
	}

	trait di{
		protected   $config;
		protected  $request;
		protected $response;
		protected $profiler;
	}

	trait error{

		/**
		 * add an error in the log
		 * @access public
		 * @param $error string : error
		 * @param $file string : file with error
		 * @param $line int : line with error
		 * @param $type string : type of error
		 * @param $log string : log file
		 * @return void
		 * @since 3.0
		*/

		public function addError($error, $file = __FILE__, $line = __LINE__, $type = ERROR_INFORMATION, $log = LOG_SYSTEM){
			if($log != LOG_HISTORY && $log != LOG_CRONS && $log != LOG_EVENT){
				if(LOG_ENABLED == true){
					if($log == LOG_SQL){
						$error = preg_replace('#([\t]{2,})#isU', "", $error);
					}

					$data = date("d/m/Y H:i:s : ",time()).'['.$type.'] file '.$file.' / line '.$line.' / '.$error;
					file_put_contents(APP_LOG_PATH.$log.EXT_LOG, $data."\n", FILE_APPEND | LOCK_EX);

					if((DISPLAY_ERROR_FATAL == true && $type == ERROR_FATAL) ||
					(DISPLAY_ERROR_EXCEPTION == true && $type == ERROR_EXCEPTION) || preg_match('#Exception#isU', $type) ||
					(DISPLAY_ERROR_ERROR == true && $type == ERROR_ERROR)){
						if(CONSOLE_ENABLED == MODE_HTTP)
							echo $data."\n<br />";
						else
							echo $data."\n";
					}
				}
			}
			else{
				file_put_contents(APP_LOG_PATH.$log.EXT_LOG, $error."\n", FILE_APPEND | LOCK_EX);
			}
		}

		/**
		 * add an hr line in the log
		 * @access public
		 * @param $log string : log file
		 * @return void
		 * @since 3.0
		*/

		public function addErrorHr($log = LOG_SYSTEM){
			if(LOG_ENABLED == true){
				file_put_contents(APP_LOG_PATH.$log.EXT_LOG, "#################### END OF EXECUTION OF http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']." ####################\n", FILE_APPEND | LOCK_EX);
			}
		}
	}

	trait ormFunctions{

		/**
		 * transform sql data in entity
		 * @access public
		 * @param $data array
		 * @param $entity string
		 * @return array
		 * @since 2.4
		*/

		final public function ormToEntity($data = [], $entity = ''){
			$entities = [];

			foreach($data as $value){
				if($entity != ''){
					$entityName = '\entity\\'.$entity;
					$entityObject = new $entityName(Database::getInstance()->db());

					foreach($value as $key => $value2){
						$entityObject->$key = $value2;
					}
				}
				else{
					$entityObject = new Multiple($data);
				}

				array_push($entities, $entityObject);
			}

			return $entities;
		}
	}

	trait langs{

		/**
		 * @var $lang string
		 */

		protected $lang  = LANG;

		/**
		 * get the client language
		 * @access public
		 * @return string
		 * @since 3.0
		*/

		public function getLangClient(){
			if(!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) || !$_SERVER['HTTP_ACCEPT_LANGUAGE'] ) { return LANG; }
			else{
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
		 * @since 3.0
		*/

		public function setLang($lang = ''){
			Request::getInstance()->lang = $lang;
		}

		/**
		 * get lang
		 * @access public
		 * @return string
		 * @since 3.0
		*/

		public function getLang(){
			return Request::getInstance()->lang;
		}

		/**
		 * @access public
		 * @param $lang
		 * @param $vars array : vars
		 * @param int $template : use template syntax or not
		 * @return string
		 * @since 3.0
		 */

		final public function useLang($lang, $vars = [], $template = Lang::USE_NOT_TPL){
			return Lang::getInstance()->lang($lang, $vars, $template);
		}
	}

	trait url{

		private $_routeAttribute = [];

		/**
		 * get an url
		 * @access public
		 * @param $name string : name of the url. With .app. before, it use the default route file. Width .x., it use the module x
		 * @param array $var
		 * @param $absolute boolean : add absolute link
		 * @internal param array $vars
		 * @return string
		 * @since 3.0
		*/

		public function getUrl($name, $var = [], $absolute = false){
			$routes = $this->resolve(RESOLVE_ROUTE, $name);

			if(isset($routes[0][''.$routes[1].''])){
				$route = $routes[0][''.$routes[1].''];

				$url = preg_replace('#\((.*)\)#isU', '<($1)>',  $route['url']);
				$urls = explode('<', $url);
				$result = '';
				$i=0;

				foreach($urls as $url){
					if(preg_match('#\)>#', $url)){
						if(count($var) > 0){
							if(isset($var[$i])){
								$result.= preg_replace('#\((.*)\)>#U', $var[$i], $url);
							}
							else{
								$result.= preg_replace('#\((.*)\)>#U', '', $url);
							}

							$i++;
						}
					}
					else{
						$result.=$url;
					}
				}

				$result = preg_replace('#\\\.#U', '.', $result);

				if(FOLDER != ''){
					if($absolute == false)
						return '/'.substr(FOLDER, 0, strlen(FOLDER)-1).$result;
					else
						return 'http://'.$_SERVER['HTTP_HOST'].FOLDER.$result;
				}
				else{
					if($absolute == false)
						return $result;
					else
						return 'http://'.$_SERVER['HTTP_HOST'].$result;
				}
			}
		}
	}

	trait singleton{
		/**
		 * singleton instance
		 * @var object
		*/

		public static $_instance = null;
	}

	interface EventListener {
		public function implementedEvents();
	}