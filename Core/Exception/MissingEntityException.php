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
	 * Class MissingEntityException
	 * @package Gcs\Framework\Core\Exception
	 */

	class MissingEntityException extends Exception {
		public function getType() {
			return 'MissingEntityException';
		}
	}