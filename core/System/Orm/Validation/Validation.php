<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Validation.php
	 | @author : fab@c++
	 | @description : entity validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Validation;

	use System\Orm\Validation\Element\Checkbox;
	use System\Orm\Validation\Element\File;
	use System\Orm\Validation\Element\Radio;
	use System\Orm\Validation\Element\Select;
	use System\Orm\Validation\Element\Text;

	/**
	 * Class Validation
	 * @package System\Orm\Validation
	 */

	class Validation {
		/**
		 * entity name
		 * @var $_entity \System\Orm\Entity\Entity
		 */

		protected $_entity;

		/**
		 * @var array
		 */

		protected $_errors = [];

		/**
		 * @var \System\Orm\Validation\Element\Element[]
		 */

		protected $_elements = [];

		/**
		 * constructor
		 * @access  public
		 * @param $entity \System\Orm\Entity\Entity
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function __construct($entity) {
			$this->_entity = $entity;
		}

		/**
		 * check a form request
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function check() {
			$this->_errors = [];

			/** @var $element \System\Orm\Validation\Element\Element */
			foreach ($this->_elements as $element) {
				$element->check();

				if ($element->valid() == false) {
					$this->_errors = array_merge($this->_errors, $element->errors());
				}
			}
		}

		/**
		 * is valid
		 * @access  public
		 * @return boolean
		 * @since   3.0
		 * @package System\Orm\Validation
		 */

		public function valid() {
			if (count($this->_errors) > 0) {
				return false;
			}
			else {
				return true;
			}
		}

		/**
		 * get errors
		 * @access  public
		 * @return array
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function errors() {
			return $this->_errors;
		}

		/**
		 * add text element
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @return \System\Orm\Validation\Element\Text
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function text($field, $label) {
			$element = new Text($this->_entity, $field, $label);
			array_push($this->_elements, $element);
			return $element;
		}

		/**
		 * add checkbox element
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Checkbox
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function checkbox($field, $label) {
			$checkbox = new Checkbox($this->_entity, $field, $label);
			array_push($this->_elements, $checkbox);
			return $checkbox;
		}

		/**
		 * add radio element
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Radio
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function radio($field, $label) {
			$radio = new Radio($this->_entity, $field, $label);
			array_push($this->_elements, $radio);
			return $radio;
		}

		/**
		 * add select element
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @return \System\Form\Validation\Element\Select
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function select($field, $label) {
			$select = new Select($this->_entity, $field, $label);
			array_push($this->_elements, $select);
			return $select;
		}

		/**
		 * add file element
		 * @access  public
		 * @param $field string
		 * @param $label string
		 * @return \System\Orm\Validation\Element\File
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function file($field, $label) {
			$file = new File($this->_entity, $field, $label);
			array_push($this->_elements, $file);
			return $file;
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Form\Validation
		 */

		public function __destruct() {
		}
	}