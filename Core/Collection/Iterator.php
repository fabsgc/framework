<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Iterator.php
	 | @author : Fabien Beaujean
	 | @description : iterator
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Collection;

	/**
	 * Class Iterator
	 * @package Gcs\Framework\Core\Collection
	 */

	class Iterator implements \Iterator {
		
		/**
		 * @var integer
		 */

		private $_position = 0;

		/**
		 * @var object[]
		 */

		private $_datas = [];

		/**
		 * Constructor
		 * @param string $data
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Iterator
		 * @param        $data
		 */

		public function __construct($data) {
			$this->_datas = $data;
			$this->_position = 0;
		}

		/**
		 * Initialization
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package Gcs\Framework\Core\Collecion
		 */

		function rewind() {
			$this->_position = 0;
		}

		/**
		 * Current
		 * @access public
		 * @return object[]
		 * @since 3.0
		 * @package Gcs\Framework\Core\Iterator
		 */

		function current() {
			return $this->_datas[$this->_position];
		}

		/**
		 * Get current key
		 * @access public
		 * @return integer
		 * @since 3.0
		 * @package Gcs\Framework\Core\Iterator
		 */

		function key() {
			return $this->_position;
		}

		/**
		 * Get next key
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package Gcs\Framework\Core\Iterator
		 */

		function next() {
			++$this->_position;
		}

		/**
		 * key is valid ?
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package Gcs\Framework\Core\Iterator
		 */

		function valid() {
			return isset($this->_datas[$this->_position]);
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Controller
		 */

		public function __desctuct() {
		}
	}