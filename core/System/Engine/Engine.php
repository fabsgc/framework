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

	use System\Cache\Cache;
	use System\Config\Config;
	use System\Controller\Injector\Injector;
	use System\Cron\Cron;
	use System\Database\Database;
	use System\Exception\ErrorHandler;
	use System\Exception\Exception;
	use System\General\di;
	use System\General\error;
	use System\General\langs;
	use System\General\resolve;
	use System\Library\Library;
	use System\Profiler\Profiler;
	use System\Request\Auth;
	use System\Request\Request;
	use System\Response\Response;
	use System\Router\Route;
	use System\Router\Router;
	use System\Template\Template;
	use System\Terminal\Terminal;

	/**
	 * Class Engine
	 * @package System\Engine
	 */

	class Engine {
		use error, langs, resolve, di;

		/**
		 * @var \System\Router\Route
		 */

		private $_route = false;

		/**
		 * constructor
		 * @access  public
		 * @param $mode integer
		 * @since   3.0
		 * @package System\Engine
		 */

		public function __construct($mode = MODE_HTTP) {
			if (!defined('CONSOLE_ENABLED')) {
				define('CONSOLE_ENABLED', $mode);
			}

			$this->config = Config::instance();
			$this->request = Request::instance();
			$this->response = Response::instance();
			$this->profiler = Profiler::instance();

			$this->_setLog();
			$this->_setErrorHandler();
		}

		/**
		 * initialization of the engine
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		public function init() {
			if (!Config::config()['user']['debug']['maintenance']) {
				date_default_timezone_set(Config::config()['user']['output']['timezone']);
				$this->_setEnvironment();
				$this->_route();

				if ($this->_route == true) {
					$this->_setDatabase();
					$this->_setSecure();
					$this->_setLibrary();
					$this->_setEvent();
					$this->_setCron();
					$this->_setFunction();
					$this->_setFunction($this->request->src);
					$this->_setEvent($this->request->src);
				}
			}
		}

		/**
		 * initialization of the engine for cron
		 * @access  public
		 * @param $src        string
		 * @param $controller string
		 * @param $action     string
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		public function initCron($src, $controller, $action) {
			if (!Config::config()['user']['debug']['maintenance']) {
				$this->_routeCron($src, $controller, $action);

				if ($this->_route == true) {
					$this->_setFunction($this->request->src);
					$this->_setEvent($this->request->src);
				}
			}
		}

		/**
		 * initialization of the console
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */
		public function console() {
			$this->_setDatabase();
			new Terminal();
		}

		/**
		 * routing
		 * @access  private
		 * @return $this
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _route() {
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

			$this->request->data->post = $_POST;
			$this->request->data->get = $_GET;
			$this->request->data->file = $_FILES;
			$this->request->data->method = strtolower($_SERVER['REQUEST_METHOD']);

			if (isset($_GET['request-get']) && $_SERVER['REQUEST_METHOD'] == 'get') {
				$this->request->data->form = true;
				$this->request->data->method = 'get';
			}
			else if (isset($_POST['request-post'])) {
				$this->request->data->form = true;
				$this->request->data->method = 'post';
			}
			else if (isset($_POST['request-put'])) {
				$this->request->data->form = true;
				$this->request->data->method = 'put';
			}
			else if (isset($_POST['request-patch'])) {
				$this->request->data->form = true;
				$this->request->data->method = 'patch';
			}
			else if (isset($_POST['request-delete'])) {
				$this->request->data->form = true;
				$this->request->data->method = 'delete';
			}

			if ($matchedRoute = $router->getRoute(preg_replace('`\?' . preg_quote($_SERVER['QUERY_STRING']) . '`isU', '', $_SERVER['REQUEST_URI']), $this->config)) {
				$_GET = array_merge($_GET, $matchedRoute->vars());

				$this->request->name = $matchedRoute->name();
				$this->request->src = $matchedRoute->src();
				$this->request->controller = $matchedRoute->controller();
				$this->request->action = $matchedRoute->action();
				$this->request->logged = $matchedRoute->logged();
				$this->request->access = $matchedRoute->access();
				$this->request->method = $matchedRoute->method();
				$this->request->auth = new Auth($this->request->src);

				if ($this->config->config['user']['output']['cache']['enabled'] && $matchedRoute->cache() != '') {
					$this->request->cache = $matchedRoute->cache();
				}
				else {
					$this->request->cache = 0;
				}

				if ($this->request->action == '') {
					$this->request->action = 'default';
				}

				$this->_route = true;
			}

			$this->profiler->addTime('route', Profiler::USER_END);

			return $this;
		}

		/**
		 * routing with cron
		 * @access  private
		 * @param $src        string
		 * @param $controller string
		 * @param $action     string
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _routeCron($src, $controller, $action) {
			$this->profiler->addTime('route cron : ' . $src . '/' . $controller . '/' . $action);
			$this->request->name = '-' . $src . '_' . $controller . '_' . $action;
			$this->request->src = $src;
			$this->request->controller = $controller;
			$this->request->action = $action;
			$this->request->auth = new Auth($this->request->src);
			$this->_route = true;
			$this->profiler->addTime('route cron : ' . $src . '/' . $controller . '/' . $action, Profiler::USER_END);
		}

		/**
		 * init controller
		 * @access  public
		 * @return void
		 * @throws Exception
		 * @since   3.0
		 * @package System\Engine
		 */

		protected function _controller() {
			if ($this->_setControllerFile($this->request->src, $this->request->controller) == true) {
				$className = "\\" . $this->request->src . "\\" . ucfirst($this->request->controller);
				/** @var \System\Controller\Controller $class */
				$class = new $className();

				if ($this->config->config['user']['security']['firewall'] == false || ($this->request->logged == '*' && $this->request->access == '*') || $class->setFirewall() == true) {
					if ($this->config->config['user']['security']['spam'] == false || $class->setSpam() == true) {
						if ($this->request->cache > 0) {
							$cache = new Cache('page_' . preg_replace('#\/#isU', '-slash-', $this->request->env('REQUEST_URI')), $this->request->cache);

							if ($cache->isDie() == true) {
								$output = $this->_action($class);
								$this->response->page($output);

								$cache->setContent(serialize($this->response));
								$cache->setCache();
							}
							else {
								$response = unserialize($cache->getCache());

								$this->response->page($response->page());
								$this->response->header($response->header());
								$this->response->contentType($response->contentType());
							}
						}
						else {
							$output = $this->_action($class);
							$this->response->page($output);
						}
					}
					else {
						$this->addError('The spam filter has detected an error', __FILE__, __LINE__, ERROR_ERROR);
					}
				}
				else {
					$this->addError('The firewall has detected an error', __FILE__, __LINE__, ERROR_ERROR);
				}
			}
			else {
				throw new Exception("Can't include controller from module " . $this->request->src);
			}
		}

		/**
		 * call action from controller
		 * @param &$class \system\Controller\Controller
		 * @throws Exception
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\Engine
		 */

		public function _action(&$class) {
			ob_start();
			$class->init();

			if (method_exists($class, 'action' . ucfirst($this->request->action))) {
				$action = 'action' . ucfirst($this->request->action);
				$params = Injector::instance()->getArgsMethod($class, $action);

				$reflectionMethod = new \ReflectionMethod($class, $action);
				$output = $reflectionMethod->invokeArgs($class, $params);

				$this->addError('Action "' . ucfirst($this->request->src) . '/' . ucfirst($this->request->controller) . '/action' . ucfirst($this->request->action) . '" called successfully', __FILE__, __LINE__, ERROR_INFORMATION);
			}
			else {
				throw new Exception('The requested "' . ucfirst($this->request->src) . '/' . ucfirst($this->request->controller) . '/action' . ucfirst($this->request->action) . '"  doesn\'t exist');
			}

			$class->end();
			$output = ob_get_contents() . $output;
			ob_get_clean();

			return $output;
		}

		/**
		 * include the module
		 * @access  protected
		 * @param $src        string
		 * @param $controller string
		 * @return boolean
		 * @since   3.0
		 * @package System\Engine
		 */

		protected function _setControllerFile($src, $controller) {
			$controllerPath = SRC_PATH . $src . '/' . SRC_CONTROLLER_PATH . ucfirst($controller) . '.php';

			if (file_exists($controllerPath)) {
				require_once($controllerPath);

				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * display the page
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		public function run() {
			if (!Config::config()['user']['debug']['maintenance']) {
				if ($this->_route == false) {
					$this->response->status(404);
					$this->addError('routing failed : http://' . $this->request->env('HTTP_HOST') . $this->request->env('REQUEST_URI'), __FILE__, __LINE__, ERROR_WARNING);
				}
				else {
					$this->_controller();
				}

				$this->response->run();
				$this->addErrorHr(LOG_ERROR);
				$this->addErrorHr(LOG_SYSTEM);
				$this->_setHistory('');

				if (Config::config()['user']['output']['minify'] && preg_match('#text/html#isU', $this->response->contentType())) {
					$this->response->page($this->_minifyHtml($this->response->page()));
				}

				if (Config::config()['user']['debug']['environment'] == 'development' && Config::config()['user']['debug']['profiler']) {
					$this->profiler->profiler($this->request, $this->response);
				}
			}
			else {
				$this->response->page($this->maintenance());
			}

			echo $this->response->page();
		}

		/**
		 * display the page for a cron
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		public function runCron() {
			if (!Config::config()['user']['debug']['maintenance']) {
				$this->_controller();
				$this->_setHistory('CRON');

				if (Config::config()['user']['debug']['environment'] == 'development' && Config::config()['user']['debug']['profiler']) {
					$this->profiler->profiler($this->request, $this->response);
				}
			}

			echo $this->response->page();
		}

		/**
		 * get maintenance template
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\Engine
		 */

		private function maintenance() {
			$tpl = new Template('.app/system/maintenance', 'maintenance');
			return $tpl->show();
		}

		/**
		 * set error environment
		 * @access  private
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setEnvironment() {
			switch (Config::config()['user']['debug']['environment']) {
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
		 * @access  private
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setErrorHandler() {
			new ErrorHandler();
		}

		/**
		 * set cron
		 * @access  private
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setCron() {
			new Cron();
		}

		/**
		 * set library
		 * @access  private
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setLibrary() {
			new Library('app');
		}

		/**
		 * escape GET and POST (htmlentities)
		 * @access  private
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setSecure() {
			if (Config::config()['user']['secure']['get'] && isset($_GET)) {
				$_GET = $this->_setSecureArray($_GET);
			}

			if (Config::config()['user']['secure']['post'] && isset($_POST)) {
				$_POST = $this->_setSecureArray($_POST);
			}
		}

		/**
		 * escape array (htmlentities)
		 * @access  private
		 * @param $var array
		 * @return mixed
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setSecureArray($var) {
			if (is_array($var)) {
				foreach ($var as $key => $value) {
					$var['' . $key . ''] = $this->_setSecureArray($value);
				}
			}
			else {
				$var = htmlentities($var);
			}

			return $var;
		}

		/**
		 * set event
		 * @access  private
		 * @param $src string
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setEvent($src = null) {
			if (empty($GLOBALS['eventListeners'])) {
				$GLOBALS['eventListeners'] = [];
			}

			if ($src != null) {
				$path = SRC_PATH . $src . '/' . SRC_RESOURCE_EVENT_PATH;
			}
			else {
				$path = APP_RESOURCE_EVENT_PATH;
			}

			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					if (preg_match('#(\.php$)$#isU', $entry)) {
						if (!array_key_exists($path . $entry, $GLOBALS['eventListeners'])) {
							include_once($path . $entry);

							if ($src != null) {
								$event = '\Event\\' . ucfirst($src) . '\\' . preg_replace('#(.+)' . preg_quote('.php') . '#', '$1', $entry);
								$event = preg_replace('#' . preg_quote('/') . '#', '\\', $event);
							}
							else {
								$event = '\Event\\' . preg_replace('#(.+)' . preg_quote('.php') . '#', '$1', $entry);
								$event = preg_replace('#' . preg_quote('/') . '#', '\\', $event);
							}

							$GLOBALS['eventListeners']['' . $path . $entry . ''] = new $event();
						}
					}
				}

				closedir($handle);
			}
		}

		/**
		 * set function.php
		 * @access  private
		 * @param $src string
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setFunction($src = null) {
			if ($src == null) {
				require_once(APP_FUNCTION);
			}
			else {
				require_once(SRC_PATH . $src . '/' . SRC_CONTROLLER_FUNCTION_PATH);
			}
		}

		/**
		 * set database
		 * @access  private
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setDatabase() {
			if (Config::config()['user']['database']['enabled']) {
				Database::instance();
			}
		}

		/**
		 * log request in history
		 * @access  private
		 * @param $message string
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setHistory($message) {
			$this->addError('URL : http://' . $this->request->env('HTTP_HOST') . $this->request->env('REQUEST_URI') . ' (' . $this->response->status() .
				') / SRC "' . $this->request->src . '" / CONTROLLER "' . $this->request->controller .
				'" / ACTION "' . $this->request->action . '" / CACHE "' . $this->request->cache .
				'" / ORIGIN : ' . $this->request->env('HTTP_REFERER') . ' / IP : ' . $this->request->env('REMOTE_ADDR') . ' / ' . $message, 0, 0, 0, LOG_HISTORY);
		}

		/**
		 * create the log folder
		 * @access  private
		 * @return void
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _setLog() {
			if (!file_exists(APP_LOG_PATH)) {
				mkdir(APP_LOG_PATH, 0755, true);
			}
		}

		/**
		 * minify html
		 * @access  private
		 * @param string $buffer
		 * @return string
		 * @since   3.0
		 * @package System\Engine
		 */

		private function _minifyHtml($buffer) {
			$search = ['/\>[^\S ]+/s', '/[^\S ]+\</s', '/\>(\s)+/s', '/(\s)+\</s'];
			$replace = ['> ', ' <', '> ', ' <'];
			$buffer = preg_replace($search, $replace, $buffer);

			return $buffer;
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Engine
		 */

		public function __destruct() {
		}
	}