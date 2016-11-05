<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Config.php
	 | @author : fab@c++
	 | @description : contain data and path used by the application. If the CONFIG_CACHE is, the config is put in cache.
	 | 				It contains data for lang and route files, and paths for css/image/file/js/template
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Config;

	use SimpleXMLElement;
	use System\Annotation\Annotation;
	use System\Cache\Cache;
	use System\Exception\Exception;
	use System\Exception\MissingConfigException;
	use System\General\singleton;

	/**
	 * Class Config
	 * @package System\Config
	 */
	class Config {
		use singleton;

		/**
		 * contain all the config, lang and path data (path, or content file)
		 * @var array
		 */

		public $config = [];

		/**
		 * cache instance
		 * @var \System\Cache\Cache
		 * @access private
		 */

		private $_cache;

		/**
		 * permit to parse easily parents node for route file.
		 * - the name of the attribute
		 * - the separator used if it must concatenate values
		 * - concatenate or no
		 * @var array
		 * @access private
		 */

		private $_routeAttribute = [
			['name' => 'name', 'separator' => '.', 'concatenate' => true],
			['name' => 'url', 'separator' => '', 'concatenate' => true],
			['name' => 'action', 'separator' => '.', 'concatenate' => true],
			['name' => 'vars', 'separator' => ',', 'concatenate' => true],
			['name' => 'cache', 'separator' => '', 'concatenate' => false],
			['name' => 'logged', 'separator' => '', 'concatenate' => false],
			['name' => 'access', 'separator' => '', 'concatenate' => false],
			['name' => 'method', 'separator' => '', 'concatenate' => false]
		];

		/**
		 * permit to parse easily parents node for lang files.
		 * - the name of the attribute
		 * - the separator used if it must concatenate values
		 * - concatenate or no
		 * @var array
		 * @access private
		 */

		private $_langAttribute = [
			['name' => 'name', 'separator' => '.', 'concatenate' => true]
		];

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package System\Config
		 * @param array $data
		 * @throws Exception
		 */

		public function __construct($data = []) {
			self::$_instance = $this;
			$this->config['user'] = $data;

			if (!$this->config['user']['output']['cache']['config']) {
				$this->_init();
			}
			else {
				$this->_cache = new Cache('config');

				if ($this->_cache->isExist()) {
					$this->config = $this->_cache->getCache();
				}
				else {
					$this->_init();
				}
			}
		}

		/**
		 * singleton
		 * @access public
		 * @since 3.0
		 * @package System\Request
		 * @param array $data
		 * @return object|Config
		 */

		public static function instance($data = []) {
			if (is_null(self::$_instance)) {
				new Config($data);
			}

			return self::$_instance;
		}

		/**
		 * provide direct access to the attribute $config
		 * @access public
		 * @since 3.0
		 * @package System\Config\Config
		 */

		public static function config() {
			return Config::instance()->config;
		}

		/**
		 * put config in array
		 * @access protected
		 * @throws Exception
		 * @return void
		 * @since 3.0
		 * @package System\Config
		 */

		protected function _init() {
			/* ############## APP ############## */

			/* ## LANG ## */
			if ($handle = opendir(APP_RESOURCE_LANG_PATH)) {
				while (false !== ($entry = readdir($handle))) {
					if (preg_match('#(' . preg_quote('.xml') . ')$#isU', $entry)) {
						$this->_parseLang(null, str_replace('.xml', '', $entry));
					}
				}

				closedir($handle);
			}

			/* ## TEMPLATE ## */
			$this->config['template']['app'] = APP_RESOURCE_TEMPLATE_PATH;
			/* ## CSS ## */
			$this->config['css']['app'] = WEB_PATH . 'app/' . WEB_CSS_PATH;
			/* ## IMAGE ## */
			$this->config['img']['app'] = WEB_PATH . 'app/' . WEB_IMAGE_PATH;
			/* ## FILE ## */
			$this->config['file']['app'] = WEB_PATH . 'app/' . WEB_FILE_PATH;
			/* ## JS ## */
			$this->config['js']['app'] = WEB_PATH . 'app/' . WEB_JS_PATH;

			/* ############## SRC ############## */

			if ($handleSrc = opendir(SRC_PATH)) {
				while (false !== ($entrySrc = readdir($handleSrc))) {
					if (is_dir(SRC_PATH . $entrySrc) && $entrySrc != '.' && $entrySrc != '..') {
						/* ## ROUTE ## */
						$this->_parseRoute($entrySrc);

						/* ## LANG ## */
						if ($handle = opendir(SRC_PATH . $entrySrc . '/' . SRC_RESOURCE_LANG_PATH)) {
							while (false !== ($entry = readdir($handle))) {
								if (preg_match('#(' . preg_quote('.xml') . ')$#isU', $entry)) {
									$lang = str_replace('.xml', '', $entry);
									$this->_parseLang($entrySrc, $lang);
								}
							}

							closedir($handle);
						}

						/* ## TEMPLATE ## */
						$this->config['template']['' . $entrySrc . ''] = SRC_PATH . $entrySrc . '/' . SRC_RESOURCE_TEMPLATE_PATH;
						/* ## CSS ## */
						$this->config['css']['' . $entrySrc . ''] = WEB_PATH . $entrySrc . '/' . WEB_CSS_PATH;
						/* ## IMAGE ## */
						$this->config['img']['' . $entrySrc . ''] = WEB_PATH . $entrySrc . '/' . WEB_IMAGE_PATH;
						/* ## FILE ## */
						$this->config['file']['' . $entrySrc . ''] = WEB_PATH . $entrySrc . '/' . WEB_FILE_PATH;
						/* ## JS ## */
						$this->config['js']['' . $entrySrc . ''] = WEB_PATH . $entrySrc . '/' . WEB_JS_PATH;
						/* ## FIREWALL ## */
						$this->_parseFirewall($entrySrc);

						//copy app lang in each other module lang
						foreach ($this->config['lang'] as $key => $value) {
							if ($key != 'app') {
								foreach ($value as $key2 => $value2) {
									$this->config['lang']['' . $key . '']['' . $key2 . ''] =
										array_merge(
											$this->config['lang']['app']['' . $key2 . ''],
											$value2
										);
								}
							}
						}

						/* ## ANNOTATIONS ## */
						$controllers = scandir(SRC_PATH . $entrySrc . '/' . SRC_CONTROLLER_PATH);

						foreach ($controllers as $controller){
							if(strlen($controller) > 2){
								$annotation = Annotation::getClass(strtolower($entrySrc . '\\' . basename($controller, '.php')));
								$this->_parseAnnotationRoute($entrySrc, $controller, $annotation);
							}
						}
					}
				}

				closedir($handleSrc);
			}
			else {
				throw new Exception('The directory ' . SRC_PATH . 'doesn\'t exist');
			}

			if ($this->config['user']['output']['cache']['config']) {
				$this->_cache->setContent($this->config);
				$this->_cache->setCache();
			}
		}

		/**
		 * parse route file and put data in an array
		 * @access protected
		 * @param $src string
		 * @param $controller string
		 * @param $annotation array
		 * @since 3.0
		 * @package System\Config
		 */

		protected function _parseAnnotationRoute($src, $controller, $annotation){
			foreach ($annotation['methods'] as $action => $annotationMethods){
				$data = [];

				foreach ($annotationMethods as $annotationMethod){
					if($annotationMethod['annotation'] == 'Routing'){
						/** @var \System\Annotation\Annotations\Router\Routing $annotation */
						$annotation = $annotationMethod['instance'];

						$data['name'] = $annotation->name;
						$data['url'] = $annotation->url;
						$data['vars'] = $annotation->vars;
						$data['method'] = $annotation->method;
						$data['access'] = $annotation->access;
						$data['cache'] = $annotation->cache;
						$data['logged'] = $annotation->logged;
						$data['action'] = lcfirst(basename($controller, '.php')) . '.' . lcfirst(str_replace('action', '', $action));
					}

					$this->config['route'][$src][$data['name']] = $data;
				}
			}
		}

		/**
		 * parse route file and put data in an array
		 * @access protected
		 * @param $src string
		 * @return array
		 * @since 3.0
		 * @throws \System\Exception\MissingConfigException if route config file doesn't exist
		 * @package System\Config
		 */

		protected function _parseRoute($src) {
			$file = SRC_PATH . $src . '/' . SRC_CONFIG_ROUTE;

			if ($xml = simplexml_load_file($file)) {
				$values = $xml->xpath('//route');

				/** @var SimpleXMLElement[] $value */
				foreach ($values as $value) {
					foreach ($this->_routeAttribute as $attribute) {
						$attributeType = $attribute['name'];

						if (is_object($value[$attributeType])) {
							$data[$attributeType] = $value[$attributeType]->__toString();
						}
						else {
							$data[$attributeType] = '';
						}
					}

					/** @var array $data */
					/** @var SimpleXMLElement $value */
					$data = $this->_parseParent($value, $data, $this->_routeAttribute);

					if (empty($data['logged']) || $data['logged'] == '') {
						$data['logged'] = '*';
					}

					if (empty($data['access']) || $data['access'] == '') {
						$data['access'] = '*';
					}

					if (empty($data['method']) || $data['method'] == '') {
						$data['method'] = '*';
					}

					$this->config['route']['' . $src . '']['' . $data['name'] . ''] = $data;
				}
			}
			else {
				throw new MissingConfigException('can\'t open file "' . $file . '"');
			}
		}

		/**
		 * parse lang files and put data in an array
		 * @access protected
		 * @param $src  string
		 * @param $lang string
		 * @return array
		 * @since 3.0
		 * @throws \System\Exception\MissingConfigException if lang config file doesn't exist
		 * @package System\Config
		 */

		protected function _parseLang($src = null, $lang) {
			if ($src == null) {
				$file = APP_RESOURCE_LANG_PATH . $lang . '.xml';
				$src = 'app';
			}
			else {
				$file = SRC_PATH . $src . '/' . SRC_RESOURCE_LANG_PATH . $lang . '.xml';
			}

			$this->config['lang']['' . $src . '']['' . $lang . ''] = [];

			if (file_exists($file)) {
				if ($xml = simplexml_load_file($file)) {
					$values = $xml->xpath('//lang');

					/** @var SimpleXMLElement[] $value */
					foreach ($values as $value) {
						$data = null;

						foreach ($this->_langAttribute as $attribute) {
							$attributeType = $attribute['name'];

							if (is_object($value[$attributeType])) {
								$data[$attributeType] = $value[$attributeType]->__toString();
							}
							else {
								$data[$attributeType] = '';
							}

							/** @var SimpleXMLElement $value */
							$data['content'] = $value->__toString();
						}

						$data = $this->_parseParent($value, $data, $this->_langAttribute);

						$this->config['lang']['' . $src . '']['' . $lang . '']['' . $data['name'] . ''] = $data;
						$this->config['lang']['' . $src . '']['' . $lang . '']['' . $data['name'] . ''] = $this->config['lang']['' . $src . '']['' . $lang . '']['' . $data['name'] . '']['content'];
					}
				}
				else {
					throw new MissingConfigException('can\'t read file "' . $file . '"');
				}
			}
			else {
				throw new MissingConfigException('can\'t open file "' . $file . '"');
			}
		}

		/**
		 * parse firewall file
		 * @access protected
		 * @param $src string
		 * @return array
		 * @since 3.0
		 * @throws \System\Exception\MissingConfigException if firewall config file doesn't exist
		 * @package System\Config
		 */

		protected function _parseFirewall($src = null) {
			$file = SRC_PATH . $src . '/' . SRC_CONFIG_FIREWALL;

			if (file_exists($file)) {
				if ($xml = simplexml_load_file($file)) {
					$roles = $xml->xpath('//roles');
					$role = $xml->xpath('//role');
					$login = $xml->xpath('//login/source');
					$default = $xml->xpath('//default/source');
					$forbidden = $xml->xpath('//forbidden');
					$csrf = $xml->xpath('//csrf');
					$forbiddenVariable = $xml->xpath('//forbidden/variable');
					$csrfVariable = $xml->xpath('//csrf/variable');
					$logged = $xml->xpath('//logged');

					$this->config['firewall']['' . $src . '']['roles'] = [];
					$this->config['firewall']['' . $src . '']['forbidden']['variable'] = [];
					$this->config['firewall']['' . $src . '']['csrf']['variable'] = [];

					/** @var SimpleXMLElement[] $value */

					foreach ($roles as $value) {
						$this->config['firewall']['' . $src . '']['roles']['name'] = $value['name']->__toString();
					}

					foreach ($role as $value) {
						$this->config['firewall']['' . $src . '']['roles']['role']['' . $value['name']->__toString() . ''] = $value['name']->__toString();
					}

					foreach ($login as $value) {
						$this->config['firewall']['' . $src . '']['login']['name'] = $value['name']->__toString();
						$this->config['firewall']['' . $src . '']['login']['vars'] = explode(',', $value['vars']->__toString());
					}

					foreach ($default as $value) {
						$this->config['firewall']['' . $src . '']['default']['name'] = $value['name']->__toString();
						$this->config['firewall']['' . $src . '']['default']['vars'] = explode(',', $value['vars']->__toString());
					}

					foreach ($forbidden as $value) {
						$this->config['firewall']['' . $src . '']['forbidden']['template'] = $value['template']->__toString();
					}

					foreach ($csrf as $value) {
						$this->config['firewall']['' . $src . '']['csrf']['name'] = $value['name']->__toString();
						$this->config['firewall']['' . $src . '']['csrf']['template'] = $value['template']->__toString();
						$this->config['firewall']['' . $src . '']['csrf']['enabled'] = $value['enabled']->__toString();
					}

					foreach ($forbiddenVariable as $value) {
						$data = [];

						$data['type'] = $value['type']->__toString();
						$data['name'] = $value['name']->__toString();
						$data['value'] = $value['value']->__toString();

						array_push($this->config['firewall']['' . $src . '']['forbidden']['variable'], $data);
					}

					foreach ($csrfVariable as $value) {
						$data = [];

						$data['type'] = $value['type']->__toString();
						$data['name'] = $value['name']->__toString();
						$data['value'] = $value['value']->__toString();

						array_push($this->config['firewall']['' . $src . '']['csrf']['variable'], $data);
					}

					foreach ($logged as $value) {
						$this->config['firewall']['' . $src . '']['logged']['name'] = $value['name']->__toString();
					}
				}
				else {
					throw new MissingConfigException('can\'t open file "' . $file . '"');
				}
			}
			else {
				throw new MissingConfigException('can\'t open file "' . $file . '"');
			}
		}

		/**
		 * parse parent node
		 * @access protected
		 * @param $child      \SimpleXMLElement
		 * @param $data       string
		 * @param $attributes array
		 * @return array
		 * @since 3.0
		 * @package System\Config
		 */

		protected function _parseParent($child, $data, $attributes) {
			$parent = $child->xpath("parent::*");

			if (is_object($parent[0]['name'])) {
				foreach ($attributes as $attribute) {
					$name = $attribute['name'];

					if (is_object($parent[0][$name])) {
						/** @var SimpleXMLElement $element */
						$element = $parent[0][$name];

						if ($attribute['concatenate'] == true) {
							if ($data[$name] != '') {
								$data[$name] = $element->__toString() . $attribute['separator'] . $data[$name];
							}
							else {
								$data[$name] = $element->__toString();
							}
						}
						else {
							if ($data[$name] == '') {
								$data[$name] = $element->__toString();
							}
						}
					}
				}

				$data = $this->_parseParent($parent[0], $data, $attributes);
			}

			return $data;
		}

		/**
		 * destructor
		 * @access protected
		 * @return string
		 * @since 3.0
		 * @package System\Config
		 */

		public function __destruct() {
		}
	}