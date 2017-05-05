<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingClassException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingClassException
	 * @package System\Exception
	 */

	class MissingClassException extends Exception {
		public function getType() {
			return 'MissingClassException';
		}
	}