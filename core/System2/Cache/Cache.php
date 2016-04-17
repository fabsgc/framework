<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Cache.php
	 | @author : fab@c++
	 | @description : cache system
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Cache;

	class Cache{

		/**
		 * cache name
		 * @var string
		*/

		protected $_name;

		/**
		 * cache file name
		 * @var string
		*/

		protected $_fileName;

		/**
		 * cache time
		 * @var string
		*/

		protected $_time = 0;

		/**
		 * cache content
		 * @var string
		*/

		protected $_content ;
		
		/**
		 * Constructor
		 * @access public
		 * @param $name string : name of the cache file
		 * @param $time int : cache time, default value is 0
		 * @since 3.0
		 * @package System\Cache
		*/
		
		public function __construct($name, $time = 0){
			$this->_name = $name;

			if (!file_exists(APP_CACHE_PATH_DEFAULT)){
				mkdir(APP_CACHE_PATH_DEFAULT, 0777, true);
			}

			if(CACHE_SHA1 == 'true')
				$this->_fileName = APP_CACHE_PATH_DEFAULT.sha1($this->_name.'.cache');
			else
				$this->_fileName = APP_CACHE_PATH_DEFAULT.$this->_name.'.cache';

			if(CACHE_ENABLED == true)
				$this->_time = $time;
			else
				$this->_time = 0;
		}
		
		/**
		 * create the file cache
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Cache
		*/
		
		public function setCache(){
			if(!file_exists($this->_fileName)){
				file_put_contents($this->_fileName, $this->_compress(serialize($this->_content)));
			}
			 
			$timeAgo = time() - filemtime($this->_fileName);
			 
			if($timeAgo > $this->_time){
				file_put_contents($this->_fileName, $this->_compress(serialize($this->_content)));
			}
		}
		
		/**
		 * set content of the cache
		 * @access public
		 * @param string $content : cache content
		 * @return void
		 * @since 3.0
		 * @package System\Cache
		*/
		
		public function setContent($content){
			$this->_content = $content;
		}

		/**
		 * destroy a cache file
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Cache
		*/
		
		public function destroy(){
			if(file_exists($this->_fileName)){
				if(unlink($this->_fileName)){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return true;
			}
		}
		
		/**
		 * get cache content
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Cache
		*/
	 
		public function getCache(){
			if(file_exists($this->_fileName)){
				return unserialize(($this->_uncompress(file_get_contents($this->_fileName))));
			}
			else{
				$this->setCache();
			}

			return unserialize(($this->_uncompress(file_get_contents($this->_fileName))));
		}
	 
		/**
		 * return true if the cache is too old
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Cache
		*/
	 
		public function isDie(){
			if($this->_time > 0){
				$die = false;

				if(!file_exists($this->_fileName)){
					$die = true;
				}
				else{
					$timeAgo = time() - filemtime($this->_fileName);
					if($timeAgo > $this->_time){
						$die = true;
					}
				}
				return $die;
			}
			else{
				return true;
			}
		}

		/**
		 * return true if the file exists
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Cache
		*/

		public function isExist(){
			if(file_exists($this->_fileName)){
				return true;
			}
			else{
				return false;
			}
		}
		
		/**
		 * compress the content
		 * @access public
		 * @param string $content
		 * @return string
		 * @since 3.0
		 * @package System\Cache
		*/
		
		protected function _compress($content){
			return gzcompress($content,9);
		}
		
		/**
		 * uncompress the content
		 * @access public
		 * @param string $val
		 * @return string
		 * @since 3.0
		 * @package System\Cache
		*/
		
		protected function _uncompress($val){
			return gzuncompress($val);
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Cache
		*/

		public function __destruct(){
		}
	}