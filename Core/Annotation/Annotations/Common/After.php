<?php
	/*\
	 | ------------------------------------------------------
	 | @file : After.php
	 | @author : Fabien Beaujean
	 | @description : annotation after
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Annotation\Annotations\Common;

	use Gcs\Framework\Core\Annotation\Annotations\Annotation;

	/**
	 * Class After
	 * @package System\Annotation\Annotations
	 */

	class After extends Annotation {

		/**
		 * Parameter class
		 * @var string
		 */

		public $class;

		/**
		 * Parameter class
		 * @var string
		 */

		public $method;
	}