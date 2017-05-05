<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MethodNotAllowedException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MethodNotAllowedException
	 * @package System\Exception
	 */

	class MethodNotAllowedException extends Exception {
		public function getType() {
			return 'MethodNotAllowedException';
		}
	}