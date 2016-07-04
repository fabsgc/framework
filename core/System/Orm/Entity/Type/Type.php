<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Type.php
	 | @author : fab@c++
	 | @description : abstract class type
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Entity\Type;

	/**
	 * Class Type
	 * @package System\Orm\Entity\Type
	 */

	abstract class Type {
		/**
		 * Constructor
		 * @access  public
		 * @since   3.0
		 * @package System\Orm\Entity\Type
		 */

		public function __construct() {
		}

		/**
		 * Hydrate object
		 * @access  public
		 * @param $field string
		 * @return void
		 * @since   3.0
		 * @package System\Orm\Entity\Type
		 */

		public function hydrate($field) {
		}

		/**
		 * Which value orm save in the database
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package string
		 */

		public function value() {
		}

		/**
		 * Save the file on the HDD
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Orm\Entity\Type
		 */

		public function save() {
		}

		/**
		 * Delete the file
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Orm\Entity\Type
		 */

		public function delete() {
		}

		/**
		 * Destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Orm\Entity\Type
		 */

		public function __destruct() {
		}
	}