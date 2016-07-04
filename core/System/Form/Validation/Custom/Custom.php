<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Custom.php
	 | @author : fab@c++
	 | @description : custom validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Form\Validation\Custom;

	/**
	 * Class Custom
	 * @package System\Form\Validation\Custom
	 */

	abstract class Custom {
		/**
		 * the field name
		 * @var string $field
		 */

		protected $field;

		/**
		 * the field label
		 * @var string $label
		 */

		protected $label;

		/**
		 * the field value
		 * @var mixed $value
		 */

		protected $value;

		/**
		 * constructor
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @param $value mixed
		 * @since   3.0
		 * @package System\Form\Validation\Custom
		 */

		final public function __construct($field, $label, $value) {
			$this->field = $field;
			$this->label = $label;
			$this->value = $value;
		}

		/**
		 * you can define your own form filter here
		 * @access  public
		 * @return boolean
		 * @since   3.0
		 * @package System\Form\Validation\Custom
		 */

		public function filter() {
			return true;
		}

		/**
		 * if the filter return false, the framework call this method to get the
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\Form\Validation\Custom
		 */

		public function error() {
			return '';
		}
	}