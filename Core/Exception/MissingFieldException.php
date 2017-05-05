<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingEntityException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingFieldException
	 * @package System\Exception
	 */

	class MissingFieldException extends Exception {
		public function getType() {
			return 'MissingFieldException';
		}
	}