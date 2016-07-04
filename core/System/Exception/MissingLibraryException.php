<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingLibraryException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	/**
	 * Class MissingLibraryException
	 * @package System\Exception
	 */

	class MissingLibraryException extends Exception {
		public function getType() {
			return 'MissingLibraryException';
		}
	}