<?php
/*\
 | ------------------------------------------------------
 | @file : Resolver.php
 | @author : Fabien Beaujean
 | @description : Trait path resolver
 | @version : 3.0
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\General;

use Gcs\Framework\Core\Config\Config;
use Gcs\Framework\Core\Exception\MissingConfigException;
use Gcs\Framework\Core\Request\Request;

trait Resolver {

    /**
     * when you want to use an image, file only, this method is used to resolve the right path
     * the method override resolve()
     * @access public
     * @param $type string : type of the config
     * @param $data string : ".gcs/template/" "template"
     * @param $php  boolean : because method return path, the framework wants to know if you want the html path or the php path
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\General
     */

    protected function path($type, $data = '', $php = false) {
        if ($php == true) {
            return $this->resolve($type, $data);
        }
        else {
            return Config::config()['user']['framework']['folder'] . $this->resolve($type, $data);
        }
    }

    /**
     * when you want to use a lang, route, image, template, this method is used to resolve the right path
     * the method use the instance of \Gcs\Framework\Core\Config\Config
     * @access public
     * @param $type string : type of the config
     * @param $data string : ".Gcs.lang" ".Gcs/template/" "template"
     * @throws MissingConfigException
     * @return mixed
     * @since 3.0
     * @package Gcs\Framework\Core\General
     */

    protected function resolve($type, $data) {
        return self::resolveStatic($type, $data);
    }

    /**
     * when you want to use a lang, route, image, template, this method is used to resolve the right path
     * the method use the instance of \Gcs\Framework\Core\Config\Config
     * @access public
     * @param $type string : type of the config
     * @param $data string : ".gcs.lang" ".gcs/template/" "template"
     * @throws MissingConfigException
     * @return mixed
     * @since 3.0
     * @package Gcs\Framework\Core\General
     */

    protected static function resolveStatic($type, $data) {
        $request = Request::instance();
        $config = Config::instance();

        if ($type == RESOLVE_ROUTE || $type == RESOLVE_LANG) {
            if (preg_match('#^((\.)([a-zA-Z0-9_-]+)(\.)(.+))#', $data, $matches)) {
                $src = $matches[3];
                $data = preg_replace('#^(\.)(' . preg_quote($src) . ')(\.)#isU', '', $data);
            }
            else {
                $src = $request->src;
            }

            return [$config->config[$type][$src], $data];
        }
        else {
            if (preg_match('#^((\.)([^(\/)]+)([(\/)]*)(.*))#', $data, $matches)) {
                $src = $matches[3];
                $data = $matches[5];
            }
            else {
                if ($request->src != '') {
                    $src = $request->src;
                }
                else {
                    $src = 'app';
                }
            }

            if ($src == 'vendor') {
                return VENDOR_PATH . $data;
            }
            else {
                if (!isset($config->config[$type][$src])) {
                    throw new MissingConfigException('The section "' . $type . '/' . $src . '" does not exist in configuration');
                }
            }

            return $config->config[$type][$src] . $data;
        }
    }
}