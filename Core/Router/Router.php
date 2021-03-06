<?php
/*\
 | ------------------------------------------------------
 | @file : Router.php
 | @author : Fabien Beaujean
 | @description : url rewriting
 | @version : 3.0 Bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Router;

use Gcs\Framework\Core\Config\Config;
use Gcs\Framework\Core\Facade\Facades;
use Gcs\Framework\Core\Http\Request\Request;

/**
 * Class Router
 * @package Gcs\Framework\Core\Router
 */
class Router {
    use Facades;

    /**
     * contain all the routes
     * @var \Gcs\Framework\Core\Router\Route[]
     */

    protected $routes = [];

    /**
     * add route to the instance
     * @access public
     * @param $route route : route instance
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Router
     */

    public function addRoute(route $route) {
        if (!in_array($route, $this->routes)) {
            $this->routes[] = $route;
        }
    }

    /**
     * after url rewriting, return the right route
     * @access public
     * @param $url string
     * @param $config
     * @return \Gcs\Framework\Core\Router\Route
     * @since 3.0
     * @package Gcs\Framework\Core\Router
     */

    public function getRoute($url, $config) {
        $url2 = substr($url, strlen(Config::config()['user']['framework']['folder']), strlen($url));
        $routeRight = null;

        foreach ($this->routes as $route) {
            if (($varsValues = $route->match($url2)) != false && ($route->method() == '*' || in_array(Request::instance()->data->method, explode(',', $route->method())) || $route->method() == Request::instance()->data->method)) {
                $routeRight = $route;
                // if she has vars
                if ($route->hasVars()) {
                    $varsNames = $route->varsNames();
                    $listVars = [];

                    //key : name of the var, value = value
                    foreach ($varsValues as $key => $match) {
                        // the first key contains all the captured string (preg_match)
                        if ($key > 0) {
                            if (array_key_exists($key - 1, $varsNames)) {
                                $listVars[$varsNames[$key - 1]] = $match;
                            }
                        }
                    }

                    $route->setVars($listVars);
                }

                /**
                 * sometimes, it's possible to have several times the same URL, for example, one when we are logged and one when we are not logged.
                 * each url has a different ID, so, when we have an url which her "logged" attribute is not correct,
                 * we have to check if there is an other url whith the right "logged" attribute
                 */
                $logged = explode('.', $config->config['firewall']['' . $route->src() . '']['logged']['name']);
                $role = explode('.', $config->config['firewall']['' . $route->src() . '']['roles']['name']);
                $logged = $this->_setFirewallConfigArray($_SESSION, $logged);
                $role = $this->_setFirewallConfigArray($_SESSION, $role);

                switch ($route->logged()) {
                    case 'true' :
                        if ($logged == true && (in_array($role, array_map('trim', explode(',', $route->access()))) || $route->access() == '*')) {
                            return $route;
                        }
                        break;

                    case 'false' :
                        if ($logged == false) {
                            return $route;
                        }
                        break;

                    case '*' :
                        return $route;
                        break;
                }
            }
        }

        if ($routeRight != null && $routeRight->match($url2) != false) {
            return $routeRight;
        }

        return null;
    }

    /**
     * get token, logged and role value from environment
     * @access public
     * @param $in    array : array which contain the value
     * @param $array array : "path" to the value in $in
     * @return mixed
     * @since 3.0
     * @package Gcs\Framework\Core\Router
     */

    protected function _setFirewallConfigArray($in, $array) {
        if (isset($in['' . $array[0] . ''])) {
            $to = $in['' . $array[0] . ''];
            array_splice($array, 0, 1);

            foreach ($array as $value) {
                if (isset($to['' . $value . ''])) {
                    $to = $to['' . $value . ''];
                }
                else {
                    return false;
                }
            }
        }
        else {
            return false;
        }

        return $to;
    }
}