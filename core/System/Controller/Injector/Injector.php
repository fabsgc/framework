<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Injector.php
	 | @author : fab@c++
	 | @description : inject objects in a controller
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Controller\Injector;

	use System\General\facades;
	use System\General\singleton;

	/**
	 * Class Injector
	 * @package System\Controller\Injector
	 */

	class Injector {
		use facades, singleton;

		/**
		 * all class you can inject
		 * @var array
		 */

		private $_alias = [
			'System\Sql\Sql'                    => 'Sql',
			'System\Security\Spam'              => 'Spam',
			'System\Cron\Cron'                  => 'Cron',
			'System\Cache\Cache'                => 'Cache',
			'System\Define\Define'              => 'Define',
			'System\Facade\FacadeHelper'        => 'Helper',
			'System\Facade\FacadeEntity'        => 'Entity',
			'System\Library\Library'            => 'Library',
			'System\Security\Firewall'          => 'Firewall',
			'System\Template\Template'          => 'Template',
			'System\Terminal\Terminal'          => 'Terminal',
			'System\Collection\Collection'      => 'Collection',
			'System\Request\Data'               => 'RequestData',
			'System\AssetManager\AssetManager'  => 'AssetManager',
			'System\Orm\Entity\Multiple'        => 'EntityMultiple',
			'System\Template\TemplateParser'    => 'TemplateParser',
			'System\Form\Validation\Validation' => 'FormValidation',
			'System\Lang\Lang'                  => 'Lang',
			'System\Config\Config'              => 'Config',
			'System\Request\Request'            => 'Request',
			'System\Database\Database'          => 'Database',
			'System\Response\Response'          => 'Response',
			'System\Profiler\Profiler'          => 'Profiler',
		];

		/**
		 * Constructor
		 * @access  public
		 * @since   3.0
		 * @package System\Controller\Injector
		 */

		private function __construct() {
		}

		/**
		 * singleton
		 * @access  public
		 * @since   3.0
		 * @package System\Controller\Injector
		 */

		public static function getInstance() {
			if (is_null(self::$_instance)) {
				self::$_instance = new Injector();
			}

			return self::$_instance;
		}

		/**
		 * Initialization of the application
		 * @access  public
		 * @param &$class object
		 * @param $method string
		 * @return array mixed
		 * @since   3.0
		 * @package System\Controller\Injector
		 */

		public function getArgsMethod(&$class, $method) {
			$params = [];

			$method = new \ReflectionMethod($class, $method);
			$parameters = $method->getParameters();

			foreach ($parameters as $parameter) {
				$object = $parameter->getClass();

				if ($object != null) {
					if (array_key_exists($object->name, $this->_alias)) {
						$name = $this->_alias[$object->name];
						array_push($params, self::$name());
					}
					else if (preg_match('#(Controller\\\Request\\\)#isU', $object->name)) {
						array_push($params, Form::getInstance()->get($object));
					}
					else if (preg_match('#(Orm\\\Entity\\\)#isU', $object->name)) {
						array_push($params, Orm::getInstance()->get($object));
					}
					else {
						array_push($params, null);
					}
				}
				else {
					if (isset($_GET[$parameter->getName()])) {
						array_push($params, $_GET[$parameter->getName()]);
					}
					else if (isset($_POST[$parameter->getName()])) {
						array_push($params, $_POST[$parameter->getName()]);
					}
					else {
						if (!$parameter->isOptional()) {
							array_push($params, null);
						}
					}
				}
			}

			return $params;
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Controller\Injector
		 */

		public function __desctuct() {
		}
	}