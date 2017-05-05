<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Radio.php
	 | @author : Fabien Beaujean
	 | @description : radio field validation
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Orm\Validation\Element;

	/**
	 * Class Radio
	 * @package Gcs\Framework\Core\Orm\Validation\Element
	 */

	class Radio extends Element {
		/**
		 * constructor
		 * @access public
		 * @param $entity \System\Orm\Entity\Entity
		 * @param $field  string
		 * @param $label  string
		 * @return \System\Orm\Validation\Element\Radio
		 * @since 3.0
		 * @package Gcs\Framework\Core\Orm\Validation\Element
		 */

		public function __construct($entity, $field, $label) {
			parent::__construct($entity, $field, $label);
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
			parent::check();
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