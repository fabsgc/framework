<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingTerminalException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingTerminalException
	 * @package System\Exception
	 */

	class MissingTerminalException extends Exception {
		public function getType() {
			return 'MissingTerminalException';
		}
	}