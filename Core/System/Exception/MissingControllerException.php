<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingControllerException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingControllerException
	 * @package Gcs\Framework\Core\Exception
	 */

	class MissingControllerException extends Exception {
		public function getType() {
			return 'MissingControllerException';
		}
	}