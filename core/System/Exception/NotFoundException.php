<?php
	/*\
	 | ------------------------------------------------------
	 | @file : NotFoundException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class NotFoundException
	 * @package System\Exception
	 */

	class NotFoundException extends Exception {
		public function getType() {
			return 'NotFoundException';
		}
	}