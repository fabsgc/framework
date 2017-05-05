<?php
	namespace Gcs\Framework\Core\Url;

	use Gcs\Framework\Core\Config\Config;
	use Gcs\Framework\Core\General\resolve;

	/**
	 * Class Url
	 * @package System\Url
	 */

	class Url {
		use resolve;

		/**
		 * get an url
		 * @access public
		 * @param string $name : name of the url. With .app. before, it use the default route file. Width .x., it use the module x
		 * @param array $var
		 * @param boolean $absolute : add absolute link
		 * @return string
		 * @since 3.0
		 */

		public static function get($name, $var = [], $absolute = false) {
			$routes = self::resolveStatic(RESOLVE_ROUTE, $name);

			if (isset($routes[0]['' . $routes[1] . ''])) {
				$route = $routes[0]['' . $routes[1] . ''];

				$url = preg_replace('#\((.*)\)#isU', '<($1)>', $route['url']);
				$urls = explode('<', $url);
				$result = '';
				$i = 0;

				foreach ($urls as $url) {
					if (preg_match('#\)>#', $url)) {
						if (count($var) > 0) {
							if (isset($var[$i])) {
								$result .= preg_replace('#\((.*)\)>#U', $var[$i], $url);
							}
							else {
								$result .= preg_replace('#\((.*)\)>#U', '', $url);
							}

							$i++;
						}
					}
					else {
						$result .= $url;
					}
				}

				$result = preg_replace('#\\\.#U', '.', $result);

				if (Config::config()['user']['framework']['folder'] != '') {
					$folder = Config::config()['user']['framework']['folder'];
					
					if ($absolute == false) {
						return '/' . substr($folder, 0, strlen($folder) - 1) . $result;
					}
					else {
						if(Config::config()['user']['output']['https'])
							return 'https://' . $_SERVER['HTTP_HOST'] . $folder . $result;
						else
							return 'http://' . $_SERVER['HTTP_HOST'] . $folder . $result;
					}
				}
				else {
					if ($absolute == false) {
						if ($result == '') {
							return '/';
						}
						else {
							return $result;
						}
					}
					else {
						if(Config::config()['user']['output']['https'])
							return 'https://' . $_SERVER['HTTP_HOST'] . $result;
						else
							return 'http://' . $_SERVER['HTTP_HOST'] . $result;
					}
				}
			}

			return null;
		}
	}