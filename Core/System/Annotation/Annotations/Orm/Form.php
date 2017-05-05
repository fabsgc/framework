<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Table.php
	 | @author : Fabien Beaujean
	 | @description : annotation orm table
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Orm;

	use System\Annotation\Annotations\Annotation;

	/**
	 * Class Form
	 * @package System\Annotation\Annotations\Orm
	 */

	class Form extends Annotation {

		/**
		 * Parameter name
		 * @var string
		 */

		public $name;
	}