<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingEntityException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Exception;

	class MissingFieldException extends Exception{
		public function getType(){
			return 'MissingFieldException';
		}
	}