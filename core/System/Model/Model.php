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

	use System\Database\Database;
	use System\Event\EventManager;
	use System\General\error;
	use System\General\facades;
	use System\General\facadesEntity;
	use System\General\facadesHelper;
	use System\General\langs;
	use System\General\ormFunctions;
	use System\General\resolve;

	/**
	 * Class Model
	 * @package System\Model
	 */

	abstract class Model {
		use error, langs, resolve, ormFunctions, facades, facadesEntity, facadesHelper;

		/**
		 * @var \System\Pdo\Pdo
		 */

		public $db;

		/**
		 * @var \System\Event\EventManager
		 */

		public $event;

		/**
		 * Initialization of the model
		 * @access  public
		 * @param $entity \System\General\facadesEntity
		 * @param $helper \System\General\facadesHelper
		 * @since   3.0
		 * @package System\Model
		 */

		final public function __construct($entity, $helper) {
			$this->db = Database::getInstance()->db();
			$this->entity = $entity;
			$this->helper = $helper;

			$this->event = new EventManager($this);
		}

		/**
		 * You can override this method. She is called when the controller instantiate the class
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Model
		 */

		public function init() {
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Model
		 */

		public function __destruct() {
		}
	}