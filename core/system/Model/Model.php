<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Model.php
	 | @author : fab@c++
	 | @description : abstract class. Mother class of all models
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Model;

	use System\General\error;
	use System\General\langs;
	use System\General\facades;
	use System\General\resolve;
	use System\General\url;
	use System\General\ormFunctions;
	use System\General\facadesEntity;
	use System\General\facadesHelper;
	use System\Event\EventManager;

	abstract class Model{
		use error, langs, url, resolve, ormFunctions, facades, facadesEntity, facadesHelper;

		/**
		 * @var \System\Pdo\Pdo
		*/

		public $db   ;

		/**
		 * @var \System\Event\EventManager
		*/

		public $event;

		/**
		 * Initialization of the model
		 * @access public
		 * @param $entity \system\General\facadesEntity
		 * @param $helper \system\General\facadesHelper
		 * @since 3.0
		 * @package system
		*/
		
		final public function __construct($entity, $helper){
			$this->db       =   self::Database()->db();
			$this->entity   =   $entity;
			$this->helper   =   $helper;

			$this->event = new eventManager($this);
		}

		/**
		 * You can override this method. She is called when the controller instantiate the class
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package system
		*/
			
		public function init(){	
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package system
		*/
		
		public function __destruct(){
		}
	}