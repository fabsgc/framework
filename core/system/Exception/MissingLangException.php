<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingConfigException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Exception;

	class MissingConfigException extends Exception{
		public function getType(){
			return 'MissingConfigException';
		}
	}