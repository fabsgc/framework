<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Engine.php
	 | @author : fab@c++
	 | @description : engine
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Engine;

	use System\Controller\Injector\Injector;
	use System\General\di;
	use System\General\error;
	use System\General\langs;
	use System\General\facades;
	use System\General\resolve;
	use System\Request\Request;
	use System\Request\Auth;
	use System\Response\Response;
	use System\Profiler\Profiler;
	use System\Config\Config;
	use System\Exception\ErrorHandler;
	use System\Exception\Exception;
	use System\Router\Router;
	use System\Router\Route;

	class Engine{
		use error, langs, facades, resolve, di;

		/**
		 * @var \System\Controller\Controller
		*/

		protected $_controller;

		/**
		 * @var \System\Router\Route
		*/

		protected $_route = false;

		/**
		 * @var array
		*/

		protected $_db = null;

		/**
		 * constructor
		 * @access public
		 * @param $mode integer
		 * @since 3.0
		 * @package System\Engine
		*/

		public function __construct ($mode = MODE_HTTP){
			if (!defined('CONSOLE_ENABLED'))
				define('CONSOLE_ENABLED', $mode);

			$this->_setLog();
			$this->_setErrorHandler();
			$this->request  = Request::getInstance();
			$this->response = Response::getInstance();
			$this->profiler = Profiler::getInstance();
			$this->config   = Config::getInstance();
			                  Injector::getInstance();
		}

		/**
		 * initialization of the engine
		 * @access public
		 * @param $db array
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		public function init($db){
			if(MAINTENANCE == false){
				date_default_timezone_set(TIMEZONE);
				$this->_setEnvironment();
				$this->_route();

				if($this->_route == true){
					$this->_setDatabase($db);
					$this->_setSecure();
					$this->_setDefine();
					$this->_setLibrary();
					$this->_setEvent();
					$this->_setCron();
					$this->_setFunction();
					$this->_setFunction($this->request->src);
					$this->_setCron($this->request->src);
					$this->_setDefine($this->request->src);
					$this->_setLibrary($this->request->src);
					$this->_setEvent($this->request->src);
				}
			}
		}

		/**
		 * initialization of the engine for cron
		 * @access public
		 * @param $src string
		 * @param $controller string
		 * @param $action string
		 * @param $db \System\Pdo\Pdo
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		public function initCron($src, $controller, $action, $db){
			if(MAINTENANCE == false){
				$this->_routeCron($src, $controller, $action);

				if($this->_route == true){
					$this->_setFunction($this->request->src);
					$this->_setCron($this->request->src);
					$this->_setDefine($this->request->src);
					$this->_setLibrary($this->request->src);
					$this->_setEvent($this->request->src);
					$this->_db = $db;
				}
			}
		}

		/**
		 * initialization of the console
		 * @access public
		 * @param $db array
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/
		public function console($db){
			$this->_setDatabase($db);
			self::Terminal();
		}

		/**
		 * routing
		 * @access private
		 * @return $this
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _route(){
			$this->profiler->addTime('route');

			$router = new Router($this);

			foreach ($this->config->config['route'] as $key => $value) {
				foreach ($value as $data) {
					$vars = explode(',', $data['vars']);
					$controller = explode('.', $data['action'])[0];
					$action = explode('.', $data['action'])[1];

					$router->addRoute(new Route($data['url'], $controller, $action, $data['name'], $data['cache'], $vars, $key, $data['logged'], $data['access'], $data['method']));
				}
			}

			$this->request->data->post   =                                 $_POST;
			$this->request->data->get    =                                  $_GET;
			$this->request->data->method = strtolower($_SERVER['REQUEST_METHOD']);

			if(isset($_POST['request-post']))
				$this->request->data->method = 'post';
			else if(isset($_POST['request-put']))
				$this->request->data->method = 'put';
			else if(isset($_POST['request-delete']))
				$this->request->data->method = 'delete';

			if($matchedRoute = $router->getRoute(preg_replace('`\?'.preg_quote($_SERVER['QUERY_STRING']).'`isU', '', $_SERVER['REQUEST_URI']), $this->config)){
				$_GET = array_merge($_GET, $matchedRoute->vars());

				$this->request->name         =         $matchedRoute->name();
				$this->request->src          =          $matchedRoute->src();
				$this->request->controller   =   $matchedRoute->controller();
				$this->request->action       =       $matchedRoute->action();
				$this->request->logged       =       $matchedRoute->logged();
				$this->request->access       =       $matchedRoute->access();
				$this->request->method       =       $matchedRoute->method();
				$this->request->auth         = new Auth($this->request->src);

				if(CACHE_ENABLED == true && $matchedRoute->cache() != '')
					$this->request->cache = $matchedRoute->cache();
				else
					$this->request->cache = 0;

				if($this->request->action == '')
					$this->request->action = 'default';

				$this->_route = true;
			}

			$this->profiler->addTime('route', Profiler::USER_END);

			return $this;
		}

		/**
		 * routing with cron
		 * @access private
		 * @param $src string
		 * @param $controller string
		 * @param $action string
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _routeCron($src, $controller, $action){
			$this->profiler->addTime('route cron : '.$src.'/'.$controller.'/'.$action);
			$this->request->name       =          '-'.$src.'_'.$controller.'_'.$action;
			$this->request->src        =                                          $src;
			$this->request->controller =                                   $controller;
			$this->request->action     =                                       $action;
			$this->request->auth       =                 new Auth($this->request->src);
			$this->_route = true;
			$this->profiler->addTime('route cron : '.$src.'/'.$controller.'/'.$action, Profiler::USER_END);
		}

		/**
		 * init controller
		 * @access public
		 * @return void
		 * @throws Exception
		 * @since 3.0
		 * @package System\Engine
		*/

		protected function _controller(){
			if($this->_setControllerFile($this->request->src, $this->request->controller) == true){
				$className = "\\".$this->request->src."\\".ucfirst($this->request->controller);
				/** @var \System\Controller\Controller $class */
				$class = new $className();

				if(SECURITY == false || ($this->request->logged == '*' && $this->request->access == '*') || $class->setFirewall() == true){
					if(SPAM == false || $class->setSpam() == true){
						if($this->request->cache > 0){
							$cache = $this->Cache('page_'.preg_replace('#\/#isU', '-slash-', $this->request->env('REQUEST_URI')), $this->request->cache);

							if($cache->isDie() == true){
								$class->model();

								$output = $this->_action($class);
								$this->response->page($output);

								$cache->setContent(serialize($this->response));
								$cache->setCache();
							}
							else{
								$response = unserialize($cache->getCache());

								$this->response->page($response->page());
								$this->response->header($response->header());
								$this->response->contentType($response->contentType());
							}
						}
						else{
							$class->model();
							$output = $this->_action($class);
							$this->response->page($output);
						}
					}
					else{
						$this->addError('The spam filter has detected an error', __FILE__, __LINE__, ERROR_ERROR);
					}
				}
				else{
					$this->addError('The firewall has detected an error', __FILE__, __LINE__, ERROR_ERROR);
				}
			}
			else{
				throw new Exception("Can't include controller and model from module ".$this->request->src);
			}
		}

		/**
		 * call action from controller
		 * @param &$class \system\Controller\Controller
		 * @throws Exception
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Engine
		 */

		public function _action(&$class){
			ob_start();
				$class->init();

				if(method_exists($class, 'action'.ucfirst($this->request->action))){
					$action = 'action'.ucfirst($this->request->action);
					$params = self::Injector()->getArgsMethod($class, $action);

					$reflectionMethod = new \ReflectionMethod($class, $action);
					$output = $reflectionMethod->invokeArgs($class, $params);

					$this->addError('Action "'.ucfirst($this->request->src).'/'.ucfirst($this->request->controller).'/action'.ucfirst($this->request->action).'" called successfully', __FILE__, __LINE__, ERROR_INFORMATION);
				}
				else{
					throw new Exception('The requested "'.ucfirst($this->request->src).'/'.ucfirst($this->request->controller).'/action'.ucfirst($this->request->action).'"  doesn\'t exist');
				}

				$class->end();
				$output = ob_get_contents().$output;
			ob_get_clean();

			return $output;
		}

		/**
		 * include the module
		 * @access protected
		 * @param $src string
		 * @param $controller string
		 * @return boolean
		 * @since 3.0
		 * @package System\Engine
		*/

		protected function _setControllerFile($src, $controller){
			$controllerPath = SRC_PATH.$src.'/'.SRC_CONTROLLER_PATH.ucfirst($controller).EXT_CONTROLLER.'.php';
			$modelPath = SRC_PATH.$src.'/'.SRC_MODEL_PATH.ucfirst($controller).EXT_MODEL.'.php';

			if(file_exists($controllerPath) && file_exists($modelPath)){
				require_once($controllerPath);
				require_once($modelPath);

				return true;
			}
			else{
				return false;
			}
		}

		/**
		 * display the page
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		public function run(){
			if(MAINTENANCE == false){
				if($this->_route == false){
					$this->response->status(404);
					$this->addError('routing failed : http://'.$this->request->env('HTTP_HOST').$this->request->env('REQUEST_URI'), __FILE__, __LINE__, ERROR_WARNING);
				}
				else{
					$this->_controller();
				}

				$this->response->run($this->profiler, $this->config, $this->request);
				$this->addErrorHr(LOG_ERROR);
				$this->addErrorHr(LOG_SYSTEM);
				$this->_setHistory('');

				if(MINIFY_OUTPUT_HTML == true && preg_match('#text/html#isU', $this->response->contentType()))
					$this->response->page($this->_minifyHtml($this->response->page()));

				if(ENVIRONMENT == 'development' && PROFILER == true)
					$this->profiler->profiler($this->request, $this->response);
			}
			else{
				$this->response->page($this->maintenance());
			}

			echo $this->response->page();
		}

		/**
		 * display the page for a cron
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		public function runCron(){
			if(MAINTENANCE == false){
				$this->_controller();
				$this->_setHistory('CRON');

				if(ENVIRONMENT == 'development' && PROFILER == true)
					$this->profiler->profiler($this->request, $this->response);
			}

			echo $this->response->page();
		}

		/**
		 * get maintenance template
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Engine
		*/

		private function maintenance(){
			$tpl = self::Template('.app/system/maintenance', 'maintenance');
			return $tpl->show();
		}

		/**
		 * set error environment
		 * @access private
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setEnvironment(){
			switch(ENVIRONMENT){
				case 'development' :
					error_reporting(E_ALL | E_NOTICE);
				break;

				case 'production' :
					error_reporting(0);
				break;
			}
		}

		/**
		 * enable error handling
		 * @access private
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setErrorHandler(){
			new ErrorHandler();
		}

		/**
		 * set cron
		 * @access private
		 * @param $src string
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setCron($src = null){
			if($src == null)
				self::Cron(APP_CONFIG_CRON);
			else
				self::Cron(SRC_PATH.$src.'/'.SRC_CONFIG_CRON);
		}

		/**
		 * set define
		 * @access private
		 * @param $src string
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setDefine($src = null){
			if($src == null)
				self::Define('app');
			else
				self::Define($src);
		}

		/**
		 * set library
		 * @access private
		 * @param $src string
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setLibrary($src = null){
			if($src == null)
				self::Library('app');
			else
				self::Library($src);
		}

		/**
		 * escape GET and POST (htmlentities)
		 * @access private
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setSecure(){
			if(SECURE_GET == true && isset($_GET)){
				$_GET = $this->_setSecureArray($_GET);
			}

			if(SECURE_POST == true && isset($_POST)){
				$_POST = $this->_setSecureArray($_POST);
			}
		}

		/**
		 * escape array (htmlentities)
		 * @access private
		 * @param $var array
		 * @return mixed
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setSecureArray($var){
			if(is_array($var)){
				foreach ($var as $key => $value) {
					$var[''.$key.''] = $this->_setSecureArray($value);
				}
			}
			else{
				$var = htmlentities($var);
			}

			return $var;
		}

		/**
		 * set event
		 * @access private
		 * @param $src string
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setEvent($src = null){
			if(empty($GLOBALS['eventListeners'])){
				$GLOBALS['eventListeners'] = array();
			}

			if($src != null){
				$path = SRC_PATH.$src.'/'.SRC_RESOURCE_EVENT_PATH;
			}
			else{
				$path = APP_RESOURCE_EVENT_PATH;
			}

			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					if(preg_match('#(\.php$)$#isU', $entry)){
						if(!array_key_exists($path.$entry, $GLOBALS['eventListeners'])){
							include_once($path.$entry);

							if($src != null) {
								$event = '\Event\\' . ucfirst($src) . '\\' . preg_replace('#(.+)' . preg_quote(EXT_EVENT . '.php') . '#', '$1', $entry);
								$event = preg_replace('#' . preg_quote('/') . '#', '\\', $event);
							}
							else{
								$event = '\Event\\' . preg_replace('#(.+)' . preg_quote(EXT_EVENT . '.php') . '#', '$1', $entry);
								$event = preg_replace('#' . preg_quote('/') . '#', '\\', $event);
							}

							$GLOBALS['eventListeners'][''.$path.$entry.''] = new $event();
						}
					}
				}

				closedir($handle);
			}
		}

		/**
		 * set function.php
		 * @access private
		 * @param $src string
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setFunction($src = null){
			if($src == null){
				require_once(APP_FUNCTION);
			}
			else{
				require_once(SRC_PATH.$src.'/'.SRC_CONTROLLER_FUNCTION_PATH);
			}
		}

		/**
		 * set database
		 * @access private
		 * @param $db array
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setDatabase($db){
			if(DATABASE == true)
				self::Database($db);
		}

		/**
		 * log request in history
		 * @access private
		 * @param $message string
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setHistory($message){
			$this->addError('URL : http://'.$this->request->env('HTTP_HOST').$this->request->env('REQUEST_URI').' ('.$this->response->status().
				') / SRC "'.$this->request->src.'" / CONTROLLER "'.$this->request->controller.
				'" / ACTION "'.$this->request->action.'" / CACHE "'.$this->request->cache.
				'" / ORIGIN : '.$this->request->env('HTTP_REFERER').' / IP : '.$this->request->env('REMOTE_ADDR'). ' / '.$message, 0, 0, 0, LOG_HISTORY);
		}

		/**
		 * create the log folder
		 * @access private
		 * @return void
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _setLog(){
			if (!file_exists(APP_LOG_PATH))
				mkdir(APP_LOG_PATH, 0755, true);
		}

		/**
		 * minify html
		 * @access private
		 * @param string $buffer
		 * @return string
		 * @since 3.0
		 * @package System\Engine
		*/

		private function _minifyHtml($buffer) {
			$search = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/\>(\s)+/s', '/(\s)+\</s');
			$replace = array('> ', ' <', '> ', ' <');
			$buffer = preg_replace($search, $replace, $buffer);

			return $buffer;
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Engine
		*/

		public function __destruct(){
		}
	}