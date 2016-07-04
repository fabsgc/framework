<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Select.php
	 | @author : fab@c++
	 | @description : select validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Form\Validation\Element;

	/**
	 * Class Select
	 * @package System\Form\Validation\Element
	 */

	class Select extends Element {
		/**
		 * constructor
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Select
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function __construct($field, $label) {
			parent::__construct($field, $label);
			return $this;
		}

		/**
		 * validity
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function check() {
			if ($this->_exist) {
				parent::check();
			}
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Form\Validation\Element
		 */

		public function __destruct() {
		}
	}