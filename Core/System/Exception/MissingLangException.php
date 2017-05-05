<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingConfigException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class MissingLangException
	 * @package Gcs\Framework\Core\Exception
	 */

	class MissingLangException extends Exception {
		public function getType() {
			return 'MissingLangException';
		}
	}