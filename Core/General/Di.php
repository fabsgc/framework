<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Di.php
	 | @author : Fabien Beaujean
	 | @description : Trait dependency Injection
	 | @version : 3.0
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\General;

	trait Di {
		/**
		 * @var \System\Request\Request $request
		 */
		private $request = null;

		/**
		 * @var \System\Response\Response $response
		 */
		private $response = null;

		/**
		 * @var \System\Profiler\Profiler $profiler
		 */
		private $profiler = null;

		/**
		 * @var \System\Config\Config $config
		 */
		private $config = null;
	}