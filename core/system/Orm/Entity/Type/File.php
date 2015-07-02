<?php
	/*\
	 | ------------------------------------------------------
	 | @file : File.php
	 | @author : fab@c++
	 | @description : permit to store a file in a db
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Entity\Type;

	class File extends Type {

		/**
		 * @var string
		*/

		public $name = '';

		/**
		 * @var string
		 */

		public $path = '';

		/**
		 * if you edit the file name, we use oldFile to delete the original file
		 * @var string
		*/

		protected $_oldFile = '';

		/**
		 * @var string
		*/

		public $content = '';

		/**
		 * @var string
		*/

		public $contentType = '';

		/**
		 * Constructor
		 * @access public
		 * @param string $file
		 * @param string $content
		 * @param string $contentType
		 * @since 3.0
		 * @package System\Orm\Entity\Type
		*/

		public function __construct($file, $content, $contentType){
			$this->path        = dirname($file).'/';
			$this->name        = basename($file);
			$this->_oldFile    = $file;
			$this->contentType = $contentType;
			$this->content     = $content;
		}

		/**
		 * Hydrate object
		 * @access public
		 * @param $field string
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity\Type
		*/

		public function hydrate($field){
			$this->path        = preg_replace('#^([^\|]*)\|([^\|]+)\|([^\|]+)$#', '$1', $field);
			$this->name        = preg_replace('#^([^\|]*)\|([^|]+)\|([^\|]+)$#', '$2', $field);
			$this->contentType = preg_replace('#^([^\|]*)\|([^\|]+)\|([^\|]+)$#', '$3', $field);

			if(!in_array(substr($this->path, strlen($this->path)-1, strlen($this->path)), array('/', '\\')) && $this->path != ''){
				$this->path .= '/';
			}

			$this->_oldFile = $this->path.$this->name;
			$this->content  = file_get_contents($this->_oldFile);
		}

		/**
		 * Which value orm save in the database
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package string
		*/

		public function value(){
			return substr($this->path, strlen($this->path)-1, 1).'|'.$this->name.'|'.$this->contentType;
		}

		/**
		 * Save the file on the HDD
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity\Type
		*/

		public function save(){
			if($this->_oldFile != $this->path.$this->name){
				unlink($this->_oldFile);
			}

			file_put_contents($this->path.$this->name, $this->content);
		}

		/**
		 * Delete the file
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity\Type
		*/

		public function delete(){
			if($this->_oldFile != $this->path.$this->name){
				unlink($this->_oldFile);
			}

			unlink($this->path.$this->name);
		}

		/**
		 * get extension
		 * @access public
		 * @since 3.0
		 * @package string
		*/

		public function extension(){
			return substr(strrchr($this->name, '.'), 1);
		}

		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Orm\Entity\Type
		*/

		public function __destruct(){
		}
	}