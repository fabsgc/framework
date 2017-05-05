<?php
	/*\
	 | ------------------------------------------------------
	 | @file : AnnotationAttributeNotExistingException
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class AnnotationAttributeNotExistingException
	 * @package System\Exception
	 */

	class AnnotationAttributeNotExistingException extends Exception {
		public function getType() {
			return 'AnnotationNotExistingException';
		}
	}