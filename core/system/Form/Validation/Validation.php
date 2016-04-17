<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Validation.php
	 | @author : fab@c++
	 | @description : request validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Form\Validation;

	use System\Form\Validation\Element\Checkbox;
	use System\Form\Validation\Element\File;
	use System\Form\Validation\Element\Radio;
	use System\Form\Validation\Element\Select;
	use System\Form\Validation\Element\Text;

	class Validation{

		/**
		 * @var array
		*/

		protected $_errors = [];

		/**
		 * @var  \System\Form\Validation\Element\Element[]
		*/

		protected $_elements = [];

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function __construct (){
		}

		/**
		 * check a form request
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function check(){
			$this->_errors = [];

			/** @var $element \System\Form\Validation\Element\Element */
			foreach($this->_elements as $element){
				$element->check();

				if($element->valid() == false){
					$this->_errors[$element->field()] = $element->errors();
				}
			}
		}

		/**
		 * is valid
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function valid(){
			if(count($this->_errors) > 0)
				return false;
			else
				return true;
		}

		/**
		 * get errors
		 * @access public
		 * @return array
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function errors(){
			return $this->_errors;
		}

		/**
		 * add text element
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Text
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function text($field, $label){
			$text = new Text($field, $label);
			array_push($this->_elements, $text);
			return $text;
		}

		/**
		 * add checkbox element
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Checkbox
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function checkbox($field, $label){
			$checkbox = new Checkbox($field, $label);
			array_push($this->_elements, $checkbox);
			return $checkbox;
		}

		/**
		 * add radio element
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Radio
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function radio($field, $label){
			$radio = new Radio($field, $label);
			array_push($this->_elements, $radio);
			return $radio;
		}

		/**
		 * add select element
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Select
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function select($field, $label){
			$select = new Select($field, $label);
			array_push($this->_elements, $select);
			return $select;
		}

		/**
		 * add file element
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\File
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function file($field, $label){
			/** @var \System\Form\Validation\Element\File $file */
			$file = new File($field, $label);
			array_push($this->_elements, $file);
			return $file;
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Form\Validation
		*/

		public function __destruct(){
		}
	}