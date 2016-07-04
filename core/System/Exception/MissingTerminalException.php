<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingTerminalException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingTerminalException
	 * @package System\Exception
	 */

	class MissingTerminalException extends Exception {
		public function getType() {
			return 'MissingTerminalException';
		}
	}