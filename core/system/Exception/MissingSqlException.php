<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingSqlException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Exception;

	class MissingSqlException extends Exception{
		public function getType(){
			return 'MissingSqlException';
		}
	}