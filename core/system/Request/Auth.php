<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Auth.php
	 | @author : fab@c++
	 | @description : permit to manipulate role and logged session variable
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Request;

	use System\General\error;
	use System\General\facades;
	use System\Exception\AttributeNotAllowedException;
	use System\Exception\MissingConfigException;

	class Auth{
		use error, facades;

		/**
		 * parameters
		 * @var array
		*/

		protected $_param = [
			'logged' => false,
			'role' => ''
		];

		/**
		 * path to logged and role
		 * @var array
		 */

		public $_path = [
			'logged' => '',
			'role' => ''
		];

		/**
		 * current module
		 * @var string
		*/

		protected $_src;

		/**
		 * config reference
		 * @var object
		*/

		protected $_config;

		/**
		 * constructor
		 * @access public
		 * @param string $src
		 * @since 3.0
		 * @package System\Request
		*/

		public function __construct ($src){
			$this->_config = self::Config();
			$this->_src    =           $src;

			$this->_path['logged'] = explode('.', $this->_config->config['firewall'][''.$src.'']['logged']['name']);
			$this->_path['role'] = explode('.', $this->_config->config['firewall'][''.$src.'']['roles']['name']);
			$this->_param['role'] = $this->_getSession($this->_path['role']);

			$data = $this->_getSession($this->_path['logged']);

			if($data != '')
				$this->_param['logged'] = $data;
		}

		/**
		 * Magic get method allows access to logged and role session variable values
		 * @access public
		 * @param $name string : name of the attribute
		 * @return mixed
		 * @throws \System\Exception\AttributeNotAllowedException
		 * @since 3.0
		 * @package System\Request
		*/

		public function __get($name){
			if (isset($this->_param[$name])) {
				return $this->_param[$name];
			}
			else{
				throw new AttributeNotAllowedException('the attribute '.$name.' doesn\'t exist');
			}
		}

		/**
		 * Magic get method allows access to logged and role variable session value
		 * @access public
		 * @param $name string : name of the attribute
		 * @param $value string : new value
		 * @throws \System\Exception\AttributeNotAllowedException
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		public function __set($name, $value){
			if (isset($this->_param[$name])) {
				$this->_param[$name] = $value;
				$this->_setSession($this->_path[''.$name.''], $value);
			}
			else{
				throw new AttributeNotAllowedException('the attribute '.$name.' doesn\'t exist');
			}
		}

		/**
		 * Get and Set the value of any role attribute
		 * @access public
		 * @param $src string
		 * @param $value string : new value
		 * @throws \System\Exception\MissingConfigException
		 * @return mixed
		 * @since 3.0
		 * @package System\Request
		*/

		public function role($src, $value = ''){
			if(isset($this->_config->config['firewall'][''.$src.''])){
				if($value == ''){
					$role = explode('.', $this->_config->config['firewall'][''.$src.'']['roles']['name']);
					return $this->_getSession($role);
				}
				else{
					$role = explode('.', $this->_config->config['firewall'][''.$src.'']['roles']['name']);
					$this->_setSession($role, $value);
				}
			}
			else{
				throw new MissingConfigException('the module '.$src.' doesn\'t exist');
			}

			return false;
		}

		/**
		 * Get and Set the value of any logged attribute
		 * @access public
		 * @param $src string
		 * @param $value string : new value
		 * @throws \System\Exception\MissingConfigException
		 * @return mixed
		 * @since 3.0
		 * @package System\Request
		*/

		public function logged($src, $value = ''){
			if(isset($this->_config->config['firewall'][''.$src.''])){
				if($value == ''){
					$logged = explode('.', $this->_config->config['firewall'][''.$src.'']['logged']['name']);

					$data = $this->_getSession($logged);

					if($data != '')
						return $data;
					else
						return false;
				}
				else{
					$logged = explode('.', $this->_config->config['firewall'][''.$src.'']['logged']['name']);
					$this->_setSession($logged, $value);
				}
			}
			else{
				throw new MissingConfigException('the module '.$src.' doesn\'t exist');
			}

			return false;
		}

		/**
		 * get logged and role value from environment
		 * @access public
		 * @param $array array : "path" to the value in $in
		 * @return mixed
		 * @since 3.0
		 * @package System\Request
		*/

		protected function _getSession($array = []){
			if(isset($_SESSION[''.$array[0].''])){
				$to = $_SESSION[''.$array[0].''];
				array_splice($array, 0, 1);

				foreach ($array as $value) {
					if(isset($to[''.$value.''])){
						$to = $to[''.$value.''];
					}
					else{
						return false;
					}
				}
			}
			else{
				return false;
			}

			return $to;
		}

		/**
		 * get logged and role value from environment
		 * @access public
		 * @param $array array : "path" to the value in $in
		 * @param $value string : new value
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		protected function _setSession($array, $value = ''){
			$data = '$_SESSION';
			$i = 0;

			foreach ($array as $keys) {
				if($i != count($array) - 1){
					$exec = $data.'[\''.$keys.'\'] = [];';
					$data .= '[\''.$keys.'\']';
				}
				else{
					$exec = $data.'[\''.$keys.'\'] = $value;';
					$data .= '[\''.$keys.'\']';
				}

				eval($exec);
				$i++;
			}
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Request
		*/

		public function __destruct(){
		}
	}