<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Controller.php
	 | @author : fab@c++
	 | @description : abstract class. Mother class of all controllers
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Controller;

	use System\Orm\Entity\Entity;
	use System\General\error;
	use System\General\langs;
	use System\General\facades;
	use System\General\resolve;
	use System\General\url;
	use System\General\ormFunctions;
	use System\General\facadesEntity;
	use System\General\facadesHelper;
	use System\Event\EventManager;
	use System\Exception\MissingModelException;

	/**
	 * @method Entity entity
	*/
	abstract class Controller{
		use error, langs, url, resolve, ormFunctions, facades, facadesEntity, facadesHelper;

		/**
		 * @var \System\Model\Model
		*/

		public $model;

		/**
		 * @var \System\Pdo\Pdo
		*/

		public $db;

		/**
		 * @var \System\Event\EventManager
		*/

		public $event;
		
		/**
		 * Initialization of the application
		 * @access public
		 * @since 3.0
		 * @package System\Controller
		*/

		final public function __construct(){
			$this->db = self::Database()->db();
			$this->_createlang();
			
			$this->entity = self::Entity();
			$this->helper = self::Helper();

			$this->event = new EventManager($this);
		}
		
		/**
		 * You can override this method. She is called before the action
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Controller
		*/

		public function init(){	
		}

		/**
		 * You can override this method. She is called after the action
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Controller
		*/

		public function end(){	
		}

		/**
		 * check firewall
		 * @access public
		 * @return bool
		 * @since 3.0
		 * @package System\Controller
		*/
		
		final public function setFirewall(){
			$firewall = self::Firewall();
			
			if($firewall->check())
				return true;
			else
				return false;
		}

		/**
		 * check spam
		 * @access public
		 * @return bool
		 * @since 3.0
		 * @package System\Controller
		*/

		final public function setSpam(){
			$spam = self::Spam();
			
			if($spam->check())
				return true;
			else
				return false;
		}

		/**
		 * load model
		 * @access public
		 * @return void
		 * @since 3.0
		 * @throws \System\Exception\MissingModelException
		 * @package System\Controller
		*/
		
		final public function model(){
			$request = self::Request();
			$class = "\\".$request->src."\\".'Manager'.ucfirst($request->controller);
			
			if(class_exists($class)){
				$this->model = new $class($this->entity, $this->helper);
				$this->model->init();
			}
			else{
				throw new MissingModelException('can\'t load model "'.$request->controller.'"');
			}
		}

		/**
		 * display a default template
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Controller
		 */

		final public function showDefault(){
			$request = self::Request();
			$t = self::Template('.app/system/default', 'systemDefault');
			$t->assign(array('action' => $request->src.'::'.$request->controller.'::'.$request->action));
			return $t->show();
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Controller
		*/

		public function __desctuct(){
		}
	}