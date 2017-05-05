<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Table.php
	 | @author : Fabien Beaujean
	 | @description : annotation orm table
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Annotation\Annotations\Orm;

	use Gcs\Framework\Core\Annotation\Annotations\Annotation;

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