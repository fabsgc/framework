<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingModelException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingModelException
	 * @package System\Exception
	 */

	class MissingModelException extends Exception {
		public function getType() {
			return 'MissingModelException';
		}
	}