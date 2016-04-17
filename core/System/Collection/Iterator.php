<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Iterator.php
	 | @author : fab@c++
	 | @description : iterator
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Collection;

	class Iterator implements \Iterator{

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
		 * @access public
		 * @since 3.0
		 * @package System\Iterator
		*/

		public function __construct($data){
			$this->_datas = $data;
			$this->position  =  0;
		}

		/**
		 * Initialization
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Collecion
		*/

		function rewind() {
			$this->position = 0;
		}

		/**
		 * Current
		 * @access public
		 * @return object[]
		 * @since 3.0
		 * @package System\Iterator
		*/

		function current() {
			return $this->_datas[$this->_position];
		}

		/**
		 * Get current key
		 * @access public
		 * @return integer
		 * @since 3.0
		 * @package System\Iterator
		*/

		function key() {
			return $this->_position;
		}

		/**
		 * Get next key
		 * @access public
		 * @return integer
		 * @since 3.0
		 * @package System\Iterator
		*/

		function next() {
			++$this->_position;
		}

		/**
		 * key is valid ?
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Iterator
		*/

		function valid() {
			return isset($this->_datas[$this->_position]);
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Controller
		*/

		public function __desctuct(){
		}
	}