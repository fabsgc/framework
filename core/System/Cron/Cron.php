<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Cron.php
	 | @author : fab@c++
	 | @description : cron
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Cron;

	use System\Cache\Cache;
	use System\Config\Config;
	use System\Engine\Engine;
	use System\Exception\MissingConfigException;
	use System\General\di;
	use System\General\error;
	use System\General\facades;
	use System\Profiler\Profiler;
	use System\Request\Request;
	use System\Response\Response;

	/**
	 * Class Cron
	 * @package System\Cron
	 */

	class Cron {
		use error, facades, di;

		/**
		 * @var boolean
		 * @access private
		 */

		private $_exception = false;

		/**
		 * list of execution time
		 * @var array
		 * @access private
		 */

		private $_crons = [];

		/**
		 * constructor
		 * @access public
		 * @throws \System\Exception\MissingConfigException
		 * @since 3.0
		 * @package System\Cron
		 */

		public function __construct() {
			$this->config = Config::instance();
			$this->request = Request::instance();
			$this->response = Response::instance();
			$this->profiler = Profiler::instance();

			if (isset($this->config->config['user']['cron'])) {
				Request::$_instance = null;
				Response::$_instance = null;
				Profiler::$_instance = null;

				if (!$this->_exception()) {
					$cache = new Cache('core-cron-crons');

					if($cache->isExist()){
						$this->_crons = $cache->getCache();
					}

					foreach ($this->config->config['user']['cron']['task'] as $key => $time) {
						if (empty($this->_crons[$key]) || $this->_crons[$key] + $time < time() || $time == 0) {
							$this->_crons[$key] = time();

							$action = explode('.', $key);
							$controller = new Engine();
							$controller->initCron($action[1], $action[2], $action[3]);
							$this->profiler->addTime('route cron : ' . $key);

							ob_start();
								$controller->runCron();
								$output = ob_get_contents();
							ob_get_clean();

							$this->profiler->addTime('route cron : ' . $key, Profiler::USER_END);
							$this->addError('[' . $key . "]\n[" . $output . "]", 0, 0, 0, LOG_CRONS);
							$this->addError('CRON ' . $key . ' called successfully ', __FILE__, __LINE__, ERROR_INFORMATION);
						}
					}

					$cache->setContent($this->_crons);
					$cache->setCache();
				}
				else {
					$this->addError('CRON : the page is an exception ', __FILE__, __LINE__, ERROR_INFORMATION);
				}
			}
			else{
				throw new MissingConfigException('Can\'t read cron configuration');
			}

			Request::$_instance = $this->request;
			Response::$_instance = $this->response;
			Profiler::$_instance = $this->profiler;
		}

		/**
		 * return if the current page which calls crons is an exception
		 * @access protected
		 * @return  boolean
		 * @since 3.0
		 * @package System\Cron
		 */

		protected function _exception() {
			$url = '.' . $this->request->src . '.' . $this->request->controller . '.' . $this->request->action;

			if (in_array($url, $this->config->config['user']['cron']['config']['exception'])) {
				$this->_exception = true;
			}
			else{
				$this->_exception = false;
			}

			return $this->_exception;
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Cron
		 */

		public function __destruct() {
		}
	}