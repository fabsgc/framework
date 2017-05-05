<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Facades.php
	 | @author : Fabien Beaujean
	 | @description : loader system to load core and helper classes
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Facade;

	use Gcs\Framework\Core\Exception\Exception;
    use Gcs\Framework\Core\Exception\MissingMethodException;

    /**
	 * Class Facade
	 * @package Gcs\Framework\Core\Facade
	 */

	class Facade {
		/**
		 * list of the class alias and the real class behind
		 * @var array
		 */

		private static $_alias = [
			'Sql'            => '\Gcs\Framework\Core\Sql\Sql',
			'Spam'           => '\Gcs\Framework\Core\Security\Spam',
			'Cron'           => '\Gcs\Framework\Core\Cron\Cron',
            'Asset'          => '\Gcs\Framework\Core\Asset\Asset',
			'Cache'          => '\Gcs\Framework\Core\Cache\Cache',
			'Helper'         => '\Gcs\Framework\Core\Facade\FacadeHelper',
			'Entity'         => '\Gcs\Framework\Core\Facade\FacadeEntity',
			'Library'        => '\Gcs\Framework\Core\Library\Library',
            'Session'        => '\Gcs\Framework\Core\Session\Session',
			'Firewall'       => '\Gcs\Framework\Core\Security\Firewall',
			'Template'       => '\Gcs\Framework\Core\Template\Template',
			'Terminal'       => '\Gcs\Framework\Core\Terminal\Terminal',
			'OrmValidation'  => '\Gcs\Framework\Core\Orm\Validation\Validation',
			'EntityMultiple' => '\Gcs\Framework\Core\Orm\Entity\Multiple',
			'TemplateParser' => '\Gcs\Framework\Core\Template\Parser',
			'FormValidation' => '\Gcs\Framework\Core\Form\Validation\Validation',
			'Lang'           => ['\Gcs\Framework\Core\Lang\Lang', 'instance'],
			'Config'         => ['\Gcs\Framework\Core\Config\Config', 'config'],
			'Request'        => ['\Gcs\Framework\Core\Request\Request', 'instance'],
			'Database'       => ['\Gcs\Framework\Core\Database\Database', 'instance'],
			'Response'       => ['\Gcs\Framework\Core\Response\Response', 'instance'],
			'Profiler'       => ['\Gcs\Framework\Core\Profiler\Profiler', 'instance'],
			'RequestData'    => ['\Gcs\Framework\Core\Request\Data', 'instance'],
			'FormInjector'   => ['\Gcs\Framework\Core\Controller\Injector\Form', 'instance'],
			'OrmInjector'    => ['\Gcs\Framework\Core\Controller\Injector\Orm', 'instance'],
			'Injector'       => ['\Gcs\Framework\Core\Controller\Injector\Injector', 'instance']
		];

		/**
		 * load a system or helper class. This static method use ReflectionClass to instantiate the class with alias $name
		 * @access public
		 * @param $name   string : class alias name
		 * @param $params array : list of parameters
		 * @param $stack  array : execution pile
		 * @throws Exception if the method is unrecognized
		 * @return mixed
		 * @package Gcs\Framework\Core\Facade
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

				throw new MissingMethodException('Undefined method "' . $name . '" in "' . $file . '" line ' . $line);
			}
		}
	}