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

	use System\Exception\MissingConfigException;
	use System\Lang\Lang;

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
		 * @package system
		*/

		protected function resolve($type, $data){
			$request = self::Request();
			$config  = self::Config();


			if($type == RESOLVE_ROUTE || $type == RESOLVE_LANG){
				if(preg_match('#^((\.)([a-zA-Z0-9_-]+)(\.)(.+))#', $data, $matches)){
					$src = $matches[3];
					$data = preg_replace('#^(\.)('.preg_quote($src).')(\.)#isU', '', $data);
				}
				else{
					$src = $request->src;
				}

				return array($config->config[$type][$src], $data);
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
		 * @package system
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
		 * @package system
		*/

		public function __call($name, $arguments = array()){
			$trace = debug_backtrace(0);

			$params = array();

			foreach ($arguments as $value) {
				array_push($params, $value);
			}

			return \System\Facade\Facade::load($name, $params, $trace);
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

		final public function ormToEntity($data = array(), $entity = ''){
			$entities = array();

			foreach($data as $value){
				if($entity != ''){
					$entityName = '\entity\\'.$entity;
					$entityObject = new $entityName($this->db);

					foreach($value as $key => $value2){
						$entityObject->$key = $value2;
					}
				}
				else{
					$entityObject = self::EntityMultiple($data);
				}

				array_push($entities, $entityObject);
			}

			return $entities;
		}
	}

	trait langs{
		protected $lang  = LANG;
		protected $langInstance;

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
			self::Request()->lang = $lang;
			$this->langInstance->setLang(self::Request()->lang);
		}

		/**
		 * get lang
		 * @access public
		 * @return string
		 * @since 3.0
		*/

		public function getLang(){
			return self::Request()->lang;
		}

		/**
		 * @access public
		 * @param $lang
		 * @param $vars array : vars
		 * @param int $template : use template syntax or not
		 * @internal param string $langs : sentence name
		 * @return string
		 * @since 3.0
		 */

		final public function useLang($lang, $vars = array(), $template = Lang::USE_NOT_TPL){
			return $this->langInstance->lang($lang, $vars, $template);
		}

		/**
		 * create new instance of \system\Lang\Lang
		 * @access public
		 * @return void
		 * @since 3.0
		*/

		final protected function _createlang(){
			$this->langInstance = self::Lang();
		}
	}

	trait url{

		private $_routeAttribute = array();

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

		public function getUrl($name, $var = array(), $absolute = false){
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

				if($absolute == false)
					return FOLDER.$result;
				else
					return 'http://'.$_SERVER['HTTP_HOST'].FOLDER.$result;
			}
		}
	}

	trait singleton{
		/**
		 * singleton instance
		 * @var array Response
		*/

		public static $_instance = null;
	}

	interface EventListener {
		public function implementedEvents();
	}