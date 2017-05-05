<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingSqlException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingSqlException
	 * @package System\Exception
	 */

	class MissingSqlException extends Exception {
		public function getType() {
			return 'MissingSqlException';
		}
	}