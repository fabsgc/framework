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
		 * name of the form
		 * @var string
		*/

		protected $form = '';

		/**
		 * the form is already sent and checked
		 * @var bool
		*/

		protected $sent = false;

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
		 * Initialization
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		*/

		public function init(){
		}

		/**
		 * Get form name
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Request
		*/

		public function getForm(){
			return $this->form;
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
			$this->sent = true;
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
		 * Is the form sent ?
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Request
		*/

		public function sent(){
			return $this->sent;
		}

		/**
		 * get errors list
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