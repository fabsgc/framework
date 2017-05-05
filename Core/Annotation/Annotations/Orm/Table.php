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

	use Gcs\Framework\Core\Annotation\Annotation;

	/**
	 * Class Table
	 * @package Gcs\Framework\Core\Annotation\Annotations\Orm
	 */

	class Table extends Annotation {

		/**
		 * Parameter name
		 * @var string
		 */

		public $name;
	}