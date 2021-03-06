<?php
/*\
 | ------------------------------------------------------
 | @file : Cron.php
 | @author : Fabien Beaujean
 | @description : cron
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Cron;

use Gcs\Framework\Core\Cache\Cache;
use Gcs\Framework\Core\Config\Config;
use Gcs\Framework\Core\Engine\Engine;
use Gcs\Framework\Core\Exception\MissingConfigException;
use Gcs\Framework\Core\Facade\Facades;
use Gcs\Framework\Core\General\Di;
use Gcs\Framework\Core\General\Errors;
use Gcs\Framework\Core\Profiler\Profiler;
use Gcs\Framework\Core\Http\Request\Request;
use Gcs\Framework\Core\Http\Response\Response;

/**
 * Class Cron
 * @package Gcs\Framework\Core\Cron
 */
class Cron {
    use Errors, Facades, Di;

    /**
     * @var boolean
     * @access private
     */

    private $_exception = false;

    /**
     * list of execution time
     * @var array
     * @access private
     */

    private $_crons = [];

    /**
     * constructor
     * @access public
     * @throws \Gcs\Framework\Core\Exception\MissingConfigException
     * @since 3.0
     * @package Gcs\Framework\Core\Cron
     */

    public function __construct() {
        $this->config = Config::instance();
        $this->request = Request::instance();
        $this->response = Response::instance();
        $this->profiler = Profiler::instance();

        if (isset($this->config->config['user']['cron'])) {
            Request::$_instance = null;
            Response::$_instance = null;
            Profiler::$_instance = null;

            if (!$this->_exception()) {
                $cache = new Cache('core-cron-crons');

                if ($cache->isExist()) {
                    $this->_crons = $cache->getCache();
                }

                foreach ($this->config->config['user']['cron']['task'] as $key => $time) {
                    if (empty($this->_crons[$key]) || $this->_crons[$key] + $time < time() || $time == 0) {
                        $this->_crons[$key] = time();

                        $action = explode('.', $key);
                        $controller = new Engine();
                        $controller->initCron($action[1], $action[2], $action[3]);
                        $this->profiler->addTime('route cron : ' . $key);

                        ob_start();
                        $controller->runCron();
                        $output = ob_get_contents();
                        ob_get_clean();

                        $this->profiler->addTime('route cron : ' . $key, Profiler::USER_END);
                        $this->addError('[' . $key . "]\n[" . $output . "]", 0, 0, 0, LOG_CRONS);
                        $this->addError('CRON ' . $key . ' called successfully ', __FILE__, __LINE__, ERROR_INFORMATION);
                    }
                }

                $cache->setContent($this->_crons);
                $cache->setCache();
            }
            else {
                $this->addError('CRON : the page is an exception ', __FILE__, __LINE__, ERROR_INFORMATION);
            }
        }
        else {
            throw new MissingConfigException('Can\'t read cron configuration');
        }

        Request::$_instance = $this->request;
        Response::$_instance = $this->response;
        Profiler::$_instance = $this->profiler;
    }

    /**
     * return if the current page which calls crons is an exception
     * @access protected
     * @return  boolean
     * @since 3.0
     * @package Gcs\Framework\Core\Cron
     */

    protected function _exception() {
        $url = '.' . $this->request->src . '.' . $this->request->controller . '.' . $this->request->action;

        if (in_array($url, $this->config->config['user']['cron']['config']['exception'])) {
            $this->_exception = true;
        }
        else {
            $this->_exception = false;
        }

        return $this->_exception;
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Cron
     */

    public function __destruct() {
    }
}