<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Facades.php
	 | @author : Fabien Beaujean
	 | @description : Facades trait
	 | @version : 3.0
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\General;
	
	use Gcs\Framework\Core\Asset\Asset;
	use Gcs\Framework\Core\Cache\Cache;
	use Gcs\Framework\Core\Collection\Collection;
	use Gcs\Framework\Core\Config\Config;
	use Gcs\Framework\Core\Controller\Injector\Form;
	use Gcs\Framework\Core\Controller\Injector\Injector;
	use Gcs\Framework\Core\Controller\Injector\Orm;
	use Gcs\Framework\Core\Cron\Cron;
	use Gcs\Framework\Core\Database\Database;
	use Gcs\Framework\Core\Facade\Facade;
	use Gcs\Framework\Core\Facade\FacadeEntity;
	use Gcs\Framework\Core\Facade\FacadeHelper;
	use Gcs\Framework\Core\Form\Validation\Validation;
	use Gcs\Framework\Core\Lang\Lang;
	use Gcs\Framework\Core\Library\Library;
	use Gcs\Framework\Core\Orm\Entity\Multiple;
	use Gcs\Framework\Core\Profiler\Profiler;
	use Gcs\Framework\Core\Request\Data;
	use Gcs\Framework\Core\Request\Request;
	use Gcs\Framework\Core\Response\Response;
	use Gcs\Framework\Core\Security\Firewall;
	use Gcs\Framework\Core\Security\Spam;
	use Gcs\Framework\Core\Sql\Sql;
	use Gcs\Framework\Core\Template\Template;
	use Gcs\Framework\Core\Template\Parser;
	use Gcs\Framework\Core\Terminal\Terminal;

	/**
	 * Facades trait
	 * @method Sql Sql
	 * @method Spam Spam
	 * @method Lang Lang
	 * @method Cron Cron
	 * @method Cache Cache
	 * @method FacadeHelper Helper
	 * @method FacadeEntity Entity
	 * @method Library Library
	 * @method Database Database
	 * @method Collection Collection
	 * @method Validation FormValidation
	 * @method Injector Injector
	 * @method Form FormInjector
	 * @method Orm OrmInjector
	 * @method Firewall Firewall
	 * @method Template Template
	 * @method Terminal Terminal
	 * @method Asset Asset
	 * @method Validation OrmValidation
	 * @method Multiple EntityMultiple
	 * @method Parser Parser
	 * @method Config Config
	 * @method Request Request
	 * @method Data RequestData
	 * @method Response Response
	 * @method Profiler Profiler
	 * @package Gcs\Framework\Core\General
	 */

	trait Facades {

		/**
		 * when you want to use a core or helper class, you can use the system of facades. It allow you tu instantiate
		 * @access public
		 * @param $name      string : name of alias
		 * @param $arguments array
		 * @return object
		 * @since 3.0
		 * @package Gcs\Framework\Core\General
		 */

		public function __call($name, $arguments = []) {
			$trace = debug_backtrace(0);
			$params = [];

			foreach ($arguments as $value) {
				array_push($params, $value);
			}

			return Facade::load($name, $params, $trace);
		}

		public function getStackTraceFacade($string) {
			return $string;
		}
	}