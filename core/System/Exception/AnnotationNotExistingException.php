<?php
	/*\
	 | ------------------------------------------------------
	 | @file : AnnotationNotExistingException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class AnnotationNotExistingException
	 * @package System\Exception
	 */

	class AnnotationNotExistingException extends Exception {
		public function getType() {
			return 'AnnotationNotExistingException';
		}
	}