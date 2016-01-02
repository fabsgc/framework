<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Checkbox.php
	 | @author : fab@c++
	 | @description : input checkbox validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Form\Validation\Element;

	use System\General\facades;

	class Checkbox extends Element{
		use facades;

		const CONSTRAINT_EQUAL = 0;

		/**
		 * constructor
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Checkbox
		 * @since 3.0
		 * @package System\Form\Validation\Element
		*/

		public function __construct ($field, $label){
			parent::__construct($field, $label);
			return $this;
		}

		/**
		 * check validity
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Form\Validation\Element
		*/

		public function check(){
			parent::check();
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Form\Validation\Element
		*/

		public function __destruct(){
		}
	}