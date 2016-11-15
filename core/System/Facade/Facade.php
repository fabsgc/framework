<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Facades.php
	 | @author : fab@c++
	 | @description : loader system to load core and helper classes
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Facade;

	use System\Exception\Exception;

	/**
	 * Class Facade
	 * @package System\Facade
	 */

	class Facade {
		/**
		 * list of the class alias and the real class behind
		 * @var array
		 */

		private static $_alias = [
			'Sql'            => '\System\Sql\Sql',
			'Spam'           => '\System\Security\Spam',
			'Cron'           => '\System\Cron\Cron',
			'Cache'          => '\System\Cache\Cache',
			'Helper'         => '\System\Facade\FacadeHelper',
			'Entity'         => '\System\Facade\FacadeEntity',
			'Library'        => '\System\Library\Library',
            'Session'        => '\System\Session\Session',
			'Firewall'       => '\System\Security\Firewall',
			'Template'       => '\System\Template\Template',
			'Terminal'       => '\System\Terminal\Terminal',
			'AssetManager'   => '\System\AssetManager\AssetManager',
			'OrmValidation'  => '\System\Orm\Validation\Validation',
			'EntityMultiple' => '\System\Orm\Entity\Multiple',
			'TemplateParser' => '\System\Template\Parser',
			'FormValidation' => '\System\Form\Validation\Validation',
			'Lang'           => ['\System\Lang\Lang', 'instance'],
			'Config'         => ['\System\Config\Config', 'config'],
			'Request'        => ['\System\Request\Request', 'instance'],
			'Database'       => ['\System\Database\Database', 'instance'],
			'Response'       => ['\System\Response\Response', 'instance'],
			'Profiler'       => ['\System\Profiler\Profiler', 'instance'],
			'RequestData'    => ['\System\Request\Data', 'instance'],
			'FormInjector'   => ['\System\Controller\Injector\Form', 'instance'],
			'OrmInjector'    => ['\System\Controller\Injector\Orm', 'instance'],
			'Injector'       => ['\System\Controller\Injector\Injector', 'instance']
		];

		/**
		 * load a system or helper class. This static method use ReflectionClass to instantiate the class with alias $name
		 * @access public
		 * @param $name   string : class alias name
		 * @param $params array : list of parameters
		 * @param $stack  array : execution pile
		 * @throws Exception if the method is unrecognized
		 * @return mixed
		 * @package System\Facade
		 */

		public static function load($name, $params, $stack) {
			if (array_key_exists($name, self::$_alias)) {
				if (!is_array(self::$_alias[$name])) {
					$reflect = new \ReflectionClass(self::$_alias[$name]);
					return $reflect->newInstanceArgs($params);
				}
				else {
					$r = new \ReflectionClass(self::$_alias[$name][0]);
					return $r->getMethod(self::$_alias[$name][1])->invoke(null, $params);
				}
			}
			else {
				$file = '';
				$line = '';

				foreach ($stack as $value) {
					if ($value['function'] == $name) {
						$file = $value['file'];
						$line = $value['line'];
						break;
					}
				}

				throw new Exception('undefined method "' . $name . '" in "' . $file . '" line ' . $line);
			}
		}
	}