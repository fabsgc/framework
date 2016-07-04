<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Spam.php
	 | @author : fab@c++
	 | @description : allow you to protect your url(s) against spam
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Security;

	use System\Config\Config;
	use System\Exception\MissingConfigException;
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
		use error, langs, resolve;

		/**
		 * @var string[] $_ip
		 */

		protected $_ip = [];

		/**
		 * @var boolean $_xmlValid
		 */

		protected $_xmlValid = true;

		/**
		 * @var string $_xmlContent
		 */

		protected $_xmlContent = '';

		/**
		 * @var boolean $_exception
		 */

		protected $_exception = false;

		/**
		 * @var string $_ipClient
		 */

		protected $_ipClient = '127.0.0.1';

		const USE_NOT_TPL = 0;
		const USE_TPL     = 1;

		/**
		 * init Spam class
		 * @access  public
		 * @throws \System\Exception\MissingConfigException
		 * @since   3.0
		 * @package System\Security
		 */

		public function __construct() {
			$this->request = Request::getInstance();
			$this->config = Config::getInstance();

			$this->_ipClient = $this->request->env('REMOTE_ADDR');

			if ($fp = @fopen(APP_CONFIG_SPAM, 'r+')) {
				if ($this->_xmlContent = simplexml_load_file(APP_CONFIG_SPAM)) {
					if ($this->_exception() == false) {
						flock($fp, LOCK_EX);
						$this->_setIp();
						flock($fp, LOCK_UN);
					}
				}
				else {
					$this->_xmlValid = true;
					throw new MissingConfigException('Can\'t open file "' . APP_CONFIG_SPAM . '"');
				}
			}
		}

		/**
		 * check authorization to allow to a visitor to load a page
		 * @access  public
		 * @return array
		 * @since   3.0
		 * @package System\Security
		 */

		public function check() {
			if ($this->_exception == false && $this->_xmlValid == true) {
				if (isset($this->_ip['ip']) && $this->_ip['ip'] == $this->_ipClient) {
					if (intval($this->_ip['time']) + intval($this->config->config['spam']['app']['query']['duration']) < time()) {
						$this->_updateIp(time(), 1);
						return true;
					}
					elseif ($this->_ip['number'] < $this->config->config['spam']['app']['query']['number']) {
						$this->_updateIp($this->_ip['time'], intval($this->_ip['number']) + 1);
						return true;
					}
					else {
						$t = new Template($this->config->config['spam']['app']['error']['template'], 'GCspam', 0);

						foreach ($this->config->config['spam']['app']['error']['variable'] as $value) {
							if ($value['type'] == 'var') {
								$t->assign([$value['name'] => $value['value']]);
							}
							else {
								$t->assign([$value['name'] => $this->useLang($value['value'])]);
							}
						}

						echo $t->show();

						$this->addError($this->_ipClient . ' : exceeded the number of queries allowed for the page ' . $this->request->src . '/' . $this->request->controller . '/' . $this->request->action, __FILE__, __LINE__, ERROR_ERROR);
						return false;
					}
				}
			}

			return true;
		}

		/**
		 * check if the url is a spam exception
		 * @access  public
		 * @return boolean
		 * @since   3.0
		 * @package System\Security
		 */

		protected function _exception() {
			$url = '.' . $this->request->src . '.' . $this->request->controller . '.' . $this->request->action;

			if (in_array($url, $this->config->config['spam']['app']['exception'])) {
				$this->_exception = true;
				return true;
			}
			else {
				$this->_exception = false;
				return false;
			}
		}

		/**
		 * get the the list of IPs
		 * @access  public
		 * @return array
		 * @since   3.0
		 * @package System\Security
		 */

		protected function _setIp() {
			$values = $this->_xmlContent->xpath('//ip');

			if (count($values) > 0) {
				foreach ($values as $value) {
					$this->_ip['ip'] = $value['ip']->__toString();
					$this->_ip['number'] = $value['number']->__toString();
					$this->_ip['time'] = $value['time']->__toString();
				}
			}
			else {
				$this->_ip['ip'] = $this->request->env('REMOTE_ADDR');
				$this->_ip['number'] = 1;
				$this->_ip['time'] = time();
			}
		}

		/**
		 * update time and number attribute from IP
		 * @access  public
		 * @param $time   int
		 * @param $number int
		 * @return array
		 * @since   3.0
		 * @package System\Security
		 */

		protected function _updateIp($time = 0, $number = 1) {
			$values = $this->_xmlContent->xpath('//ip[@ip=\'' . $this->_ip['ip'] . '\']');
			$xml = simplexml_load_file(APP_CONFIG_SPAM);

			if (count($values) > 0) {
				foreach ($values as $value) {
					$value['time'] = $time;
					$value['number'] = $number;
					$dom = new \DOMDocument("1.0");
					$dom->preserveWhiteSpace = false;
					$dom->formatOutput = true;
					$dom->loadXML($this->_xmlContent->asXML());
					$dom->save(APP_CONFIG_SPAM);
				}
			}
			else {
				$values = $xml->xpath('//ips')[0];
				$node = $values->addChild('ip', null);
				$node->addAttribute('ip', $this->_ip['ip']);
				$node->addAttribute('time', $this->_ip['time']);
				$node->addAttribute('number', $this->_ip['number']);

				$dom = new \DOMDocument("1.0");
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
				$dom->loadXML($xml->asXML());
				$dom->save(APP_CONFIG_SPAM);
			}
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Security
		 */

		public function __destruct() {
		}
	}