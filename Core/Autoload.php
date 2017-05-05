<?php
	/*\
	 | ------------------------------------------------------
	 | @file : autoload.php
	 | @author : Fabien Beaujean
	 | @description : automatic inclusion
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework;

	use Gcs\Framework\Core\General\facades;

	/**
	 * Class Autoload
	 * @package System
	 */

	class Autoload {
		use facades;

		/**
		 * Autoloader for classes
		 * @param $class string
		 * @return void
		 */

		public static function load($class) {
			$class = preg_replace('#' . preg_quote('\\') . '#isU', '/', $class);

			if (file_exists(SYSTEM_CORE_PATH . $class . '.php')) {
				include_once(SYSTEM_CORE_PATH . $class . '.php');
				return;
			}

			if (file_exists(APP_RESOURCE_PATH . lcfirst(str_replace('Orm/', '', $class)) . '.php')) {
				include_once(APP_RESOURCE_PATH . lcfirst(str_replace('Orm/', '', $class)) . '.php');
				return;
			}

			if (file_exists(SRC_PATH . $class . '.php')) {
				include_once(SRC_PATH . $class . '.php');
				return;
			}

			if (file_exists(SRC_PATH . preg_replace('#(.*)\/(.*)#isU', lcfirst('$1/') . SRC_CONTROLLER_PATH . '$2', $class) . '.php')) {
				include_once(SRC_PATH . preg_replace('#(.*)\/(.*)#isU', lcfirst('$1/') . SRC_CONTROLLER_PATH . '$2', $class) . '.php');
				return;
			}

			if (file_exists(APP_RESOURCE_REQUEST_PATH . preg_replace('#Controller\/Request\/#isU', '', $class) . '.php')) {
				include_once(APP_RESOURCE_REQUEST_PATH . preg_replace('#Controller\/Request\/#isU', '', $class) . '.php');
				return;
			}

			$formRequest = preg_replace('#(Controller\/Request\/)([a-zA-Z]+)(\/)([a-zA-Z]+)#is', '$2', $class);

			if (file_exists(SRC_PATH . strtolower($formRequest) . '/' . SRC_RESOURCE_REQUEST_PATH . preg_replace('#Controller\/Request\/' . $formRequest . '\/#isU', '', $class) . '.php')) {
				include_once(SRC_PATH . strtolower($formRequest) . '/' . SRC_RESOURCE_REQUEST_PATH . preg_replace('#Controller\/Request\/' . $formRequest . '\/#isU', '', $class) . '.php');
				return;
			}
		}
	}

	spl_autoload_register(__NAMESPACE__ . "\\Autoload::load");