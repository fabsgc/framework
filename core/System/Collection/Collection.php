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

	class Collection implements \IteratorAggregate {

		/**
		 * @var array[]
		*/

		private $_datas = [];

		/**
		 * Constructor
		 * @access public
		 * @param $data array
		 * @since 3.0
		 * @package System\Orm
		*/

		public function __construct($data = []) {
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
				$this->_datas = array_merge($this->_datas, $data);
			}
			else{
				if(get_class($data) != 'System\Collection\Collection'){
					array_push($this->_datas, $data);
				}
				else{
					array_merge($this->_datas, $data->data());
				}
			}
		}

		/**
		 * Delete one element from the collection
		 * @access public
		 * @param $key mixed
		 * @return void
		 * @since 3.0
		 * @package System\Collecion
		*/

		public function delete($key){
			unset($this->_datas[$key]);
			$this->_datas = array_values($this->_datas);
		}

		/**
		 * Delete between 2 keys
		 * @access public
		 * @param $key int
		 * @param $length int
		 * @return void
		 * @since 3.0
		 * @package System\Collecion
		*/

		public function deleteRange($key, $length){
			$badKeys = [];

			for($i = $key; $i < $key + $length; $i++){
				array_push($badKeys, $i);
			}

			$this->_datas = array_diff_key($this->_datas, array_flip($badKeys));
			$this->_datas = array_values($this->_datas);
		}

		/**
		 * Get between 2 keys
		 * @access public
		 * @param $key int
		 * @param $length int
		 * @return array
		 * @since 3.0
		 * @package System\Collecion
		 */

		public function getRange($key, $length){
			$badKeys = [];

			for($i = 0; $i < $key; $i++){
				array_push($badKeys, $i);
			}

			for($i = $key + $length; $i < count($this->_datas); $i++){
				array_push($badKeys, $i);
			}

			return array_diff_key($this->_datas, array_flip($badKeys));
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