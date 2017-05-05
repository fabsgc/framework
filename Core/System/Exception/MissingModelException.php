<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingModelException.php
	 | @author : Fabien Beaujean
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