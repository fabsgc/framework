<?php
	/*\
 	 | ------------------------------------------------------
	 | @file : Entity.php
	 | @author : fab@c++
	 | @description : sql table representation
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Entity;

	use System\General\facades;

	class Multiple {
		use facades;

		/**
		 * @var array
		*/

		private $_data;

		/**
		 * Constructor
		 * @access public
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function __construct($data) {
			$this->_data = $data;
		}

		/**
		 * edit the value of a column
		 * @access public
		 * @param string $key
		 * @param array $value
		 * @return void
		 * @since 3.0
		 * @package system
		*/

		public function __set($key, $value) {
			if(array_key_exists($key, $this->_data)){
				$this->_data[''.$key.''] = $value;
			}
		}

		/**
		 * get a column
		 * @access public
		 * @param $key string
		 * @return string
		 * @since 3.0
		 * @package system
		*/

		public function __get($key) {
			if(array_key_exists($key, $this->_data)){
				return $this->_data[''.$key.''];
			}

			return null;
		}

		/**
		 * return data
		 * @access public
		 * @param $data array
		 * @return array,null
		 * @since 3.0
		 * @package system
		*/

		public function data($data = []){
			if(count($data) == 0){
				return $this->_data;
			}
			else{
				$this->_data = $data;
			}

			return null;
		}


		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function __destruct(){
		}
	}