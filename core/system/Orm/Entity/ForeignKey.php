<?php
	/*\
	 | ------------------------------------------------------
	 | @file : ForeignKey.php
	 | @author : fab@c++
	 | @description : represent a foreign key constraint
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Entity;

	use System\Orm\Builder;

	class ForeignKey {

		const ONE_TO_ONE   = 0;
		const ONE_TO_MANY  = 1;
		const MANY_TO_ONE  = 2;
		const MANY_TO_MANY = 3;

		const AGGREGATION = 0;
		const COMPOSITION = 1;

		/**
		 * @var string
		*/

		protected $_entity = '';

		/**
		 * the field in current table
		 * @var string
		*/

		protected $_field = '';

		/**
		 * the reference table, because sometimes, the field which has the relation
		 * is not the field which has the constraint (many to many AND one to many)
		 * @var string
		 */

		protected $_referenceEntity = '';

		/**
		 * @var string
		*/

		protected $_referenceField = '';

		/**
		 * If you use scaffolding, you can show the
		 * value of a field from the referenced table
		 * @var string
		*/

		protected $_value = '';

		/**
		 * You can choose the type of Join used
		 * @var string
		*/

		protected $_join = Builder::JOIN_INNER;

		/**
		 * @var integer
		*/

		protected $_type = self::ONE_TO_ONE;

		/**
		 * the field can be null ?
		 * @var boolean
		*/

		protected $_belong = self::AGGREGATION;

		/**
		 * Constructor
		 * @access public
		 * @param $datas array
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function __construct($datas = array()) {
			foreach($datas as $key => $data){
				switch($key){
					case 'type' :
						$this->_type = $data;
					break;

					case 'belong' :
						$this->_belong = $data;
					break;

					case 'current' :
						$this->_entity = $data[0];
						$this->_field  = $data[1];
					break;

					case 'reference' :
						$this->_referenceEntity = $data[0];
						$this->_referenceField  = $data[1];
					break;

					case 'value' :
						$this->_value = $data;
					break;

					case 'join' :
						$this->_join = $data;
					break;
				}
			}
		}

		/**
		 * set or get entity
		 * @access public
		 * @param $entity string
		 * @return mixed void,string
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function entity($entity = '') {
			if($entity != '')
				$this->_entity = $entity;
			else
				return $this->_entity;
		}

		/**
		 * set or get field
		 * @access public
		 * @param $field string
		 * @return mixed void,string
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function field($field = '') {
			if($field != '')
				$this->_field = $field;
			else
				return $this->_field;
		}

		/**
		 * set or get reference entity
		 * @access public
		 * @param $entity string
		 * @return mixed void,string
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function referenceEntity($entity = '') {
			if($entity != '')
				$this->_referenceEntity = $entity;
			else
				return $this->_referenceEntity;
		}

		/**
		 * set or get reference entity
		 * @access public
		 * @param $field string
		 * @return mixed void,string
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function referenceField($field = '') {
			if($field != '')
				$this->_referenceField = $field;
			else
				return $this->_referenceField;
		}

		/**
		 * set or get field
		 * @access public
		 * @param $type string
		 * @return mixed void,integer
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function type($type = '') {
			if($type != '')
				$this->_type = $type;
			else
				return $this->_type;
		}

		/**
		 * belong
		 * @access public
		 * @param $belong string
		 * @return mixed void,integer
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function belong($belong = '') {
			if($belong != '')
				$this->_belong = $belong;
			else
				return $this->_belong;
		}

		/**
		 * join
		 * @access public
		 * @param $join string
		 * @return mixed integer
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function join($join = '') {
			if($join != '')
				$this->_join = $join;
			else
				return $this->_join;
		}

		/**
		 * value
		 * @access public
		 * @param $value string
		 * @return string
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function value($value = '') {
			if($value != '')
				$this->_value = $value;
			else
				return $this->_value;
		}

		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function __destruct(){
		}
	}