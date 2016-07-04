<?php
	/*\
	 | ------------------------------------------------------
	 | @file : FacadeEntity.php
	 | @author : fab@c++
	 | @description : easier way to instantiate entities
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Facade;

	use System\Exception\MissingEntityException;

	/**
	 * Class FacadeEntity
	 * @package System\Facade
	 */

	class FacadeEntity {
		/**
		 * Constructor
		 * @access  public
		 * @since   3.0
		 * @package System\Facade
		 */

		final public function __construct() {
		}

		/**
		 * instantiate the good entity
		 * @access  public
		 * @param $name      string
		 * @param $arguments array
		 * @throws \System\Exception\MissingEntityException
		 * @return \System\Orm\Entity\Entity
		 * @since   3.0
		 * @package System\Facade
		 */

		public function __call($name, $arguments) {
			if (file_exists(APP_RESOURCE_ENTITY_PATH . $name . EXT_ENTITY . '.php')) {
				include_once(APP_RESOURCE_ENTITY_PATH . $name . EXT_ENTITY . '.php');

				$class = '\Orm\Entity\\' . $name;

				$params = [];

				foreach ($arguments as $value) {
					array_push($params, $value);
				}

				$reflect = new \ReflectionClass($class);
				return $reflect->newInstanceArgs($params);
			}
			else {
				$file = '';
				$line = '';
				$stack = debug_backtrace(0);
				$trace = $this->getStackTraceFacade($stack);

				foreach ($trace as $value) {
					if ($value['function'] == $name) {
						$file = $value['file'];
						$line = $value['line'];
						break;
					}
				}

				throw new MissingEntityException('undefined entity "' . $name . '" in "' . $file . '" line ' . $line);
			}
		}

		/**
		 * @param $string
		 * @return mixed
		 * @since   3.0
		 * @package System\Facade
		 */

		public function getStackTraceFacade($string) {
			return $string;
		}

		/**
		 * Destructor
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\Facade
		 */

		public function __destruct() {
		}
	}