<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingActionException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingActionException
	 * @package System\Exception
	 */

	class MissingActionException extends Exception {
		public function getType() {
			return 'MissingActionException';
		}
	}