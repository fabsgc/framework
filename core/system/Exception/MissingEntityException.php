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

	class MissingEntityException extends Exception{
		public function getType(){
			return 'MissingEntityException';
		}
	}