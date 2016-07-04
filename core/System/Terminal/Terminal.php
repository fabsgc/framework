<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Terminal.php
	 | @author : fab@c++
	 | @description : terminal
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Terminal;

	/**
	 * Class Terminal
	 * @package System\Terminal
	 */

	class Terminal {

		/**
		 * args
		 * @var array $_argv
		 * @access protected
		 */

		protected $_argv = [];

		/**
		 * init terminal
		 * @access  public
		 * @since   3.0
		 * @package System\Terminal
		 */

		public function __construct() {
			$this->_parseArg($_SERVER['argv']);

			if (isset($this->_argv[0])) {
				$this->_command();
			}
		}

		/**
		 * Parse terminal parameters to allow user to use spaces
		 * @access  public
		 * @param $argv string
		 * @return void
		 * @since   3.0
		 * @package System\Terminal
		 */

		protected function _parseArg($argv) {
			for ($i = 0; $i < count($argv); $i++) {
				if ($argv[$i] != 'console') {
					if (!preg_match('#\[#', $argv[$i])) {
						array_push($this->_argv, $argv[$i]);
					}
					else {
						$data = '';

						for ($i = 0; $i < count($argv); $i++) {
							$data .= $argv[$i] . ' ';

							if (preg_match('#\]#', $argv[$i])) {
								$data = str_replace(['[', ']'], ['', ''], $data);
								array_push($this->_argv, trim($data));
								break;
							}
						}
					}
				}
			}
		}

		/**
		 * Terminal interpreter
		 * @access   public
		 * @internal param string $argv
		 * @return void
		 * @since    3.0
		 * @package  System\Terminal
		 */

		protected function _command() {
			$class = '\System\Terminal\Terminal' . ucfirst($this->_argv[0]);

			if (isset($this->_argv[1])) {
				$method = $this->_argv[1];
			}
			else if ($this->_argv[0] == 'help') {
				$method = 'help';
			}
			else {
				$method = '';
			}

			if (method_exists($class, $method)) {
				$instance = new $class($this->_argv);
				$instance->$method($this->_argv);
			}
			else {
				if (isset($this->_argv[1])) {
					echo '[ERROR] unrecognized command "' . $this->_argv[0] . ' ' . $this->_argv[1] . '"';
				}
				else {
					echo '[ERROR] unrecognized command "' . $this->_argv[0] . '"\n';
				}
			}
		}

		/**
		 * Remove directory content
		 * @access  public
		 * @param $dir       string : path
		 * @param $removeDir : remove subdirectories too
		 * @param $except    : files you don't want to delete
		 * @return void
		 * @since   3.0
		 * @package System\Terminal
		 */

		public static function rrmdir($dir, $removeDir = false, $except = []) {
			if (is_dir($dir)) {
				$objects = scandir($dir);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (filetype($dir . "/" . $object) == "dir") {
							Terminal::rrmdir($dir . "/" . $object, $removeDir, $except);

							if ($removeDir == true) {
								rmdir($dir . "/" . $object . '/');
							}
						}
						else {
							if (!in_array($object, $except)) {
								unlink($dir . "/" . $object);
							}
						}
					}
				}
				reset($objects);
			}
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\Terminal
		 */

		public function __destruct() {
		}
	}

	class TerminalCommand {
		/**
		 * command content
		 * @var
		 */

		protected $_argv;

		/**
		 * init terminal command
		 * @access  public
		 * @param $argv
		 * @since   3.0
		 * @package System\Terminal
		 */

		public function __construct($argv) {
			$this->_argv = $argv;
		}
	}

	class ArgvInput {
		public static function get() {
			$data = fgets(STDIN);
			$data = substr($data, 0, -2);

			return $data;
		}
	}