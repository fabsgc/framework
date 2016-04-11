<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Define.php
	 | @author : fab@c++
	 | @description : define
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Define;

	use System\Config\Config;
	use System\General\error;

	class Define{
		use error;

		/**
		 * constructor
		 * @access public
		 * @param $src string
		 * @since 3.0
		 * @package System\Define
		*/

		public function __construct ($src){
			foreach(Config::getInstance()->config['define'][''.$src.''] as $key => $value){
				$define = strtoupper($src.'_'.DEFINE_PREFIX.strval($key));

				if (!defined($define)){
					define($define, htmlspecialchars_decode($value));
				}
				else{
					$this->addError('The define '.$define.' is already defined', __FILE__, __LINE__, ERROR_WARNING);
				}
			}
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Define
		*/

		public function __destruct(){
		}
	}