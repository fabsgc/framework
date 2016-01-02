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

	use System\General\error;
	use System\General\facades;
	use System\General\langs;
	use System\General\singleton;

	class Response{
		use error, langs, singleton, facades;

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

		protected $_statusErrorPage = array(
			400 => ['error.http.400', ERROR_TEMPLATE],
			401 => ['error.http.401', ERROR_TEMPLATE],
			402 => ['error.http.402', ERROR_TEMPLATE],
			403 => ['error.http.403', ERROR_403_TEMPLATE],
			404 => ['error.http.404', ERROR_404_TEMPLATE],
			405 => ['error.http.405', ERROR_TEMPLATE],
			406 => ['error.http.406', ERROR_TEMPLATE],
			407 => ['error.http.407', ERROR_TEMPLATE],
			408 => ['error.http.408', ERROR_TEMPLATE],
			409 => ['error.http.409', ERROR_TEMPLATE],
			410 => ['error.http.410', ERROR_TEMPLATE],
			411 => ['error.http.411', ERROR_TEMPLATE],
			412 => ['error.http.412', ERROR_TEMPLATE],
			413 => ['error.http.413', ERROR_TEMPLATE],
			414 => ['error.http.414', ERROR_TEMPLATE],
			415 => ['error.http.415', ERROR_TEMPLATE],
			416 => ['error.http.416', ERROR_TEMPLATE],
			417 => ['error.http.417', ERROR_TEMPLATE],
			418 => ['error.http.418', ERROR_TEMPLATE],
			500 => ['error.http.500', ERROR_500_TEMPLATE],
			501 => ['error.http.501', ERROR_TEMPLATE],
			502 => ['error.http.502', ERROR_TEMPLATE],
			503 => ['error.http.503', ERROR_TEMPLATE],
			504 => ['error.http.504', ERROR_TEMPLATE],
			505 => ['error.http.505', ERROR_TEMPLATE]
		);

		protected $_status      = null;
		protected $_contentType = null;
		protected $_headers =      [] ;
		protected $_page              ;

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package System\Response
		*/

		private function __construct (){
			$this->_status = http_response_code();
			$this->_contentType = 'text/html; charset='.CHARSET;
		}

		/**
		 * singleton
		 * @access public
		 * @since 3.0
		 * @package System\Request
		*/

		public static function getInstance(){
			if (is_null(self::$_instance))
				self::$_instance = new Response();

			return self::$_instance;
		}

		/**
		 * add header to the stack
		 * get headers
		 * @access public
		 * @param $header string
		 * @return mixed
		 * @since 3.0
		 * @package System\Response
		*/

		public function header($header = null){
			if($this->_status != null){
				array_push($this->_headers, $header);
			}
			else{
				return $this->_headers;
			}
		}

		/**
		 * set the status code. If you use 404, 403 or 500, the framework will display an error page
		 * get the status code
		 * @access public
		 * @param $status string
		 * @return mixed
		 * @since 3.0
		 * @package System\Response
		*/

		public function status($status = null){
			if($status != null){
				if(array_key_exists($status, $this->_statusCode)){
					$this->_status = $status;
				}
			}
			else{
				return $this->_status;
			}
		}

		/**
		 * set Content-Type without Content-Type
		 * get Content-Type
		 * @access public
		 * @param $contentType string
		 * @return mixed
		 * @since 3.0
		 * @package System\Response
		*/

		public function contentType($contentType = null){
			if($contentType != null){
				$this->_contentType = $contentType;
			}
			else{
				return $this->_contentType;
			}
		}

		/**
		 * execute all the headers
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Response
		*/

		public function run(){
			header('Content-Type: '.$this->_contentType);
			
			if($this->_status != 200)
				http_response_code($this->_status);

			if(array_key_exists($this->_status, $this->_statusErrorPage)){
				$tpl = self::Template($this->_statusErrorPage[$this->_status][1], $this->_status, '0', self::Request()->lang);

				$tpl->assign(array(
					'code' => $this->_status,
					'description' => $this->_statusCode[$this->_status]
				));

				$this->_page = $tpl->show();
			}
			else{
				foreach ($this->_headers as $value) {
					header($value);
				}
			}
		}

		/**
		 * return the page content
		 * @access public
		 * @param $page string
		 * @return mixed
		 * @since 3.0
		 * @package System\Response
		*/

		public function page($page = null){
			if($page != null){
				$this->_page = $page;
			}
			else{
				return $this->_page;
			}
		}

		/**
		 * destructor
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Response
		*/

		public function __destruct(){
		}
	}