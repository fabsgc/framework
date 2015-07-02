<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Builder.php
	 | @author : fab@c++
	 | @description : Query Builder
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Collection;

	use System\Exception\Exception;

	class Collection implements \IteratorAggregate {

		/**
		 * @var array[]
		*/

		private $_datas = array();

		/**
		 * Constructor
		 * @access public
		 * @since 3.0
		 * @package System\Orm
		*/

		public function __construct($data = array()) {
			$this->_datas = $data;
		}

		/**
		 * Get iterator
		 * @access public
		 * @return \System\Collection\Iterator
		 * @since 3.0
		 * @package System\Orm
		*/

		public function getIterator() {
			return new Iterator($this->_datas);
		}

		/**
		 * First value
		 * @access public
		 * @return \System\Orm\Entity\Entity
		 * @since 3.0
		 * @package System\Orm
		*/

		public function first(){
			if(isset($this->_datas[0]))
				return $this->_datas[0];
			else
				return null;
		}

		/**
		 * Count
		 * @access public
		 * @return integer
		 * @since 3.0
		 * @package System\Orm
		*/

		public function count(){
			return count($this->_datas);
		}

		/**
		 * Count
		 * @access public
		 * @return array[]
		 * @since 3.0
		 * @package System\Collecion
		*/

		public function data(){
			return $this->_datas;
		}

		/**
		 * Add elements to the collection
		 * @access public
		 * @param $data mixed array[], Collection
		 * @return void
		 * @since 3.0
		 * @package System\Collecion
		*/

		public function add($data){
			if(!array_search($data, $this->_datas, true) && is_object($data)){
				$data = clone $data;
			}

			if(is_array($data)){
				array_push($this->_datas, $data);
			}
			else{
				if(get_class($data) != 'System\Collection\Collection'){
					array_push($this->_datas, $data);
				}
				else{
					array_push($this->_datas, $data);
				}
			}
		}

		/**
		 * Filter the collection with a closure
		 * @access public
		 * @param $closure callable
		 * @return Collection
		 * @since 3.0
		 * @package System\Collecion
		*/

		public function filter($closure){
			$collection = new Collection();

			foreach($this->_datas as $data){
				if($closure($data)){
					$collection->add($data);
				}
			}

			return $collection;
		}

		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Collecion
		*/

		public function __destruct(){
		}
	}