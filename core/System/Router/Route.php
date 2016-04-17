<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Route.php
	 | @author : fab@c++
	 | @description : route configuration
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Router;

	class Route{

		/**
		 * @var string
		*/

		protected $action;

		/**
		 * @var string
		*/

		protected $controller;

		/**
		 * @var string
		*/

		protected $name;

		/**
		 * @var \System\Cache\Cache
		*/

		protected $cache;

		/**
		 * @var string
		*/

		protected $url;

		/**
		 * @var string
		*/

		protected $varsNames;

		/**
		 * @var array
		*/

		protected $vars = [];

		/**
		 * @var string
		*/

		protected $src;

		/**
		 * @var boolean
		*/

		protected $logged;

		/**
		 * @var string
		*/

		protected $access;

		/**
		 * @var string
		*/

		protected $method;

		/**
		 * each route from route.xml become an instance of this class
		 * @access public
		 * @param $url string
		 * @param $controller string
		 * @param $action string
		 * @param $name string
		 * @param $cache int
		 * @param $varsNames array : list of variable from vars=""
		 * @param $src string : location of the file
		 * @param $logged
		 * @param $access
		 * @param $method
		 * @since 3.0
		 * @package System\Router
		*/

		public function __construct($url, $controller, $action, $name, $cache, $varsNames = [], $src, $logged, $access, $method){
			$this->setUrl($url);
			$this->setController($controller);
			$this->setAction($action);
			$this->setName($name);
			$this->setCache($cache);
			$this->setVarsNames($varsNames);
			$this->setSrc($src);
			$this->setLogged($logged);
			$this->setAccess($access);
			$this->setMethod($method);
		}

		/**
		 * @return bool
		 * @since 3.0
		 * @package System\Router
		*/

		public function hasVars(){
			return !empty($this->varsNames);
		}

		/**
		 * @param $url
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		*/

		public function match($url){
			if (preg_match('`^'.$this->url.'$`', $url, $matches)){
				return $matches;
			}
			else{
				return false;
			}
		}

		/**
		 * @param $action
		 * @since 3.0
		 * @package System\Router
		*/

		public function setAction($action){
			if (is_string($action)){
				$this->action = $action;
			}
		}

		/**
		 * @param $name
		 * @since 3.0
		 * @package System\Router
		 */

		public function setName($name){
			if (is_string($name)){
				$this->name = $name;
			}
		}

		/**
		 * @param $cache
		 * @since 3.0
		 * @package System\Router
		*/

		public function setCache($cache){
			$this->cache = $cache;
		}

		/**
		 * @param $controller
		 * @since 3.0
		 * @package System\Router
		*/

		public function setController($controller){
			if (is_string($controller)){
				$this->controller = $controller;
			}
		}

		/**
		 * @param $url
		 * @since 3.0
		 * @package System\Router
		*/

		public function setUrl($url){
			if (is_string($url)){
				$this->url = $url;
			}
		}

		/**
		 * @param array $varsNames
		 * @since 3.0
		 * @package System\Router
		*/

		public function setVarsNames(array $varsNames){
			$this->varsNames = $varsNames;
		}

		/**
		 * @param array $vars
		 * @since 3.0
		 * @package System\Router
		*/

		public function setVars(array $vars){
			$this->vars = $vars;
		}

		/**
		 * @param $src
		 * @since 3.0
		 * @package System\Router
		*/

		public function setSrc($src){
			$this->src = $src;
		}

		/**
		 * @param $logged
		 * @since 3.0
		 * @package System\Router
		*/

		public function setLogged($logged){
			$this->logged = $logged;
		}

		/**
		 * @param $access
		 * @since 3.0
		 * @package System\Router
		*/

		public function setAccess($access){
			$this->access = $access;
		}

		/**
		 * @param $method
		 * @since 3.0
		 * @package System\Router
		*/

		public function setMethod($method){
			$this->method = $method;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		*/

		public function action(){
			return $this->action;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		*/

		public function name(){
			return $this->name;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		*/

		public function cache(){
			return $this->cache;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		*/

		public function url(){
			return $this->url;
		}

		/**
		 * @return string
		 * @since 3.0
		 * @package System\Router
		*/

		public function controller(){
			return $this->controller;
		}

		/**
		 * @return array
		 * @since 3.0
		 * @package System\Router
		*/

		public function vars(){
			return $this->vars;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		*/

		public function varsNames(){
			return $this->varsNames;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		*/

		public function src(){
			return $this->src;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		 */

		public function logged(){
			return $this->logged;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		 */

		public function access(){
			return $this->access;
		}

		/**
		 * @return mixed
		 * @since 3.0
		 * @package System\Router
		 */

		public function method(){
			return $this->method;
		}

		/**
		 * @since 3.0
		 * @package System\Router
		 */

		public function __destruct(){
		}
	}