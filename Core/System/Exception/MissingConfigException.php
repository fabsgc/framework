<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingLangException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingLangException
	 * @package System\Exception
	 */

	class MissingConfigException extends Exception {
		public function getType() {
			return 'MissingConfigException';
		}
	}