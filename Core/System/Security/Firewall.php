<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Firewall.php
	 | @author : Fabien Beaujean
	 | @description : allow you to protect your url(s) against attacks
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Security;

	use System\Config\Config;
	use System\General\di;
	use System\General\error;
	use System\General\langs;
	use System\General\resolve;
	use System\Request\Request;
	use System\Response\Response;
	use System\Template\Template;
	use System\Url\Url;

	/**
	 * Class Firewall
	 * @package System\Security
	 */

	class Firewall {
		use error, langs, resolve, di;

		/**
		 * @var array $_configFirewall
		 * @access protected
		 */

		protected $_configFirewall = [];

		/**
		 * @var array $_csrf
		 * @access protected
		 */

		protected $_csrf = [];

		/**
		 * @var boolean $_logged
		 * @access protected
		 */

		protected $_logged;

		/**
		 * @var string $_role
		 * @access protected
		 */

		protected $_role;

		/**
		 * init lang class
		 * @access public
		 * @since 3.0
		 * @package System\Security
		 */

		public function __construct() {
			$this->request = Request::instance();
			$this->config = Config::instance();
			$this->response = Response::instance();

			$this->_configFirewall = &$this->config->config['firewall']['' . $this->request->src . ''];
			$this->_setFirewall();
		}

		/**
		 * set firewall configuration
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _setFirewall() {
			$csrf = explode('.', $this->_configFirewall['csrf']['name']);
			$logged = explode('.', $this->_configFirewall['logged']['name']);
			$role = explode('.', $this->_configFirewall['roles']['name']);

			$this->_csrf['POST'] = $this->_setFirewallConfigArray($_POST, $csrf);
			$this->_csrf['GET'] = $this->_setFirewallConfigArray($_GET, $csrf);
			$this->_csrf['SESSION'] = $this->_setFirewallConfigArray($_SESSION, $csrf);
			$this->_logged = $this->_setFirewallConfigArray($_SESSION, $logged);
			$this->_role = $this->_setFirewallConfigArray($_SESSION, $role);
		}

		/**
		 * get token, logged and role value from environment
		 * @access public
		 * @param $in    array : array which contain the value
		 * @param $array array : "path" to the value in $in
		 * @return mixed
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _setFirewallConfigArray($in, $array) {
			if (isset($in['' . $array[0] . ''])) {
				$to = $in['' . $array[0] . ''];
				array_splice($array, 0, 1);

				foreach ($array as $value) {
					if (isset($to['' . $value . ''])) {
						$to = $to['' . $value . ''];
					}
					else {
						return false;
					}
				}
			}
			else {
				return false;
			}

			return $to;
		}

		/**
		 * check authorization to allow to a visitor to load a page
		 * @access public
		 * @return mixed
		 * @since 3.0
		 * @package System\Security
		 */

		public function check() {
			if ($this->_checkCsrf() == true) {
				switch ($this->request->logged) {
					case '*' :
						return true;
					break;

					case 'true' :
						if ($this->_checkLogged()) {
							if ($this->_checkRole()) {
								return true;
							}
							else {
								$t = new Template($this->_configFirewall['forbidden']['template'], 'gcsfirewall', 0);
								foreach ($this->_configFirewall['forbidden']['variable'] as $val) {
									if ($val['type'] == 'var') {
										$t->assign([$val['name'] => $val['value']]);
									}
									else {
										$t->assign([$val['name'] => $this->useLang($val['value'])]);
									}
								}
								echo $t->show();

								$this->addError('The access to the page ' . $this->request->src . '/' . $this->request->controller . '/' . $this->request->action . ' is forbidden', __FILE__, __LINE__, ERROR_FATAL);

								return false;
							}
						}
						else {
							$this->addError('The access to the page ' . $this->request->src . '/' . $this->request->controller . '/' . $this->request->action . ' is forbidden because the user must be logged', __FILE__, __LINE__, ERROR_FATAL);
							$url = Url::get($this->_configFirewall['login']['name'], $this->_configFirewall['login']['vars']);

							if ($url != "") {
								$this->response->header('Location: ' . $url);
								return false;
							}
							else {
								$this->addError('The firewall failed to redirect the user to the url ' . $url, __FILE__, __LINE__, ERROR_FATAL);
								return false;
							}
						}
					break;

					case 'false' :
						if ($this->_checkLogged() == false) {
							return true;
						}
						else {
							$this->addError('The access to the page ' . $this->request->src . '/' . $this->request->controller . '/' . $this->request->action . ' is forbidden because the user mustn\'t be logged', __FILE__, __LINE__, ERROR_FATAL);
							$url = Url::get($this->_configFirewall['default']['name'], $this->_configFirewall['default']['vars']);

							if ($url != "") {
								$this->response->header('Location:' . $url);
							}
							else {
								$this->addError('The firewall failed to redirect the user to the url ' . $url, __FILE__, __LINE__, ERROR_FATAL);
								return false;
							}

							return false;
						}
					break;
				}
			}
			else {
				$t = new Template($this->_configFirewall['csrf']['template'], 'gcsfirewall', 0);
				foreach ($this->_configFirewall['csrf']['variable'] as $val) {
					if ($val['type'] == 'var') {
						$t->assign([$val['name'] => $val['value']]);
					}
					else {
						$t->assign([$val['name'] => $this->useLang($val['value'])]);
					}
				}

				echo $t->show();
				$this->addError('The access to the page ' . $this->request->src . '/' . $this->request->controller . '/' . $this->request->action . ' is forbidden : CSRF error', __FILE__, __LINE__, ERROR_FATAL);

				return false;
			}

			return true;
		}

		/**
		 * check csrf
		 * @access protected
		 * @return boolean
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _checkCsrf() {
			if ($this->_configFirewall['csrf']['enabled'] == true && $this->request->logged == true) {
				if ($this->_csrf['SESSION'] != false && ($this->_csrf['GET'] != false || $this->_csrf['POST'] != false)) {
					if ($this->_csrf['POST'] == $this->_csrf['SESSION'] || $this->_csrf['GET'] == $this->_csrf['SESSION']) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					return true;
				}
			}
			else {
				return true;
			}
		}

		/**
		 * check logged
		 * @access protected
		 * @return boolean
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _checkLogged() {
			return $this->_logged;
		}

		/**
		 * check role
		 * @access protected
		 * @return boolean
		 * @since 3.0
		 * @package System\Security
		 */

		protected function _checkRole() {
			if (in_array($this->_role, array_map('trim', explode(',', $this->request->access))) || $this->request->access == '*') {
				return true;
			}
			else {
				return false;
			}
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