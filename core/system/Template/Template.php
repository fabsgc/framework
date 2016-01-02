<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Template.php
	 | @author : fab@c++
	 | @description : template engine
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Template;

	use System\General\error;
	use System\General\langs;
	use System\General\facades;
	use System\General\url;
	use System\General\resolve;
	use System\Profiler\Profiler;
	use System\Exception\MissingTemplateException;

	class Template{
		use error, langs, facades, url, resolve;

		/**
		 * path to the .tpl file
		 * @var string
		*/

		protected $_file;

		/**
		 * path to the .compil.tpl file
		 * @var string
		*/

		protected $_fileCache;

		/**
		 * template cache file name
		 * @var string
		*/

		protected $_name;

		/**
		 * content of the .tpl
		 * @var string
		*/

		protected $_content;

		/**
		 * compiled content of the .tpl
		 * @var string
		*/

		protected $_contentCompiled;

		/**
		 * list of variables
		 * @var array
		*/

		public $vars = [];

		/**
		 * time cache
		 * @var integer
		*/

		protected $_timeCache = 0;

		/**
		 * last update of the .tpl
		 * @var integer
		*/

		protected $_timeFile = 0;

		/**
		 * type of tpl stream : file or string
		 * @var integer
		*/

		protected $_stream = 1;

		/**
		 * reference to the parser instance
		 * @var \System\Template\TemplateParser
		*/

		protected $_parser = null;

		/**
		 * reference to the parser instance
		 * @var array
		*/

		public static $_extends = [];

		const TPL_FILE               = 0; //we can load a .tpl as template
		const TPL_STRING             = 1; //we can load a string as template
		const TPL_COMPILE_ALL        = 0;
		const TPL_COMPILE_INCLUDE    = 1;
		const TPL_COMPILE_LANG       = 2;
		const TPL_COMPILE_TO_INCLUDE = 0;
		const TPL_COMPILE_TO_STRING  = 1;

		/**
		 * constructor
		 * @access public
		 * @param $file string : file path or content
		 * @param $name string : template name
		 * @param $cache int : cache time
		 * @param $stream int : use a file or a string
		 * @throws \System\Exception\MissingTemplateException if the tpl file can't be read
		 * @return \System\Template\Template
		 * @since 3.0
		 * @package System\Template
		*/

		public function __construct ($file = '', $name = 'template', $cache = 0, $stream = self::TPL_FILE){
			$this->_createlang();
			$this->_file = $this->resolve(RESOLVE_TEMPLATE, $file).EXT_TEMPLATE;
			$this->_name      = $name;
			$this->_timeCache = $cache;
			$this->_stream    = $stream;

			if(CACHE_ENABLED == false)
				$this->_timeCache = 0;

			if(!preg_match('#(tplInclude)#isU', $name)){
				$stack = debug_backtrace(0);
				$trace = $this->getStackTraceToString($stack);
				$this->_name .= $trace;
			}
			else{
				$trace = '';
				$this->_name .= $trace;
			}

			if($this->_stream == self::TPL_FILE){
				if(file_exists($this->_file)){
					if (!file_exists(APP_CACHE_PATH_TEMPLATE))
						mkdir(APP_CACHE_PATH_TEMPLATE, 0755, true);

					$hash = sha1(preg_replace('#/#isU', '', $file));
				}
				else{
					throw new MissingTemplateException('can\'t open template file "'.$this->_file.'"');
				}
			}
			else{
				$this->_content = $file;
				$hash = '';
			}

			if(CACHE_SHA1 == 'true')
				$this->_fileCache = APP_CACHE_PATH_TEMPLATE.sha1(substr($hash, 0, 10).'_template_'.$this->_name.EXT_COMPILED_TEMPLATE);
			else
				$this->_fileCache = APP_CACHE_PATH_TEMPLATE.substr($hash, 0, 10).'_template_'.$this->_name.EXT_COMPILED_TEMPLATE;

			$this->_setParser();

			return $this;
		}

		/**
		 * permit to extend the template engine with custom functions
		 * @access public
		 * @param $method mixed array,string
		 * @throws MissingTemplateException
		 * @return void
		 * @since 3.0
		 * @package System\Template
		*/

		public static function extend($method){
			if(is_array($method)){
				if(count($method) == 2 && method_exists($method[0], $method[1])){
					array_push(self::$_extends, $method);
				}
				else{
					throw new MissingTemplateException('You c\'ant extend the template engine with the method : "'.$method[0].'"');
				}
			}
			else{
				$trace = debug_backtrace();

				if (isset($trace[1])) {
					array_push(self::$_extends, ['\\'.$trace[1]['class'], $method]);
				}
				else{
					throw new MissingTemplateException('Can\'t reach the method "'.$method.'"');
				}
			}
		}

		/**
		 * get the trace of execution. it's used to give an explicit name to the caching file
		 * @access protected
		 * @param $stack array
		 * @return string
		 * @since 3.0
		 * @package System\Template
		*/

		private function getStackTraceToString($stack){
			$max = 0;
			$trace = '';

			for($i = 3; $i < count($stack) && $max < 4; $i++){
				if(isset($stack[$i]['file']) && preg_match('#('.preg_quote('System\Orm').')#isU', $stack[$i]['file'])){ //ORM
					$trace .= str_replace('\\', '-', $stack[$i]['class']).'_'.$stack[$i]['function'].'_'.$stack[$i-1]['line'].'__';
				}
			}

			return $trace;
		}

		/**
		 * initialize the parser instance reference
		 * @return void
		 * @since 3.0
		 * @package System\Template
		*/

		protected function _setParser(){
			$this->_parser = $this->TemplateParser($this);
		}

		/**
		 * insert variable
		 * @param $name
		 * @param $vars
		 * @return \System\Template\Template
		 * @since 3.0
		 * @package System\Template
		*/

		public function assign($name, $vars = ''){
			if(is_array($name))
				$this->vars = array_merge($this->vars, $name);
			else
				$this->vars[$name] = $vars;

			return $this;
		}

		/**
		 * compile the template instance
		 * @param $content string
		 * @param $type int
		 * @return mixed
		 * @since 3.0
		 * @package System\Template
		*/

		protected function _compile($content, $type = self::TPL_COMPILE_ALL){
			switch ($type) {
				case self::TPL_COMPILE_ALL:
					return $this->_parser->parse($content);
				break;

				case self::TPL_COMPILE_INCLUDE:
					return $this->_parser->parseNoTemplate($content);
				break;

				case self::TPL_COMPILE_LANG:
					return $this->_parser->parseLang($content);
				break;
			}

			return '';
		}

		/**
		 * save content in cache file
		 * @param $content
		 * @return void
		 * @since 3.0
		 * @package System\Template
		*/

		protected function _save($content){
			file_put_contents($this->_fileCache, $content);
		}

		/**
		 * @param $returnType : make a include or eval the template
		 * @param int $type
		 * @return mixed
		 * @since 3.0
		 * @package System\Template
		*/

		public function show($returnType = self::TPL_COMPILE_TO_STRING, $type = self::TPL_COMPILE_ALL){
			$profiler = self::Profiler();

			$profiler->addTime('template '.$this->_name);
			$profiler->addTemplate($this->_name, Profiler::TEMPLATE_START, $this->_file);

			foreach ($this->vars as $cle => $valeur){
				${$cle} = $valeur;
			}

			if($this->_timeCache > 0 && file_exists($this->_fileCache)){
				$this->_timeFile = filemtime($this->_fileCache);

				if(($this->_timeFile + $this->_timeCache) <= time()){
					if($this->_stream == self::TPL_FILE){
						$this->_content = file_get_contents($this->_file);
					}

					$this->_contentCompiled = $this->_compile($this->_content, $type);
					$this->_save($this->_contentCompiled);
				}
			}
			else{
				if($this->_stream == self::TPL_FILE){
					$this->_content = file_get_contents($this->_file);
				}

				$this->_contentCompiled = $this->_compile($this->_content, $type);
				$this->_save($this->_contentCompiled);
			}

			if($returnType == self::TPL_COMPILE_TO_INCLUDE){
				if($type != self::TPL_COMPILE_INCLUDE){
					require_once($this->_fileCache);
				}
			}

			$profiler->addTemplate($this->_name, Profiler::TEMPLATE_END, $this->_file);
			$profiler->addTime('template '.$this->_name, Profiler::USER_END);

			if($returnType == self::TPL_COMPILE_TO_STRING){
				ob_start();
					require_once($this->_fileCache);
				$output = ob_get_contents();
				ob_get_clean();

				return $output;
			}

			return '';
		}

		/**
		 * get file path
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Template
		*/

		public function getFile(){
			return $this->_file;
		}

		/**
		 * get file path cache
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Template
		*/

		public function getFileCache(){
			return $this->_fileCache;
		}

		/**
		 * get tpl name
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Template
		*/

		public function getName(){
			return $this->_name;
		}

		/**
		 * destructor
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Template
		*/

		public function __destruct(){
		}
	}