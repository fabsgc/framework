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
	use System\Request\Request;

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
			$class = $object->name;

			/** @var \System\Orm\Entity\Entity $class */
			$class = new $class();
			$class->hydrate();

			$request = Request::getInstance();

			if(($class->getForm() == '' && $request->data->form == true) || isset($request->data->post[$class->getForm()])){
				switch($request->data->method){
					case 'get' :
						$class->beforeInsert();
					break;

					case 'post' :
						$class->beforeInsert();
					break;

					case 'put' :
						$class->beforeUpdate();
					break;

					case 'delete' :
						$class->beforeDelete();
					break;
				}

				$class->check();
			}

			return $class;
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