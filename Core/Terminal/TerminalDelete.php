<?php
	/*\
	 | ------------------------------------------------------
	 | @file : TerminalDelete.php
	 | @author : Fabien Beaujean
	 | @description : terminal command delete
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Terminal;

	/**
	 * Class TerminalDelete
	 * @package Gcs\Framework\Core\Terminal
	 */

	class TerminalDelete extends TerminalCommand {
		public function module() {
			$src = '';

			//choose the module name
			while (1 == 1) {
				echo ' - choose the module you want to delete : ';
				$src = ArgvInput::get();

				if (file_exists(DOCUMENT_ROOT . SRC_PATH . $src . '/')) {
					break;
				}
				else {
					echo " - [ERROR] this module doesn't exist\n";
				}
			}
			
			Terminal::rrmdir(SRC_PATH . $src, true);
			Terminal::rrmdir(WEB_PATH . $src, true);
			rmdir(SRC_PATH . $src);
			rmdir(WEB_PATH . $src);

			echo ' - the module has been successfully delete';
		}

		/**
		 * return void
		 * @access public
		 */

		public function controller() {
			$src = '';
			$controllers = [];

			//choose the module name
			while (1 == 1) {
				echo ' - choose module : ';
				$src = ArgvInput::get();

				if (file_exists(DOCUMENT_ROOT . SRC_PATH . $src . '/')) {
					break;
				}
				else {
					echo " - [ERROR] this module doesn't exist\n";
				}
			}

			//choose the controllers
			while (1 == 1) {
				echo ' - choose a controller (keep empty to stop) : ';
				$controller = ArgvInput::get();

				if ($controller != '') {
					if (!in_array($controller, $controllers) AND file_exists(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_CONTROLLER_PATH . '/' . ucfirst($controller) . '.php')) {
						array_push($controllers, $controller);
					}
					else {
						echo " - [ERROR] you have already chosen this controller or it isn't created.\n";
					}
				}
				else {
					if (count($controllers) > 0) {
						break;
					}
					else {
						echo " - [ERROR] you must add at least one controller\n";
					}
				}
			}

			foreach ($controllers as $value) {
				unlink(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_CONTROLLER_PATH . ucfirst($value) . '.php');

				echo " - the controller " . $value . " have been successfully deleted";
			}
		}
	}