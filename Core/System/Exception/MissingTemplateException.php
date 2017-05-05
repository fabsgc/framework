<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingTemplateException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingTemplateException
	 * @package System\Exception
	 */

	class MissingTemplateException extends Exception {
		public function getType() {
			return 'MissingTemplateException';
		}
	}