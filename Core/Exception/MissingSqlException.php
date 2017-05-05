<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingSqlException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingSqlException
	 * @package Gcs\Framework\Core\Exception
	 */

	class MissingSqlException extends Exception {
		public function getType() {
			return 'MissingSqlException';
		}
	}