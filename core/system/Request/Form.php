<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Form.php
	 | @author : fab@c++
	 | @description : abstract to validate a form
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Request;

	use System\Form\Validation\Validation;

	/**
	 * @property string method
	 * @property array get
	 * @property array post
	 * @property array put
	*/

	abstract class Form{

		/**
		 * parameters of each action
		 * @var \System\Request\Data
		*/

		public $data = null;

		/**
		 * We put errors inside
		 * @var \System\Form\Validation\Validation
		*/

		protected $validation = null;

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package System\Request
		*/

		public function __construct (){
			$this->data = Data::getInstance();
			$this->validation = new Validation();
		}

		/**
		 * We can check the validity of a GET request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		public function get(){
		}

		/**
		 * We can check the validity of a POST request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		public function post(){
		}

		/**
		 * We can check the validity of a PUT request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		public function put(){
		}

		/**
		 * We can check the validity of a DELETE request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		public function delete(){
		}

		/**
		 * Check
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		public function check(){
			$this->validation->check();
		}

		/**
		 * Is the request valid ?
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Request
		*/

		public function valid(){
			return $this->validation->valid();
		}

		/**
		 * Is the request valid ?
		 * @access public
		 * @return string[]
		 * @since 3.0
		 * @package System\Request
		*/

		public function errors(){
			return $this->validation->errors();
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