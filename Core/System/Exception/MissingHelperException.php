<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingHelperException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingHelperException
	 * @package Gcs\Framework\Core\Exception
	 */

	class MissingHelperException extends Exception {
		public function getType() {
			return 'MissingHelperException';
		}
	}