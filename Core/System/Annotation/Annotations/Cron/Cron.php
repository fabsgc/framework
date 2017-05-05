<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Con.php
	 | @author : Fabien Beaujean
	 | @description : annotation cron
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Cron;

	use System\Annotation\Annotations\Annotation;

	/**
	 * Class Cron
	 * @package System\Annotation\Annotations
	 */

	class Cron extends Annotation {

		/**
		 * Parameter time
		 * @var string
		 */

		public $time;
	}