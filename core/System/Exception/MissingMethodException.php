<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingMethodException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingMethodException
	 * @package System\Exception
	 */

	class MissingMethodException extends Exception {
		public function getType() {
			return 'MissingMethodException';
		}
	}