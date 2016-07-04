<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingControllerException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingControllerException
	 * @package System\Exception
	 */

	class MissingControllerException extends Exception {
		public function getType() {
			return 'MissingControllerException';
		}
	}