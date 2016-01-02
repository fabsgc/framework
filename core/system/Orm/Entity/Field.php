<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Field.php
	 | @author : fab@c++
	 | @description : represent a field of an entity
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Entity;

	use System\Exception\MissingEntityException;
	use System\Orm\Builder;

	class Field {

		const INCREMENT  =  0;
		const INT        =  1;
		const CHAR       =  2;
		const TEXT       =  3;
		const STRING     =  4;
		const BOOL       =  5;
		const FILE       =  6;
		const FLOAT      =  8;
		const DATE       =  9;
		const DATETIME   = 10;
		const TIME       = 11;
		const TIMESTAMP  = 12;
		const ENUM       = 14;

		/**
		 * @var string
		*/

		public $type = self::INT;

		/**
		 * @var string
		*/

		public $name = null;

		/**
		 * @var string
		*/

		public $entity = null;

		/**
		 * @var boolean
		*/

		public $primary = false;


		/**
		 * @var integer
		*/

		protected $size = 0;

		/**
		 * @var \System\Orm\Entity\ForeignKey
		*/

		public $foreign = null;

		/**
		 * @var boolean
		*/

		public $unique = false;

		/**
		 * if the field is INT,FLOAT etc.
		 * @var integer[]
		*/

		public $precision = [];

		/**
		 * if the field is INT,FLOAT etc.
		 * @var string[]
		*/

		public $enum = [];

		/**
		 * the field can be null ?
		 * @var boolean
		*/

		public $beNull = true;

		/**
		 * @var mixed string|\System\Orm\Entity\Field|\System\Orm\Entity\Field[]|\System\Orm\Entity\Type
		*/

		public $value = null;

		/**
		 * Constructor
		 * @access public
		 * @param string $name
		 * @param string $entity
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function __construct($name, $entity) {
			$this->name = $name;
			$this->entity = $entity;
			return $this;
		}

		/**
		 * Set type
		 * @access public
		 * @param integer $type
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function type($type = self::BOOL){
			$this->type = $type;
			return $this;
		}

		/**
		 * Set name
		 * @access public
		 * @param string $name
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function name($name = ''){
			$this->name = $name;
			return $this;
		}

		/**
		 * Set primary key
		 * @access public
		 * @param boolean $primary
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function primary($primary = false){
			$this->primary = $primary;
			return $this;
		}

		/**
		 * Set primary key
		 * @access public
		 * @param $size integer
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function size($size = 255){
			$this->size = $size;
			return $this;
		}

		/**
		 * Set foreigns key
		 * @access public
		 * @param $datas array
		 * @throws MissingEntityException
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function foreign($datas = []){
			if(!array_key_exists('type', $datas)){
				throw new MissingEntityException('The parameter "type" is missing for the foreign key');
			}

			if(!array_key_exists('reference', $datas)){
				throw new MissingEntityException('The parameter "reference" is missing for the foreign key');
			}

			if(!array_key_exists('belong', $datas)){
				$datas['belong'] = ForeignKey::AGGREGATION;
			}

			if(!array_key_exists('current', $datas)){
				$datas['current'] = [$this->entity, $this->name];
			}

			if(!array_key_exists('value', $datas)){
				$datas['value'] = '';
			}

			if(!array_key_exists('join', $datas)){
				$datas['join'] = Builder::JOIN_INNER;
			}

			$this->foreign = new ForeignKey($datas);

			return $this;
		}

		/**
		 * Set unique
		 * @access public
		 * @param boolean $unique
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function unique($unique = false){
			$this->unique = $unique;
			return $this;
		}

		/**
		 * Set precision
		 * @access public
		 * @param $precision string
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function precision($precision){
			$this->precision = $precision;
			return $this;
		}

		/**
		 * Set precision
		 * @access public
		 * @param $enum string[]
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function enum($enum = []){
			$this->enum = $enum;
			return $this;
		}

		/**
		 * Set beNull
		 * @access public
		 * @param $beNull boolean
		 * @since 3.0
		 * @return \System\Orm\Entity\Field
		 * @package System\Orm\Entity
		*/

		public function beNull($beNull){
			$this->beNull = $beNull;
			return $this;
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