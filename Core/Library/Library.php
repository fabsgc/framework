<?php
/*\
 | ------------------------------------------------------
 | @file : Library.php
 | @author : Fabien Beaujean
 | @description : library
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Library;

use Gcs\Framework\Core\Config\Config;
use Gcs\Framework\Core\Exception\MissingLibraryException;
use Gcs\Framework\Core\General\Errors;
use Gcs\Framework\Core\Http\Request\Request;

/**
 * Class Library
 * @package Gcs\Framework\Core\Library
 */
class Library {
    use Errors;

    /**
     * constructor
     * @access public
     * @since 3.0
     * @throws \Gcs\Framework\Core\Exception\MissingLibraryException
     * @package Gcs\Framework\Core\Library
     */

    public function __construct() {
        $config = Config::instance();

        foreach ($config->config['user']['library'] as $key => $value) {
            if ($this->_checkInclude($value)) {
                $file = APP_RESOURCE_LIBRARY_PATH . $value['access'];

                if (file_exists($file)) {
                    require_once($file);
                    $this->addError('The library ' . $file . ' was successfully included', __FILE__, __LINE__, ERROR_INFORMATION, LOG_SYSTEM);
                }
                else {
                    throw new MissingLibraryException('The library ' . $file . ' could not be included');
                }
            }
        }
    }

    /**
     * check if the library can be included
     * @access protected
     * @param $include []
     * @return boolean
     * @since 3.0
     * @package Gcs\Framework\Core\Library
     */

    protected function _checkInclude($include) {
        $request = Request::instance();

        if (isset($include['no'])) {
            if (in_array('.' . $request->src, $include['no']) || in_array('.' . $request->src . '.' . $request->controller, $include['no']) || in_array('.' . $request->src . '.' . $request->controller . '.' . $request->action, $include['no']) || in_array('.' . $request->controller, $include['no']) || in_array('.' . $request->controller . '.' . $request->action, $include['no'])) {
                return false;
            }
            else {
                return true;
            }
        }
        if (isset($include['yes'])) {
            if (in_array('.' . $request->src, $include['yes']) || in_array('.' . $request->src . '.' . $request->controller, $include['yes']) || in_array('.' . $request->src . '.' . $request->controller . '.' . $request->action, $include['yes']) || in_array('.' . $request->controller, $include['yes']) || in_array('.' . $request->controller . '.' . $request->action, $include['yes'])) {
                return true;
            }
            else {
                return false;
            }
        }

        return true;
    }

    /**
     * destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Library
     */

    public function __destruct() {
    }
}