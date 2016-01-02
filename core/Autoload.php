<?php
	/*\
	 | ------------------------------------------------------
	 | @file : autoload.php
	 | @author : fab@c++
	 | @description : automatic inclusion
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System;

	use System\General\facades;

	require_once(APP_FUNCTION);
	require_once(CLASS_GENERAL);

	class Autoload{
		use facades;

		/**
		 * Autoloader for classes
		 * @param $class string
		 * @return void
		*/

		public static function load($class){
			$class = preg_replace('#'.preg_quote('\\').'#isU', '/', $class);

			if(file_exists(SYSTEM_CORE_PATH.$class.'.php')){
				include_once(SYSTEM_CORE_PATH.$class.'.php');
				return;
			}

			if(file_exists(APP_RESOURCE_PATH.lcfirst(str_replace('Orm/', '', $class)).EXT_ENTITY.'.php')){
				include_once(APP_RESOURCE_PATH.lcfirst(str_replace('Orm/', '', $class)).EXT_ENTITY.'.php');
				return;
			}

			if(file_exists(SRC_PATH.$class.EXT_CONTROLLER.'.php')){
				include_once(SRC_PATH.$class.EXT_CONTROLLER.'.php');
				return;
			}

			if(file_exists(SRC_PATH.preg_replace('#(.*)\/(.*)#isU', '$1/'.SRC_CONTROLLER_PATH.'$2', $class).EXT_CONTROLLER.'.php')){
				include_once(SRC_PATH.preg_replace('#(.*)\/(.*)#isU', '$1/'.SRC_CONTROLLER_PATH.'$2', $class).EXT_CONTROLLER.'.php');
				return;
			}

			if(file_exists(SRC_PATH.preg_replace('#(.*)\/(.*)#isU', '$1/'.SRC_MODEL_PATH.'$2', $class).EXT_MODEL.'.php')){
				include_once(SRC_PATH.preg_replace('#(.*)\/(.*)#isU', '$1/'.SRC_MODEL_PATH.'$2', $class).EXT_MODEL.'.php');
				return;
			}

			if(file_exists(APP_RESOURCE_REQUEST_PATH.preg_replace('#Controller\/Request\/#isU', '', $class).'.php')){
				include_once(APP_RESOURCE_REQUEST_PATH.preg_replace('#Controller\/Request\/#isU', '', $class).'.php');
				return;
			}

			$formRequest = preg_replace('#(Controller\/Request\/)([a-zA-Z]+)(\/)([a-zA-Z]+)#is', '$2', $class);

			if(file_exists(SRC_PATH.strtolower($formRequest).'/'.SRC_RESOURCE_REQUEST_PATH.preg_replace('#Controller\/Request\/'.$formRequest.'\/#isU', '', $class).'.php')){
				include_once(SRC_PATH.strtolower($formRequest).'/'.SRC_RESOURCE_REQUEST_PATH.preg_replace('#Controller\/Request\/'.$formRequest.'\/#isU', '', $class).'.php');
				return;
			}
		}
	}

	spl_autoload_register(__NAMESPACE__ . "\\Autoload::load");