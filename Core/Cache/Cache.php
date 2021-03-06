<?php
/*\
 | ------------------------------------------------------
 | @file : Cache.php
 | @author : Fabien Beaujean
 | @description : cache system
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Cache;

use Gcs\Framework\Core\Config\Config;

/**
 * Class Cache
 * @package Gcs\Framework\Core\Cache
 */
class Cache {

    /**
     * cache name
     * @var string
     * @access private
     */

    private $_name;

    /**
     * cache file name
     * @var string
     * @access private
     */

    private $_fileName;

    /**
     * cache time
     * @var string
     * @access private
     */

    private $_time = 0;

    /**
     * cache content
     * @var string
     * @access private
     */

    private $_content;

    /**
     * Constructor
     * @access public
     * @param $name string : name of the cache file
     * @param $time int : cache time, default value is 0
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function __construct($name, $time = 0) {
        $this->_name = $name;

        if (!file_exists(APP_CACHE_PATH_DEFAULT)) {
            mkdir(APP_CACHE_PATH_DEFAULT, 0777, true);
        }

        if (Config::config()['user']['output']['cache']['sha1']) {
            $this->_fileName = APP_CACHE_PATH_DEFAULT . sha1($this->_name . '.cache');
        }
        else {
            $this->_fileName = APP_CACHE_PATH_DEFAULT . $this->_name . '.cache';
        }

        if (Config::config()['user']['output']['cache']['enabled']) {
            $this->_time = $time;
        }
        else {
            $this->_time = 0;
        }
    }

    /**
     * set content of the cache
     * @access public
     * @param mixed $content
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function setContent($content) {
        $this->_content = $content;
    }

    /**
     * destroy a cache file
     * @access public
     * @return boolean
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function destroy() {
        if (file_exists($this->_fileName)) {
            if (unlink($this->_fileName)) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return true;
        }
    }

    /**
     * get cache content
     * @access public
     * @return mixed
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function getCache() {
        if (!file_exists($this->_fileName)) {
            $this->setCache();
        }

        return unserialize(($this->_uncompress(file_get_contents($this->_fileName))));
    }

    /**
     * uncompress the content
     * @access public
     * @param string $val
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    private function _uncompress($val) {
        return gzuncompress($val);
    }

    /**
     * create the file cache
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function setCache() {
        if (!file_exists($this->_fileName)) {
            file_put_contents($this->_fileName, $this->_compress(serialize($this->_content)));
        }

        $timeAgo = time() - filemtime($this->_fileName);

        if ($timeAgo > $this->_time) {
            file_put_contents($this->_fileName, $this->_compress(serialize($this->_content)));
        }
    }

    /**
     * compress the content
     * @access public
     * @param string $content
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    private function _compress($content) {
        return gzcompress($content, 9);
    }

    /**
     * return true if the cache is too old
     * @access public
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function isDie() {
        if ($this->_time > 0) {
            $die = false;

            if (!file_exists($this->_fileName)) {
                $die = true;
            }
            else {
                $timeAgo = time() - filemtime($this->_fileName);
                if ($timeAgo > $this->_time) {
                    $die = true;
                }
            }

            return $die;
        }
        else {
            return true;
        }
    }

    /**
     * return true if the file exists
     * @access public
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function isExist() {
        if (file_exists($this->_fileName)) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Cache
     */

    public function __destruct() {
    }
}