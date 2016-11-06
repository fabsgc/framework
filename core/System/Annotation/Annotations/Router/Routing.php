<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Routing.php
	 | @author : fab@c++
	 | @description : annotation routing
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Annotation\Annotations\Router;

	use System\Annotation\Annotations\Annotation;

	/**
	 * Class Routing
	 * @package System\Annotation\Annotations\Router
	 */

	class Routing extends Annotation {

		/**
		 * Parameter name
		 * @var string
		 */

		public $name;

		/**
		 * Parameter url
		 * @var string
		 */

		public $url = '/';

		/**
		 * Parameter vars
		 * @var string
		 */

		public $vars = '';

		/**
		 * Parameter url
		 * @var string
		 */

		public $method = '*';

		/**
		 * Parameter access
		 * @var string
		 */

		public $access = '*';

		/**
		 * Parameter cache
		 * @var string
		 */

		public $cache = '0';

		/**
		 * Parameter logged
		 * @var string
		 */

		public $logged = '*';
	}