<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingHelperException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingHelperException
	 * @package System\Exception
	 */

	class MissingHelperException extends Exception {
		public function getType() {
			return 'MissingHelperException';
		}
	}