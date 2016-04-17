<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Select.php
	 | @author : fab@c++
	 | @description : select field validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Validation\Element;

	class Select extends Element{

		/**
		 * constructor
		 * @access public
		 * @param $entity \System\Orm\Entity\Entity
		 * @param $field string
		 * @param $label string
		 * @return \System\Orm\Validation\Element\Select
		 * @since 3.0
		 * @package System\Orm\Validation\Element
		*/

		public function __construct ($entity, $field, $label){
			parent::__construct($entity, $field, $label);
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