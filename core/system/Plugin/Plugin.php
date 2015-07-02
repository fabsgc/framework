<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Plugin.php
	 | @author : fab@c++
	 | @description : plugin installation
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Plugin;

	use System\General\error;

	class Plugin {
		use error;

		/**
		 * Constructor
		 * @access public
		 * @param $db \system\Pdo\Pdo
		 * @since 3.0
		 * @package System\Plugin
		*/

		public function __construct($db){

		}

		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Plugin
		*/

		public function __destruct(){
		}
	}