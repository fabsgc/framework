<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Response.php
	 | @author : fab@c++
	 | @description : Controllers will use this class to render their response.
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Response;

	use System\Config\Config;
	use System\General\singleton;
	use System\Request\Request;
	use System\Template\Template;

	/**
	 * Class Response
	 * @package System\Response
	 */

	class Response {
		use singleton;

		/**
		 * Array of http errors
		 * @var array
		 */

		protected $_statusCode = [
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			416 => 'Requested range not satisfiable',
			417 => 'Expectation Failed',
			418 => 'You\'re speaking with a teapot',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out',
			505 => 'Unsupported Version'
		];

		/**
		 * status code which display an error page
		 * @var array
		 */

		protected $_statusErrorPage = [
			400 => ['error.http.400', ''],
			401 => ['error.http.401', ''],
			402 => ['error.http.402', ''],
			403 => ['error.http.403', ''],
			404 => ['error.http.404', ''],
			405 => ['error.http.405', ''],
			406 => ['error.http.406', ''],
			407 => ['error.http.407', ''],
			408 => ['error.http.408', ''],
			409 => ['error.http.409', ''],
			410 => ['error.http.410', ''],
			411 => ['error.http.411', ''],
			412 => ['error.http.412', ''],
			413 => ['error.http.413', ''],
			414 => ['error.http.414', ''],
			415 => ['error.http.415', ''],
			416 => ['error.http.416', ''],
			417 => ['error.http.417', ''],
			418 => ['error.http.418', ''],
			500 => ['error.http.500', ''],
			501 => ['error.http.501', ''],
			502 => ['error.http.502', ''],
			503 => ['error.http.503', ''],
			504 => ['error.http.504', ''],
			505 => ['error.http.505', '']
		];

		/**
		 * @var int $_status
		 * @access private
		 */

		private $_status          = 200;

		/**
		 * @var string $_contentType
		 * @access private
		 */

		private $_contentType     = '';

		/**
		 * @var array $_headers
		 * @access private
		 */

		private $_headers         = [];

		/**
		 * @var string $_page
		 * @access private
		 */

		private $_page;

		/**
		 * constructor
		 * @access  public
		 * @since   3.0
		 * @package System\Response
		 */

		private function __construct() {
			foreach ($this->_statusErrorPage as $key => $status){
				switch ($key){
					case 403:
						$this->_statusErrorPage[$key] = Config::config()['user']['framework']['http']['error']['403'];
					break;

					case 404:
						$this->_statusErrorPage[$key] = Config::config()['user']['framework']['http']['error']['404'];
					break;

					case 500:
						$this->_statusErrorPage[$key] = Config::config()['user']['framework']['http']['error']['500'];
					break;

					default:
						$this->_statusErrorPage[$key] = Config::config()['user']['framework']['http']['error']['template'];
					break;
				}
			}

			$this->_status = http_response_code();
			$this->_contentType = 'text/html; charset=' . Config::config()['user']['output']['charset'];
		}

		/**
		 * singleton
		 * @access  public
		 * @since   3.0
		 * @package System\Request
		 */

		public static function instance() {
			if (is_null(self::$_instance)) {
				self::$_instance = new Response();
			}

			return self::$_instance;
		}

		/**
		 * add header to the stack
		 * get headers
		 * @access  public
		 * @param $header string
		 * @return mixed
		 * @since   3.0
		 * @package System\Response
		 */

		public function header($header = null) {
			if ($this->_status != null) {
				array_push($this->_headers, $header);
			}
			else {
				return $this->_headers;
			}
		}

		/**
		 * set the status code. If you use 404, 403 or 500, the framework will display an error page
		 * get the status code
		 * @access  public
		 * @param $status string
		 * @return mixed
		 * @since   3.0
		 * @package System\Response
		 */

		public function status($status = null) {
			if ($status != null) {
				if (array_key_exists($status, $this->_statusCode)) {
					$this->_status = $status;
				}
			}
			else {
				return $this->_status;
			}
		}

		/**
		 * set Content-Type without Content-Type
		 * get Content-Type
		 * @access  public
		 * @param $contentType string
		 * @return mixed
		 * @since   3.0
		 * @package System\Response
		 */

		public function contentType($contentType = null) {
			if ($contentType != null) {
				$this->_contentType = $contentType;
			}
			else {
				return $this->_contentType;
			}
		}

		/**
		 * execute all the headers
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\Response
		 */

		public function run() {
			header('Content-Type: ' . $this->_contentType);

			if ($this->_status != 200) {
				http_response_code($this->_status);
			}

			if (array_key_exists($this->_status, $this->_statusErrorPage)) {
				$tpl = new Template($this->_statusErrorPage[$this->_status][1], $this->_status, '0', Request::instance()->lang);

				$tpl->assign([
					'code'        => $this->_status,
					'description' => $this->_statusCode[$this->_status]
				]);

				$this->_page = $tpl->show();
			}
			else {
				foreach ($this->_headers as $value) {
					header($value);
				}
			}
		}

		/**
		 * return the page content
		 * @access  public
		 * @param $page string
		 * @return mixed
		 * @since   3.0
		 * @package System\Response
		 */

		public function page($page = null) {
			if ($page != null) {
				$this->_page = $page;
			}
			else {
				return $this->_page;
			}
		}

		/**
		 * destructor
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\Response
		 */

		public function __destruct() {
		}
	}