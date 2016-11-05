<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Before.php
	 | @author : fab@c++
	 | @description : annotation before
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations;
	use Doctrine\Common\Annotations\Annotation;

	/**
	 * Class Before
	 * @package System\Annotation\Annotations
	 * @Annotation
	 * @Target("CLASS")
	 */

	class Before extends Annotation {
		/**
		 * Parameter class
		 * @var string
		 */

		public $class;
	}