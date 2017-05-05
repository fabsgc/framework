<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingDatabaseException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingDatabaseException
	 * @package System\Exception
	 */

	class MissingDatabaseException extends Exception {
		public function getType() {
			return 'MissingDatabaseException';
		}
	}