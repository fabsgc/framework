<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Processor.php
	 | @author : Fabien Beaujean
	 | @description : parser
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace Gcs\Framework\Core\Annotation;
	
	/**
	 * Class Processor
	 * @package Gcs\Framework\Core\Annotation
	 */
	
	class Processor {
	
		/**
		 * annotation information
		 * @var string
		 */
	
		protected $_annotation;
	
		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Annotation
		 * @param array $annotation
		 */
	
		public function __construct($annotation = []) {
			$this->_annotation = $annotation;
		}
	
		/**
		 * parse comments to retrieve annotations
		 * @access public
		 * @return \Gcs\Framework\Core\Annotation\Annotation;
		 */
	
		public function process(){
			$className = new $this->_annotation['class'];
			$class = new $className();
	
			foreach ($this->_annotation['properties'] as $property => $value){
				$class->$property = $value;
			}
	
			return $class;
		}
	}