<?php
/*\
 | ------------------------------------------------------
 | @file : Profiler.php
 | @author : Fabien Beaujean
 | @description : profiler
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Profiler;

use Gcs\Framework\Core\Cache\Cache;
use Gcs\Framework\Core\Config\Config;
use Gcs\Framework\Core\General\Errors;
use Gcs\Framework\Core\General\Singleton;
use Gcs\Framework\Core\Http\Request\Data;

/**
 * Class Profiler
 * @package Gcs\Framework\Core\Profiler
 */
class Profiler {
    use Errors, Singleton;

    const SQL_START      = 0;
    const SQL_END        = 1;
    const SQL_ROWS       = 2;
    const TEMPLATE_START = 0;
    const TEMPLATE_END   = 1;
    const USER_START     = 0;
    const USER_END       = 1;
    /**
     * sql queries
     * @var string[]
     */

    protected $_sql = [];
    /**
     * templates
     * @var string[]
     */

    protected $_template = [];
    /**
     * errors
     * @var string[]
     */

    protected $_error = [];
    /**
     * //profiler activated ?
     * @var boolean
     */

    protected $_enabled = true;
    /**
     * time
     * @var integer
     */

    protected $_time;
    /**
     * sometimes, 2 sql queries have the same name
     * @var integer
     */

    protected $_lastSql;
    /**
     * times list
     * @var boolean
     */

    protected $_timeUser = [];

    /**
     * constructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function __construct() {
        $this->_time = microtime(true);
        $this->_enabled = Config::config()['user']['debug']['profiler'];
    }

    /**
     * singleton
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Request
     */

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Profiler();
        }

        return self::$_instance;
    }

    /**
     * at the end, put data in cache
     * @access public
     * @param $request
     * @param $response
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function profiler($request, $response) {
        $this->_stopTime();

        if ($this->_enabled == true) {
            $dataProfiler = [];

            $dataProfiler['time'] = round($this->_time, 2);
            $dataProfiler['timeUser'] = $this->_timeUser;
            $dataProfiler['controller'] = get_included_files();
            $dataProfiler['template'] = $this->_template;
            $dataProfiler['request'] = serialize($request);
            $dataProfiler['response'] = serialize($response);
            $dataProfiler['sql'] = $this->_sql;
            $dataProfiler['get'] = $_GET;
            $dataProfiler['post'] = $_POST;
            $dataProfiler['put'] = Data::instance()->put;
            $dataProfiler['patch'] = Data::instance()->patch;
            $dataProfiler['delete'] = Data::instance()->delete;
            $dataProfiler['session'] = $_SESSION;
            $dataProfiler['cookie'] = $_COOKIE;
            $dataProfiler['files'] = $_FILES;
            $dataProfiler['server'] = $_SERVER;
            $dataProfiler['url'] = $_SERVER['REQUEST_URI'];

            if ($request->controller != 'asset' && $request->controller != 'profiler') {
                $cache = new Cache('core-profiler', 0);
                $cache->setContent($dataProfiler);
                $cache->setCache();
            }

            $cacheId = new Cache('core-profiler-' . $request->src . '.' . $request->controller . '.' . $request->action, 0);
            $cacheId->setContent($dataProfiler);
            $cacheId->setCache();
        }
    }

    /**
     * stop the timer
     * @access protected
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    protected function _stopTime() {
        $this->_time = (microtime(true) - $this->_time) * 1000;
    }

    /**
     * add an error
     * @access public
     * @param $error string
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function addError($error) {
        array_push($this->_error, $error);
    }

    /**
     * add a template
     * @access public
     * @param $name string
     * @param $type
     * @param $file
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function addTemplate($name, $type = self::TEMPLATE_START, $file) {
        if ($this->_enabled == true) {
            switch ($type) {
                case self::TEMPLATE_START:
                    $this->_template[$file]['name'] = $name;
                    $this->_template[$file]['time'] = microtime(true);
                    break;

                case self::TEMPLATE_END:
                    $this->_template[$file]['time'] = round((microtime(true) - $this->_template[$file]['time']) * 1000, 4);
                    break;
            }
        }
    }

    /**
     * add a sql query
     * @access public
     * @param $name
     * @param $type
     * @param $value
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function addSql($name, $type = self::SQL_START, $value = '') {
        if ($this->_enabled == true) {
            switch ($type) {
                case self::SQL_START:
                    if (isset($this->_sql[$name])) {
                        $this->_lastSql = $name;
                    }
                    else {
                        $this->_lastSql = $name . rand(0, 10);
                    }

                    $this->_sql[$this->_lastSql] = [];
                    $this->_sql[$this->_lastSql]['time'] = microtime(true);
                    break;

                case self::SQL_END:
                    $this->_sql[$this->_lastSql]['time'] = round((microtime(true) - $this->_sql[$this->_lastSql]['time']) * 1000, 4);
                    $this->_sql[$this->_lastSql]['query'] = $value;
                    break;
            }
        }
    }

    /**
     * add time to timer
     * @access public
     * @param $name
     * @param $type
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function addTime($name, $type = self::USER_START) {
        if ($this->_enabled == true) {
            switch ($type) {
                case self::USER_START:
                    $this->_timeUser[$name] = 0;
                    $this->_timeUser[$name] = microtime(true);
                    break;

                case self::USER_END:
                    $this->_timeUser[$name] = round((microtime(true) - $this->_timeUser[$name]) * 1000, 4);
                    break;
            }
        }
    }

    /**
     * enable or disable the profiler
     * @access public
     * @param $enabled boolean
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function enable($enabled = true) {
        $this->_enabled = $enabled;
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Profiler
     */

    public function __destruct() {
    }
}