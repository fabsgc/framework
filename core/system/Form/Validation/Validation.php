<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Validation.php
	 | @author : fab@c++
	 | @description : contain data from headers
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Form\Validation;

	class Validation{

		/**
		 * @var array
		*/

		protected $_errors = [];

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package System\Request
		*/

		public function __construct (){
		}

		/**
		 * get errors
		 * @access public
		 * @return array
		 * @since 3.0
		 * @package System\Request
		*/

		public function errors(){
			return $this->_errors;
		}

		/**
		 * check a form request
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Request
		*/

		public function check(){
			if($_POST['text'] == 'erreur')
				array_push($this->_errors, ['text', 'putain de faute']);

			return false;
		}

		/**
		 * is valid
		 * @access public
		 * @return array
		 * @since 3.0
		 * @package System\Request
		*/

		public function valid(){
			if(count($this->_errors) > 0)
				return false;
			else
				return true;
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