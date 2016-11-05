<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingConfigException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingLangException
	 * @package System\Exception
	 */

	class MissingLangException extends Exception {
		public function getType() {
			return 'MissingLangException';
		}
	}