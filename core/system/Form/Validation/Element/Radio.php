<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Radio.php
	 | @author : fab@c++
	 | @description : radio validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Form\Validation\Element;

	use System\General\facades;

	class Radio extends Element{
		use facades;

		/**
		 * constructor
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Radio
		 * @since 3.0
		 * @package System\Form\Validation\Element
		*/

		public function __construct ($field, $label){
			parent::__construct($field, $label);

			if(!isset($this->_data[$field])){
				array_push($this->_errors, [
					'field' => $this->_label,
					'message' =>self::Lang()->lang('.app.system.form.exist')
				]);

				$this->_exist = false;
			}

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
			if($this->_exist)
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