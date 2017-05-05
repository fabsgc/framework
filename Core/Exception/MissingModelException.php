<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingModelException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingModelException
	 * @package Gcs\Framework\Core\Exception
	 */

	class MissingModelException extends Exception {
		public function getType() {
			return 'MissingModelException';
		}
	}