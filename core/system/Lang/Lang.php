<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Lang.php
	 | @author : fab@c++
	 | @description : allow to use translation in the application
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Lang;

	use System\General\error;
	use System\General\facades;
	use System\General\resolve;
	use System\General\singleton;
	use System\Template\Template;

    class Lang{
		use error, facades, resolve, singleton;

		const USE_NOT_TPL    = 0;
		const USE_TPL        = 1;
		
		/**
		 * init Lang class
		 * @access public
		 * @since 3.0
		 * @package System\Lang
		*/
		
		public function __construct(){
		}

		/**
		 * singleton
		 * @access public
		 * @since 3.0
		 * @package System\Request
		 */

		public static function getInstance(){
			if (is_null(self::$_instance))
				self::$_instance = new Lang();

			return self::$_instance;
		}

		/**
		 * load a sentence from config instance
		 * @access public
		 * @param $name string : name of the sentence
		 * @param $vars array : vars
		 * @param $template bool|int : use template syntax or not
		 * @return string
		 * @since 3.0
		 * @package System\Lang
		*/
		
		public function lang($name, $vars = array(), $template = self::USE_NOT_TPL){
			$request = self::Request();
			$config = $this->resolve(RESOLVE_LANG, $name);
			$name = $config[1];
			$config = $config[0];

			if(isset($config[$request->lang][$name])){
				if($template == self::USE_NOT_TPL){
					if(count($vars) == 0){
						return $config[$request->lang][$name];
					}
					else{
						$content = $config[$request->lang][$name];
								
						foreach($vars as $key => $value){
							$content = preg_replace('#\{'.$key.'\}#isU', $value, $content);
						}

						return $content;
					}
				}
				else{
					$tpl = self::Template($config[$request->lang][$name], $name, 0, Template::TPL_STRING);
					$tpl->assign($vars);
					return $tpl->show(Template::TPL_COMPILE_TO_STRING, Template::TPL_COMPILE_LANG);
				}
			}
			else{
				$this->addError('lang '.$name.'/'.$request->lang.' not found', __FILE__, __LINE__, ERROR_WARNING);
				return 'lang not found ('.$name.','.$request->lang.')';
			}
		}

		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Lang
		*/
		
		public function __destruct(){
		}
	}