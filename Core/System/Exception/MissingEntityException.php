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
	 * Class MissingEntityException
	 * @package System\Exception
	 */

	class MissingEntityException extends Exception {
		public function getType() {
			return 'MissingEntityException';
		}
	}