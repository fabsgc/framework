<?php
	/*\
	 | ------------------------------------------------------
	 | @file : NotFoundException.php
	 | @author : Fabien Beaujean
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Exception;

	/**
	 * Class NotFoundException
	 * @package Gcs\Framework\Core\Exception
	 */

	class NotFoundException extends Exception {
		public function getType() {
			return 'NotFoundException';
		}
	}