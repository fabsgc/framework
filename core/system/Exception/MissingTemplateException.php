<?php
	/*\
	 | ------------------------------------------------------
	 | @file : MissingTemplateException.php
	 | @author : fab@c++
	 | @description : overriding of php exceptions
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Exception;

	class MissingTemplateException extends Exception{
		public function getType(){
			return 'MissingTemplateException';
		}
	}