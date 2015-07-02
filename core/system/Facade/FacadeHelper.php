<?php
	/*\
	 | ------------------------------------------------------
	 | @file : FacadeHelper.php
	 | @author : fab@c++
	 | @description : helper facades : permit to manipulate easily helpers class
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Facade;

	use System\General\error;
	use System\General\langs;
	use System\General\facades;
	use System\Exception\MissingHelperException;

	class FacadeHelper {
		use error, facades, langs;

		/** 
		 * list of the class aliases and the real classes behind
		 * @var array
		*/

		private $_alias = array(
			'Pagination' => '\helper\Pagination\Pagination',
			'Mail'       =>             '\helper\Mail\Mail',
			'Alert'      =>            '\helper\Alert\Alert'
		);

		/**
		 * Constructor
		 * @access public
		 * @since 3.0
		 * @package System\Facade
		*/

		final public function __construct(){
		}

		/**
		 * instantiate the good helper
		 * @access public
		 * @param $name string : helper class name
		 * @param $arguments array : helper class arguments
		 * @return object
		 * @throws \System\Exception\MissingHelperException when the helper doesn't exist
		 * @since 3.0
		 * @package System\Facade
		*/

		public function __call($name, $arguments){
			if(array_key_exists($name, $this->_alias)){
				$params = array();

				for($i = 0;$i < count($arguments);$i++){
					$params[$i+5] = $arguments[$i];
				}

				$reflect  = new \ReflectionClass($this->_alias[$name]);
				return $reflect->newInstanceArgs($params);
			}
			else{
				$file = '';
				$line = '';
				$stack = debug_backtrace(0);
				$trace = $this->getStackTraceFacade($stack);

				foreach ($trace as $value) {
					if($value['function'] == $name){
						$file = $value['file'];
						$line = $value['line'];
						break;
					}
				}

				throw new MissingHelperException('undefined helper "'.$name.'" in "'.$file.'" line '.$line);
			}
		}

		/**
		 * @param $string
		 * @return mixed
		 * @since 3.0
		 * @package System\Facade
		*/

		public function getStackTraceFacade($string){
			return $string;
		}

		/**
		 * Destructor
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Facade
		*/

		public function __destruct(){
		}
	}