<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Session.php
	 | @author : Fabien Beaujean
	 | @description : Session handler
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Session;

	use Gcs\Framework\Core\General\Singleton;

	/**
	 * Class Session
	 * @package Gcs\Framework\Core\Request
	 */

	class Session {
		use singleton;

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
		 */

		private function __construct() {
		}

		/**
		 * singleton
		 * @access public
		 * @return \System\Session\Session
		 * @since 3.0
		 * @package Gcs\Framework\Core\Session
		 */

		public static function instance() {
			if (is_null(self::$_instance)) {
				self::$_instance = new Session();
			}

			return self::$_instance;
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Session
		 */

		public function __destruct() {
		}
	}