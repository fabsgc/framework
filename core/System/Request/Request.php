<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Request.php
	 | @author : fab@c++
	 | @description : contain data and informations from http request and engine
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Request;

	use System\Exception\AttributeNotAllowedException;
	use System\General\singleton;

	/**
	 * Class Request
	 * @property string name
	 * @property string controller
	 * @property string src
	 * @property string action
	 * @property string cache
	 * @property string logged
	 * @property string access
	 * @property string method
	 * @property string lang
	 * @property \System\Request\Auth auth
	 * @property \System\Request\Data data
	 * @package System\Request
	 */

	class Request {
		use singleton;

		/**
		 * parameters of each action
		 * @var array
		 */

		public $param = [
			'name'       => '',
			'src'        => '',
			'controller' => '',
			'action'     => '',
			'cache'      => 0,
			'logged'     => '*',
			'access'     => '*',
			'method'     => '*',
			'lang'       => LANG,
			'auth'       => '',
			'data'       => null
		];

		/**
		 * constructor
		 * @access  public
		 * @since   3.0
		 * @package System\Request
		 */

		private function __construct() {
			$this->param['data'] = Data::getInstance();
		}

		/**
		 * singleton
		 * @access  public
		 * @since   3.0
		 * @package System\Request
		 */

		public static function getInstance() {
			if (is_null(self::$_instance)) {
				self::$_instance = new Request();
			}

			return self::$_instance;
		}

		/**
		 * Magic get method allows access to parsed routing parameters directly on the object.
		 * @access  public
		 * @param $name string : name of the attribute
		 * @return mixed
		 * @throws \System\Exception\AttributeNotAllowedException
		 * @since   3.0
		 * @package System\Request
		 */

		public function __get($name) {
			if (isset($this->param[$name])) {
				return $this->param[$name];
			}
			else {
				throw new AttributeNotAllowedException("the attribute " . $name . " doesn't exist");
			}
		}

		/**
		 * Magic get method allows access to parsed routing parameters directly on the object to modify it
		 * @access  public
		 * @param $name  string : name of the attribute
		 * @param $value string : new value
		 * @return void
		 * @throws \System\Exception\AttributeNotAllowedException
		 * @since   3.0
		 * @package System\Request
		 */

		public function __set($name, $value) {
			if (isset($this->param[$name])) {
				$this->param[$name] = $value;
			}
			else {
				throw new AttributeNotAllowedException("the attribute " . $name . " doesn't exist");
			}
		}

		/**
		 * get server data
		 * @access  public
		 * @param $env
		 * @return boolean
		 * @since   3.0
		 * @package System\Request
		 */

		public function env($env) {
			if (isset($_SERVER[$env])) {
				return $_SERVER[$env];
			}
			else {
				return false;
			}
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Request
		 */

		public function __destruct() {
		}
	}