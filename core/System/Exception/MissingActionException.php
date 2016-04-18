<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingActionException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Exception;

	class MissingActionException extends Exception{
		public function getType(){
			return 'MissingActionException';
		}
	}