<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Library.php
	 | @author : fab@c++
	 | @description : library
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Library;

	use System\General\error;
	use System\General\langs;
	use System\General\facades;
	use System\Exception\MissingLibraryException;

	class Library{
		use error, langs, facades;

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @throws \System\Exception\MissingLibraryException
		 * @package System\Library
		*/

		public function __construct ($src){
			$config = self::Config();

			foreach($config->config['library'][''.$src.''] as $value){
				if($value['enabled'] == 'true'){
					if($this->_checkInclude($value['include']) == true){
						if($src == 'app'){
							$file = APP_RESOURCE_LIBRARY_PATH.$value['access'];
						}
						else{
							$file = SRC_PATH.$src.'/'.SRC_RESOURCE_LIBRARY_PATH.$value['access'];
						}

						if(file_exists($file)){
							require_once($file);
							$this->addError('The library '.$file.' was successfully included', __FILE__, __LINE__, ERROR_INFORMATION, LOG_SYSTEM);
						}
						else{
							throw new MissingLibraryException('The library '.$file.' could not be included');
						}
					}
				}
			}
		}

		/**
		 * check if the library can be included
		 * @access protected
		 * @param $include string
		 * @return boolean
		 * @since 3.0
		 * @package System\Library
		*/

		protected function _checkInclude($include){
			$request = self::Request();
			
			if($include == '*'){
				return true;
			}
			else if(preg_match('#no\[(.*)\]#isU', $include, $matches)){
				$match = array_map('trim', explode(',', $matches[1]));

				if(
					in_array('.'.$request->src, $match) || 
					in_array('.'.$request->src.'.'.$request->controller, $match) || 
					in_array('.'.$request->src.'.'.$request->controller.'.'.$request->action, $match) ||
					in_array($request->controller, $match) || 
					in_array($request->controller.'.'.$request->action, $match)
				){
					return false;
				}
				else{
					return true;
				}
			}
			else if(preg_match('#yes\[(.*)\]#isU', $include, $matches)){
				$match = explode(',', $matches[1]);

				if(
					in_array('.'.$request->src, $match) || 
					in_array('.'.$request->src.'.'.$request->controller, $match) || 
					in_array('.'.$request->src.'.'.$request->controller.'.'.$request->action, $match) ||
					in_array($request->controller, $match) || 
					in_array($request->controller.'.'.$request->action, $match)
				){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Library
		*/

		public function __destruct(){
		}
	}