<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Orm.php
	 | @author : fab@c++
	 | @description : inject and valid Request object
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Controller\Injector;

	use System\General\facades;
	use System\General\singleton;

	class Orm{
		use facades, singleton;

		/**
		 * Constructor
		 * @access public
		 * @since 3.0
		 * @package System\Controller\Injector
		*/

		private function __construct(){

		}

		/**
		 * singleton
		 * @access public
		 * @since 3.0
		 * @package System\Controller\Injector
		*/

		public static function getInstance(){
			if (is_null(self::$_instance))
				self::$_instance = new Orm();

			return self::$_instance;
		}

		/**
		 * Return a fully completed Request Object
		 * @access public
		 * @param \ReflectionClass $object
		 * @return \System\Orm\Entity\Entity
		 * @since 3.0
		 * @package System\Controller\Injector
		*/

		public static function get($object){
			return null;
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Controller\Injector
		*/

		public function __desctuct(){
		}
	}