<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Cron.php
	 | @author : fab@c++
	 | @description : cron
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Cron;

	use System\General\di;
	use System\Config\Config;
	use System\General\error;
	use System\Engine\Engine;
	use System\General\facades;
	use System\Request\Request;
	use System\Profiler\Profiler;
	use System\Database\Database;
	use System\Response\Response;
	use System\Exception\MissingConfigException;

	class Cron{
		use error, facades, di;

		/**
		 * @var boolean
		*/

		protected $_xmlValid   =  true;

		/**
		 * @var string
		*/

		protected $_xmlContent =    '';

		/**
		 * @var boolean
		*/

		protected $_exception  = false;

		/**
		 * constructor
		 * @access public
		 * @param $file string : file path
		 * @throws \System\Exception\MissingConfigException
		 * @since 3.0
		 * @package System\Cron
		*/

		public function __construct ($file){
			$this->config   =   Config::getInstance();
			$this->request  =  Request::getInstance();
			$this->response = Response::getInstance();
			$this->profiler = Profiler::getInstance();

			if(@fopen($file, 'r+')) {
				if($this->_xmlContent = simplexml_load_file($file)){
					if($this->_exception() == false){
						$crons =  $this->_xmlContent->xpath('//cron');

						foreach ($crons as $value) {
							if ($value['executed'] + $value['time'] < time() || $value['time'] == 0){
								Config::$_instance   = null;
								Request::$_instance  = null;
								Response::$_instance = null;
								Profiler::$_instance = null;

								$value['executed'] = time();
								$dom = new \DOMDocument("1.0");
								$dom->preserveWhiteSpace = false;
								$dom->formatOutput = true;
								$dom->loadXML($this->_xmlContent->asXML());
								$dom->save($file);

								$action = explode('.', $value['action']);
								$controller = new Engine();
								$controller->initCron($action[0], $action[1], $action[2], Database::getInstance()->db());

								ob_start();
									$controller->runCron();
									$output = ob_get_contents();
								ob_get_clean();

								$this->addError('['.$value['action']."]\n[".$output."]",  0, 0, 0, LOG_CRONS);
								$this->addError('CRON '.$value['action'].' called successfully ', __FILE__, __LINE__, ERROR_INFORMATION);
							}
						}
					}
					else{
						$this->addError('CRON : the page is an exception ', __FILE__, __LINE__, ERROR_INFORMATION);
					}
				}
				else{
					$this->_xmlValid = true;
					throw new MissingConfigException('Can\'t open file "'.$file.'"');
				}
			}

			Config::$_instance   =   $this->config;
			Request::$_instance  =  $this->request;
			Response::$_instance = $this->response;
			Profiler::$_instance = $this->profiler;
		}

		/**
		 * return if the current page which calls crons is an exception
		 * @access protected
		 * @return boolean
		 * @since 3.0
		 * @package System\Cron
		*/

		protected function _exception(){
			$exceptions =  $this->_xmlContent->xpath('//exception');

			foreach ($exceptions as $value) {
				if($this->request->src.'.'.$this->request->controller.'.'.$this->request->action == $value['action']){
					return true;
				}
			}

			return false;
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Cron
		*/

		public function __destruct(){
		}
	}