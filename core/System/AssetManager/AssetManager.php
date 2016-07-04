<?php
	/*\
	 | ------------------------------------------------------
	 | @file : AssetManager.php
	 | @author : fab@c++
	 | @description : css and js manager system (minify, compress, put in cache file)
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\AssetManager;

	use System\Cache\Cache;
	use System\General;

	/**
	 * Class AssetManager
	 * @package System\AssetManager
	 */

	class AssetManager {

		/**
		 * concatenated name files
		 * @var string
		 */

		protected $_name;

		/**
		 * files list
		 * @var string[]
		 */

		protected $_files = [];

		/**
		 * cache file
		 * @var string[]
		 */

		protected $_data = [];

		/**
		 * cache file
		 * @var \System\Cache\Cache
		 */

		protected $_cache;

		/**
		 * cache file
		 * @var integer
		 */

		protected $_time;

		/**
		 * js or css
		 * @var string
		 */
		protected $_type;

		/**
		 * path to the current file
		 * @var string
		 */

		protected $_currentPath;

		/**
		 * concatened content, corrected and compressed
		 * @var string
		 */

		protected $_concatenedContent;

		/**
		 * Constructor
		 * @access  public
		 * @param $data []
		 *              files array
		 *              cache int
		 *              type string
		 * @since   3.0
		 * @package System\AssetManager
		 */

		public function __construct($data = []) {
			foreach ($data as $key => $value) {
				switch ($key) {
					case 'files':
						$this->_setFiles($value);
					break;

					case 'cache':
						$this->_time = abs(intval($value));
					break;

					case 'type':
						$this->_type = $value;
					break;
				}
			}
		}

		/**
		 * get the ID of the generated file
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\AssetManager
		 */

		public function getId() {
			return sha1($this->_name);
		}

		/**
		 * get the type
		 * @access  public
		 * @return string
		 * @since   3.0
		 * @package System\AssetManager
		 */

		public function getType() {
			return $this->_type;
		}

		/**
		 * configuration
		 * @access  protected
		 * @param $data array
		 * @return void
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _setFiles($data = []) {
			foreach ($data as $value) {
				$value = preg_replace('#\\n#isU', '', $value);
				$value = preg_replace('#\\r#isU', '', $value);
				$value = preg_replace('#\\t#isU', '', $value);

				if (is_file(trim($value))) {
					if (empty($this->_data['' . $value . ''])) {
						$this->_setFile($value);
					}
				}
				else if (is_dir(trim($value))) {
					$this->_setDir($value);
				}
			}

			$this->_cache = new Cache(sha1($this->_name) . '.' . $this->_type, $this->_time);

			if ($this->_cache->isDie()) {
				$this->_compress();
				$this->_save();
			}
		}

		/**
		 * configure one file
		 * @access  public
		 * @param $path string
		 * @return void
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _setFile($path) {
			$this->_name .= $path;
			$this->_data['' . $path . ''] = file_get_contents($path);

			if ($this->_type == 'css') {
				$this->_currentPath = dirname($path) . '/';
				$this->_data['' . $path . ''] = preg_replace_callback('`url\((.*)\)`isU', ['System\AssetManager\AssetManager', '_parseRelativePathCssUrl'], $this->_data['' . $path . '']);
				$this->_data['' . $path . ''] = preg_replace_callback('`src=\'(.*)\'`isU', ['System\AssetManager\AssetManager', '_parseRelativePathCssSrc'], $this->_data['' . $path . '']);
			}
		}

		/**
		 * configure a directory
		 * @access  public
		 * @param $path string
		 * @return void
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _setDir($path) {
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					$extension = explode('.', basename($entry));
					$ext = $extension[count($extension) - 1];

					if ($ext == $this->_type) {
						$this->_setFile($path . $entry);
					}
				}

				closedir($handle);
			}
		}

		/**
		 * parse url()
		 * @access  public
		 * @param $m array
		 * @return string
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _parseRelativePathCssUrl($m) {
			return 'url(' . $this->_parseRelativePathCss($m) . ')';
		}

		/**
		 * parse src=""
		 * @access  public
		 * @param $m array
		 * @return string
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _parseRelativePathCssSrc($m) {
			return 'src=\'' . $this->_parseRelativePathCss($m) . '\'';
		}

		/**
		 * correct wrong links
		 * @access  public
		 * @param $m array
		 * @return string
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _parseRelativePathCss($m) {
			/**
			 * We take the page. Each time we have a '../' in a path file in the css, we drop a folder of the parent file. Zxample :
			 *   css file : css/dossier/truc/test.css
			 *   image file in css : ../../test.png
			 *   we have two ../ so we delete two folder : css/test/test.png
			 */

			$pathReplace = '';

			//we clear the '/' at the beginning
			$m[1] = preg_replace("#^/#isU", '', $m[1]);
			$m[1] = str_replace('"', '', $m[1]);
			$m[1] = str_replace("'", '', $m[1]);
			$this->_currentPath = preg_replace("#^/#isU", '', $this->_currentPath);

			//on count the number of '../'
			$numberParentDir = substr_count($m[1], '../');

			if ($numberParentDir > 0) {
				for ($i = 0; $i < $numberParentDir; $i++) {
					$pathReplace .= '(.[^\/]+)\/';
				}

				$pathReplace .= '$';
				$newCurrentPath = preg_replace('#' . $pathReplace . '#isU', '/', $this->_currentPath);
				$m[1] = preg_replace('#\.\./#isU', '', $m[1]);

				if ($newCurrentPath != $this->_currentPath) {
					$m[1] = $newCurrentPath . $m[1];
				}
				else if (!preg_match('#^' . preg_quote($newCurrentPath) . '#isU', $m[1])) {
					if (!preg_match('#^/asset#isU', $m[1]) && !preg_match('#^asset#isU', $m[1])) {
						$m[1] = $newCurrentPath . $m[1];
					}
				}
			}

			if (!preg_match('#^/#isU', $m[1])) {
				$m[1] = '/' . $m[1];
			}

			return 'http://' . $_SERVER['HTTP_HOST'] . str_replace('//', '/', FOLDER . $m[1]);
		}

		/**
		 * concatenate parser content
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _compress() {
			//$before = '(?<=[:(, ])';
			//$after = '(?=[ ,);}])';
			//$units = '(em|ex|%|px|cm|mm|in|pt|pc|ch|rem|vh|vw|vmin|vmax|vm)';

			foreach ($this->_data as $value) {
				$this->_concatenedContent .= $value;
			}

			if ($this->_type == 'css') {
				$this->_concatenedContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $this->_concatenedContent);
				$this->_concatenedContent = str_replace(': ', ':', $this->_concatenedContent);
				$this->_concatenedContent = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $this->_concatenedContent);
			}
		}

		/**
		 * save content in cache
		 * @access  public
		 * @return void
		 * @since   3.0
		 * @package System\AssetManager
		 */

		protected function _save() {
			$this->_cache->setContent($this->_concatenedContent);
			$this->_cache->setCache();
		}

		/**
		 * destructor
		 * @access  public
		 * @since   3.0
		 * @package System\AssetManager
		 */

		public function __destruct() {
		}
	}