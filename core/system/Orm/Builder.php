<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Builder.php
	 | @author : fab@c++
	 | @description : Query Builder
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm;

	use System\Collection\Collection;
	use System\Exception\MissingEntityException;
	use System\General\facades;
	use System\Orm\Entity\ForeignKey;
	use System\Sql\Sql;

	class Builder {
		use facades;

		const QUERY_SELECT   = 0;
		const QUERY_DISTINCT = 1;
		const QUERY_RAW      = 2;

		const JOIN_LEFT  =  'LEFT JOIN';
		const JOIN_RIGHT = 'RIGHT JOIN';
		const JOIN_INNER = 'INNER JOIN';
		const JOIN_FULL  =  'FULL JOIN';

		const RETURN_COLLECTION = 0;
		const RETURN_ENTITY     = 1;

		/**
		 * the query
		 * @var string
		*/

		protected $_query;

		/**
		 * vars used in the query
		 * @var array
		*/

		protected $_vars;

		/**
		 * if you use SELECT DISTINCT, it contains all the fields of the DISTINCT
		 * @var string
		*/

		protected $_distinct;

		/**
		 * the entity used to make the query
		 * @var \System\Orm\Entity\Entity
		*/

		protected  $_entity;

		/**
		 * We use an object format for the field : Post.id, so we must know which tables are used in the query
		 * @var string[]
		*/

		protected $_entities = array();

		/**
		 * the type (SELECT, SELECT DISTINCT)
		 * @var integer
		*/

		protected  $_type = self::QUERY_SELECT;

		/**
		 * token, to have unique cache file
		 * @var string
		*/

		protected $_token = '';

		/**
		 * Constructor
		 * @access public
		 * @param $entity \System\Orm\Entity\Entity
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function __construct($entity) {
			$this->_entity = $entity;
			$this->_token = rand(0,100);
			array_push($this->_entities, str_replace('Orm\Entity\\', '', get_class($entity)));
			return $this;
		}

		/**
		 * add variables to the instance
		 * @access public
		 * @param $var  mixed : contain the list of the variable that will be used in the queries.
		 *  first syntax  : array('id' => array(31, Sql::PARAM_INT), 'pass' => array("fuck", sql::PARAM_STR))
		 *  second syntax : array('id' => 31, 'pass' => "fuck"). If you don't define the type of the variable, the class will assign itself the correct type
		 *  If you have only one variable to pass, you can use the 2/3 parameters form
		 *	first syntax  : ('id', 'value')
		 *  second syntax : ('id', 'value', Sql::PARAM_INT)
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function vars($var){
			if(is_array($var)){
				foreach($var as $key => $valeur){
					$this->_vars[$key] = $valeur;
				}
			}
			else if(func_num_args() == 2){
				$args = func_get_args();
				$this->_vars[$args[0]] = $args[1];
			}

			else if(func_num_args() == 3){
				$args = func_get_args();
				$this->_vars[$args[0]] = array($args[1], $args[2]);
			}

			return $this;
		}

		/**
		 * create a select query
		 * @access public
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function find() {
			$this->_type = self::QUERY_SELECT;
			$this->_getSelect();

			return $this;
		}

		/**
		 * create a select distinct query
		 * @access public
		 * @param $distinct string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function findDistinct($distinct) {
			$this->_detectEntity($distinct);
			$this->_distinct = $distinct;
			$this->_type = self::QUERY_DISTINCT;
			$this->_getSelect();

			return $this;
		}

		/**
		 * create a query without sql completion
		 * @access public
		 * @param $query string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		 */

		public function findRaw($query) {
			$this->_type = self::QUERY_RAW;
			$this->_query = $query;

			return $this;
		}

		/**
		 * add where clause
		 * @access public
		 * @param $where string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function where($where){
			$this->_detectEntity($where);
			$this->_query .=' WHERE '.$where;

			return $this;
		}

		/**
		 * add and where clause
		 * @access public
		 * @param $where string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function andWhere($where){
			$this->_detectEntity($where);
			$this->_query .=' AND '.$where;

			return $this;
		}

		/**
		 * add or where clause
		 * @access public
		 * @param $where string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function orWhere($where){
			$this->_detectEntity($where);
			$this->_query .=' OR '.$where;

			return $this;
		}

		/**
		 * add join
		 * @access public
		 * @param $type string
		 * @param $table string
		 * @param $on string
		 * @throws MissingEntityException
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function join($type = self::JOIN_INNER, $table, $on){
			$class = $this->_getTableName($table);

			if($table != $class->name())
				$table = $class->name();

			if($class->name() != $this->_entity->name()){
				$this->_detectEntity($on);
				$this->_query .=' '.$type.' '.$table.' ON '.$on;
			}

			return $this;
		}

		/**
		 * add order by
		 * @access public
		 * @param $orderBy string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function orderBy($orderBy){
			$this->_detectEntity($orderBy);
			$this->_query .=' ORDER BY '.$orderBy;

			return $this;
		}

		/**
		 * add group by
		 * @access public
		 * @param $groupBy string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function groupBy($groupBy){
			$this->_detectEntity($groupBy);
			$this->_query .=' GROUP BY '.$groupBy;

			return $this;
		}

		/**
		 * add having
		 * @access public
		 * @param $having string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function having($having){
			$this->_detectEntity($having);
			$this->_query .=' HAVING '.$having;

			return $this;
		}

		/**
		 * add limit
		 * @access public
		 * @param $offset integer
		 * @param $number integer
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function limit($offset, $number){
			$limit = rand(0,50);
			$this->_query .=' LIMIT :limit_offset_'.$limit.', :limit_number_'.$limit;

			$this->vars('limit_offset_'.$limit, array($offset, Sql::PARAM_INT));
			$this->vars('limit_number_'.$limit, array($number, Sql::PARAM_INT));

			return $this;
		}

		/**
		 * add raw
		 * @access public
		 * @param $raw string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm
		*/

		public function raw($raw){
			$this->_query .= ' '.$raw;
			return $this;
		}

		/**
		 * detect new entity in the query
		 * @access public
		 * @param $query string
		 * @return void
		 * @since 3.0
		 * @package System\Orm
		*/

		protected function _detectEntity($query){
			preg_replace_callback('`([a-zA-Z]+)\.([a-zA-Z]+)`sU', array('System\Orm\Builder', '_detectEntityCallback'), $query);
		}

		/**
		 * detect new entity in the query [callback]
		 * @access public
		 * @param $m string[]
		 * @return void
		 * @since 3.0
		 * @package System\Orm
		*/

		protected function _detectEntityCallback($m){
			if(!in_array($m[1], $this->_entities))
				array_push($this->_entities, $m[1]);
		}

		/**
		 * fetch the current query
		 * @access public
		 * @param $return integer
		 * @return \System\Collection\Collection
		 * @since 3.0
		 * @package System\Orm
		 */

		public function fetch($return = self::RETURN_COLLECTION) {
			/** We replace Post.xx by post.xx */
			foreach($this->_entities as $entity){
				$class = $this->_getTableName($entity);

				if($entity != $class->name())
					$this->_query = preg_replace('#(.*)'.$entity.'\.(.*)#isU', '$1'.$class->name().'.$2', $this->_query);
			}

			/** Query execution */
			$sql = self::Sql();
			$sql->vars($this->_vars);
			$sql->query('orm-'.$this->_token, $this->_query);
			$sql->fetch('orm-'.$this->_token, Sql::PARAM_FETCH);

			$collection = $sql->data($this->_getTableName($this->_entity->name())->name());

			/** We can do SELECT OR SELECT DISTINCT OR SELECT RAW */
			if(in_array($this->_type, array(self::QUERY_DISTINCT, self::QUERY_RAW))){
				$nLines = $collection->count();
				$in = '';

				/** @var $value \System\Orm\Entity\Entity */
				foreach($collection as $key => $value){
					$in .= $value->fields()[$value->primary()]->value;

					if($key < $nLines - 1){
						$in .= ', ';
					}
				}

				$builder = new Builder($this->_entity);
				$collection = $builder->find()->where($this->_entity->name().'.'.$this->_entity->primary(). ' IN ('.$in.')')->fetch();
			}

			/** If we have fields with a relation ONE TO MANY OR MANY TO MANY */
			foreach($this->_entity->fields() as $field){
				if($field->foreign != null && in_array($field->foreign->type(), array(ForeignKey::ONE_TO_MANY, ForeignKey::MANY_TO_MANY))){
					/** we loop through the results to add each time */
					switch($field->foreign->type()){
						case ForeignKey::ONE_TO_MANY :
							$collection = $this->_dataOneToMany($field, $collection);
						break;

						case ForeignKey::MANY_TO_MANY :
							$collection = $this->_dataManyToMany($field, $collection);
						break;
					}
				}
			}

			if($return == self::RETURN_ENTITY && $collection->count() == 1)
				return $collection->first();
			else
				return $collection;
		}

		/**
		 * If the entity has at least one to many relation, we had a collection to the right field
		 * @access public
		 * @param $field \System\Orm\Entity\Field : foreign key field
		 * @param $collection \System\Collection\Collection
		 * @return \System\Collection\Collection
		 * @since 3.0
		 * @package System\Orm
		*/

		protected function _dataOneToMany($field, $collection){
			/** @var $line \System\Orm\Entity\Entity */
			foreach($collection as $line){
				$currentEntity = $field->foreign->entity();
				$currentField  = $field->foreign->field();
				$referenceEntity = $field->foreign->referenceEntity();

				$where = $currentEntity.'.'.$currentField.' = '.$line->get($line->primary());
				$data = self::Entity()->$referenceEntity()->find()->where($where)->fetch();
				$line->set($field->name, $data);
			}

			return $collection;
		}

		/**
		 * If the entity has at least many to many relation, we had a collection to the right field
		 * @access public
		 * @param $field \System\Orm\Entity\Field
		 * @param $collection \System\Collection\Collection
		 * @return \System\Collection\Collection
		 * @since 3.0
		 * @package System\Orm
		*/

		protected function _dataManyToMany($field, $collection){
			/** we built the relation table name : $table */
			$currentEntity = $field->foreign->entity();
			$currentField  = $field->foreign->field();
			$referenceEntity  = $field->foreign->referenceEntity();
			$referenceField  = $field->foreign->referenceField();

			$current   = strtolower($currentEntity.$currentField);
			$reference = strtolower($referenceEntity.$referenceField);
			$table = array($current, $reference);
			sort($table, SORT_STRING);
			$table = ucfirst($table[0].$table[1]);

			/** @var $entity \System\Orm\Entity\Entity */
			$entity = self::Entity()->$table();

			/** @var $line \System\Orm\Entity\Entity */
			foreach($collection as $line){
				/** we get all the lines from the relation table */
				$where = $currentEntity.'.'.$currentField.' = '.$line->get($line->primary());
				$data = $entity->find()->where($where)->fetch();

				$result = new Collection();

				/** @var $lineRelation \System\Orm\Entity\Entity */
				foreach($data as $lineRelation){
					/** we get the column needed for example : student_id (one to one relation) in the relation table */
					$fieldRelation = $this->_getTableName($field->foreign->referenceEntity())->name().'_'.$field->foreign->referenceField();
					$result->add($lineRelation->get($fieldRelation));
				}

				$line->set($field->name, $result);
			}

			return $collection;
		}

		/**
		 * When you use fetch, it generates the SELECT .....
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Orm
		*/

		protected function _getSelect(){
			$fields = $this->_entity->fields();

			switch($this->_type){
				case self::QUERY_SELECT :
					$this->_query .= 'SELECT ';

					$nFields = count($fields);
					$i = 0;

					//We add all fields to the select
					foreach($fields as $value){
						//We mustn't add fields which don't exist in the SQL table
						if($value->foreign == null || in_array($value->foreign->type(), array(ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE))){
							$this->_query.= $this->_entity->name().'.'.$value->name. ' AS '.$this->_entity->name().'_'.$value->name;

							if($i < $nFields - 1){
								$this->_query .= ', ';
							}
						}

						$i++;
					}

					$this->_query = preg_replace('#^(.*)(, )$#isU', '$1', $this->_query);

					//if some fields have a relation one to one or many to one, we had a join
					foreach($fields as $value){
						if($value->foreign != null && in_array($value->foreign->type(), array(ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE))){
							$this->_query .= ', ';

							$class = $this->_getTableName($value->foreign->referenceEntity());

							$fieldsRelation = $class->fields();
							$nFieldsRelation = count($fieldsRelation);
							$i = 0;

							foreach($fieldsRelation as $relation){
								if($relation->foreign == null || in_array($relation->foreign->type(), array(ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE))){
									$this->_query.= $class->name().'.'.$relation->name. ' AS '.$class->name().'_'.$relation->name;

									if($i < $nFieldsRelation - 1){
										$this->_query .= ', ';
									}
								}

								$i++;
							}

							$this->_query = preg_replace('#^(.*)(, )$#isU', '$1', $this->_query);
						}
					}
				break;

				case self::QUERY_DISTINCT :
					$primaryColumn = $this->_entity->name().'.'.$this->_entity->fields()[$this->_entity->primary()]->name;

					if(preg_match('('.$primaryColumn.')', $this->_distinct))
						$this->_query .= 'SELECT DISTINCT '.$this->_distinct. ' ';
					else
						$this->_query .= 'SELECT DISTINCT '.$this->_distinct. ', '.$primaryColumn.' ';
				break;
			}

			$this->_query .= ' FROM '.$this->_entity->name();

			/** If there are relations like one to one, many_to_one or many to many */
			foreach($fields as $value){
				if($value->foreign != null &&  in_array($value->foreign->type(), array(ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE))){
					$entity = $value->foreign->entity();
					$field  = $value->foreign->field();
					$referenceEntity = $value->foreign->referenceEntity();
					$referenceField  = $value->foreign->referenceField();
					$this->join(self::JOIN_LEFT, $referenceEntity, $entity.'.'.$field.' = '.$referenceEntity.'.'.$referenceField);
				}
				else if($value->foreign != null && in_array($value->foreign->type(), array(ForeignKey::MANY_TO_MANY))){
					//We add here two join (relation table and table linked)
					$currentEntity    = $value->foreign->entity();
					$currentField     = $value->foreign->field();
					$referenceEntity  = $value->foreign->referenceEntity();
					$referenceField   = $value->foreign->referenceField();

					$current   = strtolower($currentEntity.$currentField);
					$reference = strtolower($referenceEntity.$referenceField);
					$table = array($current, $reference);
					sort($table, SORT_STRING);
					$table = ucfirst($table[0].$table[1]);

					$this->join(self::JOIN_LEFT, $table, $table.'.'.$this->_entity->name().'_'.$currentField.' = '.$this->_entity->name().'.'.$currentField);
					$this->join(self::JOIN_LEFT, $value->foreign->referenceEntity(), $table.'.'.$referenceEntity.'_'.$referenceField.' = '.$referenceEntity.'.'.$referenceField);
				}
			}
		}

		/**
		 * add join
		 * @access protected
		 * @param $entity string
		 * @throws MissingEntityException
		 * @return \System\Orm\Entity\Entity
		 * @since 3.0
		 * @package System\Orm
		*/

		protected function _getTableName($entity = ''){
			/** @var $class \System\Orm\Entity\Entity */

			$className = '\Orm\Entity\\'.$entity;

			if(class_exists($className)){
				return  self::Entity()->$entity();
			}
			else{
				throw new MissingEntityException('The entity '.$entity.' does not exist');
			}
		}

		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Orm
		*/

		public function __destruct(){
		}
	}