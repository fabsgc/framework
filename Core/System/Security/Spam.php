<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Spam.php
	 | @author : Fabien Beaujean
	 | @description : allow you to protect your url(s) against spam
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Security;

	use System\Cache\Cache;
	use System\Config\Config;
	use System\Exception\MissingConfigException;
	use System\General\di;
	use System\General\error;
	use System\General\langs;
	use System\General\resolve;
	use System\Request\Request;
	use System\Template\Template;

	/**
	 * Class Spam
	 * @package System\Security
	 */

	class Spam {
		use error, langs, resolve, di;

		/**
		 * @var string[] $_ip
		 */

		protected $_ips = [];

		/**
		 * @var \System\Cache\Cache $_cache
		 */

		protected $_cache = null;

		/**
		 * @var boolean $_exception
		 */

		protected $_exception = false;

		/**
		 * @var string $_ipClient
		 */

		protected $_ip = '127.0.0.1';

		/**
		 * init Spam class
		 * @access public
		 * @throws \System\Exception\MissingConfigException
		 * @since 3.0
		 * @package System\Security
		 */

		public function __construct() {
			$this->request = Request::instance();
			$this->config = Config::config();

			$this->_cache = new Cache('core-spam-ips');
			$this->_ip = $this->request->env('REMOTE_ADDR');

			if (isset($this->config['user']['security']['spam'])) {
				if (!$this->_exception()) {
					$this->_ips();
				}
			}
			else {
				throw new MissingConfigException('Can\'t read spam configuration');
			}
		}

		/**
		 * check authorization to allow to a visitor to load a page
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Security
		 */

		public function check() {
            if(empty($this->_ips[$this->_ip])) {
                $this->_ips[$this->_ip] = array();
                $this->_ips[$this->_ip]['time'] = 0;
            }

			if ($this->_exception == false) {
				if (intval($this->_ips[$this->_ip]['time']) + intval($this->config['user']['security']['spam']['config']['query']['duration']) < time()) {
					$this->_updateIp(time(), 1);
					return true;
				}
				elseif ($this->_ips[$this->_ip]['number'] <= $this->config['user']['security']['spam']['config']['query']['number']) {
					$this->_updateIp($this->_ips[$this->_ip]['time'], intval($this->_ips[$this->_ip]['number']) + 1);
					return true;
				}
				else {
					$t = new Template($this->config['user']['security']['spam']['config']['error']['template'], 'core-security-spam', 0);

					foreach ($this->config['user']['security']['spam']['config']['error']['variable'] as $key => $value) {
						if ($value['type'] == 'var') {
							$t->assign([$key => $value['value']]);
						}
						else {
							$t->assign([$key => $this->useLang($value['value'])]);
						}
					}

					echo $t->show();

					$this->addError($this->_ip . ' : exceeded the number of queries allowed for the page ' . $this->request->src . '/' . $this->request->controller . '/' . $this->request->action, __FILE__, __LINE__, ERROR_ERROR);
					return false;
				}
			}

			return true;
		}

		/**
		 * check if the url is a spam exception
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _exception() {
			$url = '.' . $this->request->src . '.' . $this->request->controller . '.' . $this->request->action;

			if (in_array($url, $this->config['user']['security']['spam']['config']['exception'])) {
				$this->_exception = true;
			}

			return $this->_exception;
		}

		/**
		 * get the the list of IPs
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _ips() {
			if($this->_cache->isExist()){
				$this->_ips = $this->_cache->getCache();
			}
			else {
				$this->_ips = [
					$this->_ip => [
						'time'   => time(),
						'number' => 1
					]
				];
			}
		}

		/**
		 * update time and number attribute from IP
		 * @access public
		 * @param int $time
		 * @param int $number
		 * @return array
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _updateIp($time = 0, $number = 1) {
			$this->_ips[$this->_ip] = [
				'time' => $time,
				'number' => $number
			];

			$this->_cache->setContent($this->_ips);
			$this->_cache->setCache();
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Security
		 */

		public function __destruct() {
		}
	}