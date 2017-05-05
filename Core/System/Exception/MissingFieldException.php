<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingEntityException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingFieldException
	 * @package System\Exception
	 */

	class MissingFieldException extends Exception {
		public function getType() {
			return 'MissingFieldException';
		}
	}