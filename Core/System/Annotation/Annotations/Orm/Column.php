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
	 * Class Column
	 * @package Gcs\Framework\Core\Annotation\Annotations\Orm
	 */

	class Column extends Annotation {

		/**
		 * Parameter type
		 * @var string
		 */

		public $type = 'INT';

		/**
		 * Parameter size
		 * @var string
		 */

		public $size = '11';

		/**
		 * Parameter null
		 * @var string
		 */

		public $null = 'false';

		/**
		 * Parameter type
		 * @var string
		 */

		public $primary = 'false';

		/**
		 * Parameter unique
		 * @var string
		 */

		public $unique = 'false';

		/**
		 * Parameter precision
		 * @var string
		 */

		public $precision = 'false';

		/**
		 * Parameter defaultValue
		 * @var string
		 */

		public $default = '';

		/**
		 * Parameter enum
		 * @var string
		 */

		public $enum = '';
	}