<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Data.php
	 | @author : Fabien Beaujean
	 | @description : contain data from headers
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Request;

	use Gcs\Framework\Core\Exception\AttributeNotAllowedException;
	use Gcs\Framework\Core\General\Singleton;

	/**
	 * Class Data
	 * @property string form
	 * @property string method
	 * @property array  get
	 * @property array  post
	 * @property array  put
	 * @property array  patch
	 * @property array  delete
	 * @property array  file
	 * @package Gcs\Framework\Core\Request
	 */

	class Data {
		use Singleton;

		/**
		 * parameters of each action
		 * @var array
		 */

		public $param = [
			'form'    => false,
			'method'  => '',
			'get'     => [],
			'post'    => [],
			'put'     => [],
			'patch'   => [],
			'delete'  => [],
			'cookie'  => [],
			'file'    => [],
			'session' => []
		];

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
		 */

		private function __construct() {
		}

		/**
		 * singleton
		 * @access public
		 * @return \System\Request\Data
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
		 */

		public static function instance() {
			if (is_null(self::$_instance)) {
				self::$_instance = new Data();
			}

			return self::$_instance;
		}

		/**
		 * Magic get method allows access to parsed routing parameters directly on the object.
		 * @access public
		 * @param $name string : name of the attribute
		 * @return mixed
		 * @throws \System\Exception\AttributeNotAllowedException
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
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
		 * @access public
		 * @param $name  string : name of the attribute
		 * @param $value string : new value
		 * @return void
		 * @throws \System\Exception\AttributeNotAllowedException
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
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
		 * @access public
		 * @param $env
		 * @return boolean
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
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
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
		 */

		public function __destruct() {
		}
	}