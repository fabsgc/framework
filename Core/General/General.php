<?php
	/*\
	 | ------------------------------------------------------
	 | @file : General.php
	 | @author : Fabien Beaujean
	 | @description : functions used everywhere
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
	use Gcs\Framework\Core\Exception\MissingConfigException;
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