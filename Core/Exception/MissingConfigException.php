<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingLangException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingLangException
	 * @package System\Exception
	 */

	class MissingConfigException extends Exception {
		public function getType() {
			return 'MissingConfigException';
		}
	}