<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Before.php
	 | @author : fab@c++
	 | @description : annotation before
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Common;

	use System\Annotation\Annotations\Annotation;

	/**
	 * Class Before
	 * @package System\Annotation\Annotations\Common
	 */

	class Before extends Annotation {

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