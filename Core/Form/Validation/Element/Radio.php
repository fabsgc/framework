<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Radio.php
	 | @author : Fabien Beaujean
	 | @description : radio validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Form\Validation\Element;

	use Gcs\Framework\Core\Lang\Lang;

	/**
	 * Class Radio
	 * @package Gcs\Framework\Core\Form\Validation\Element
	 */

	class Radio extends Element {
		/**
		 * constructor
		 * @access public
		 * @param $field string
		 * @param $label string
		 * @return \Gcs\Framework\Core\Form\Validation\Element\Radio
		 * @since 3.0
		 * @package Gcs\Framework\Core\Form\Validation\Element
		 */

		public function __construct($field, $label) {
			parent::__construct($field, $label);

			if (!isset($this->_data[$field])) {
				array_push($this->_errors, [
					'field'   => $this->_label,
					'message' => Lang::instance()->lang('.app.system.form.exist')
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
		 * @package Gcs\Framework\Core\Form\Validation\Element
		 */

		public function check() {
			if ($this->_exist) {
				parent::check();
			}
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Form\Validation\Element
		 */

		public function __destruct() {
		}
	}