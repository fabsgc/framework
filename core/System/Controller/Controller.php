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

	use System\Database\Database;
	use System\Event\EventManager;
	use System\Exception\MissingModelException;
	use System\General\error;
	use System\General\facades;
	use System\General\facadesEntity;
	use System\General\facadesHelper;
	use System\General\langs;
	use System\General\ormFunctions;
	use System\General\resolve;
	use System\Orm\Entity\Entity;
	use System\Request\Request;
	use System\Security\Firewall;
	use System\Security\Spam;
	use System\Template\Template;


	/**
	 * Class Controller
	 * @method Entity entity
	 * @package System\Controller
	 */

	abstract class Controller {
		use error, langs, resolve, ormFunctions, facades, facadesEntity, facadesHelper;

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
		 * @access  public
		 * @since   3.0
		 * @package System\Controller
		 */

		final public function __construct() {
			$this->db = Database::getInstance()->db();

			$this->entity = self::Entity();
			$this->helper = self::Helper();

			$this->event = new EventManager($this);
		}

		/**
		 * You can override this method. She is called before the action
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Controller
		 */

		public function init() {
		}

		/**
		 * You can override this method. She is called after the action
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Controller
		 */

		public function end() {
		}

		/**
		 * check firewall
		 * @access  public
		 * @return bool
		 * @since   3.0
		 * @package System\Controller
		 */

		final public function setFirewall() {
			$firewall = new Firewall();

			if ($firewall->check()) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * check spam
		 * @access  public
		 * @return bool
		 * @since   3.0
		 * @package System\Controller
		 */

		final public function setSpam() {
			$spam = new Spam();

			if ($spam->check()) {
				return true;
			}
			else {
				return false;
			}
		}

		/**
		 * load model
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @throws \System\Exception\MissingModelException
		 * @package System\Controller
		 */

		final public function model() {
			$request = Request::getInstance();
			$class = "\\" . $request->src . "\\" . 'Manager' . ucfirst($request->controller);

			if (class_exists($class)) {
				$this->model = new $class($this->entity, $this->helper);
				$this->model->init();
			}
			else {
				throw new MissingModelException('can\'t load model "' . $request->controller . '"');
			}
		}

		/**
		 * display a default template
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\Controller
		 */

		final public function showDefault() {
			$request = Request::getInstance();
			$t = new Template('.app/system/default', 'systemDefault');
			$t->assign(['action' => $request->src . '::' . $request->controller . '::' . $request->action]);
			return $t->show();
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Controller
		 */

		public function __desctuct() {
		}
	}