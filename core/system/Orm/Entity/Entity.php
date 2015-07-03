<?php
	/*\
 	 | ------------------------------------------------------
	 | @file : Entity.php
	 | @author : fab@c++
	 | @description : sql table representation
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Entity;

	use System\Exception\MissingEntityException;
	use System\General\facades;
	use System\Orm\Builder;
	use System\Sql\Sql;

	abstract class Entity {
		use facades;

		/**
		 * @var string
		*/

		protected $_name = '';

		/**
		 * @var \System\Orm\Entity\Field[]
		*/

		protected $_fields = array();

		/**
		 * @var string
		*/

		protected $_primary = '';

		/**
		 * token, to have unique cache file
		 * @var string
		*/

		protected $_token = '';

		/**
		 * Constructor
		 * @access public
		 * @throws MissingEntityException
		 * @return \System\Orm\Entity\Entity
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		final public function __construct() {
			$this->tableDefinition();
			$this->_getPrimary();
			$this->_token = rand(0,100);

			if($this->_primary == '')
				throw new MissingEntityException('The entity '.$this->_name.' does not have any primary key');

			return $this;
		}

		/**
		 * Creation of the table
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function tableDefinition(){
		}

		/**
		 * Get primary key name
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		protected function _getPrimary(){
			foreach($this->_fields as $key => $field){
				if($field->primary == true){
					$this->_primary = $key;
					break;
				}
			}
		}

		/**
		 * set or get name
		 * @access public
		 * @param $name string
		 * @return mixed void,string
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function name($name = '') {
			if($name != '')
				$this->_name = $name;
			else
				return $this->_name;

			return null;
		}

		/**
		 * get fields
		 * @access public
		 * @return \System\Orm\Entity\Field[]
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function fields() {
			return $this->_fields;
		}

		/**
		 * get primary
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function primary() {
			return $this->_primary;
		}

		/**
		 * add a field
		 * @access public
		 * @param $name string
		 * @return \System\Orm\Entity\Field
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function field($name) {
			$this->_fields[''.$name.''] = new Field($name, $this->_name);
			return $this->_fields[''.$name.''];
		}

		/**
		 * permit to set a column value
		 * @access public
		 * @param $key string
		 * @param $value integer,boolean,string,\System\Orm\Entity\Type\Type
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function __set($key, $value) {
			$this->set($key, $value);
		}

		/**
		 * permit to set a column value
		 * @access public
		 * @param $key string
		 * @throws MissingEntityException
		 * @param $value mixed integer,boolean,string,\System\Orm\Entity\Type\Type
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function set($key, $value) {
			if(array_key_exists($key, $this->_fields)){
				if(gettype($this->_fields[''.$key.'']) == 'object')
					$this->_fields[''.$key.'']->value = $value;
				else
					$this->_fields[''.$key.''] = $value;
			}
			else{
				throw new MissingEntityException('The field "'.$key.'" doesn\'t exist in '.$this->_name);
			}
		}

		/**
		 * permit to set a column
		 * @access public
		 * @param string $key
		 * @param $value \System\Orm\Entity\Field
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function setField($key, $value) {
			if(array_key_exists($key, $this->_fields)){
				$this->_fields[''.$key.''] = $value;
			}
		}

		/**
		 * return a field value
		 * @access public
		 * @param $key string
		 * @return mixed integer,boolean,string,\System\Orm\Entity\Type\Type,\System\Collection\Collection
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function __get($key) {
			return $this->get($key);
		}

		/**
		 * return a field value
		 * @access public
		 * @param $key string
		 * @throws MissingEntityException
		 * @return mixed integer,boolean,string,\System\Orm\Entity\Type\Type
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function get($key) {
			if(array_key_exists($key, $this->_fields)){
				if(gettype($this->_fields[''.$key.'']) == 'object')
					return $this->_fields[''.$key.'']->value;
				else
					return $this->_fields[''.$key.''];
			}
			else{
				throw new MissingEntityException('The field "'.$key.'" doesn\'t exist in '.$this->_name);
			}
		}

		/**
		 * isset
		 * @access public
		 * @param $key string
		 * @return boolean
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function __isset($key) {
			return isset($this->_fields[''.$key.'']);
		}

		/**
		 * return a field
		 * @access public
		 * @param $key string
		 * @return \System\Orm\Entity\Field
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function getField($key) {
			if(array_key_exists($key, $this->_fields)){
				return $this->_fields[''.$key.''];
			}

			return null;
		}

		/**
		 * find a list of model
		 * @access public
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public static function find(){
			/** @var \System\Orm\Entity\Entity $obj */
			$obj = new static();
			$builder = new Builder($obj);
			return $builder->find();
		}

		/**
		 * find raw no (sql completion)
		 * @access public
		 * @param $query string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function raw($query){
			/** @var \System\Orm\Entity\Entity $obj */
			$obj = new static();
			$builder = new Builder($obj);
			return $builder->findRaw($query);
		}

		/**
		 * select distinct
		 * @access public
		 * @param $distinct string
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function distinct($distinct){
			/** @var \System\Orm\Entity\Entity $obj */
			$obj = new static();
			$builder = new Builder($obj);
			return $builder->findDistinct($distinct);
		}

		/**
		 * insert a new line in the database
		 * @access public
		 * @throws
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function insert() {
			$sql = self::Sql();
			$queryFields = '';
			$queryValues = '';
			$manyToMany  = null;
			$transaction = self::Database()->db()->inTransaction();

			if(!$transaction)
				self::Database()->db()->beginTransaction();

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertOneToMany = array();
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateOneToMany = array();

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertManyToMany = array();
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateManyToMany = array();



			/** @var $field \System\Orm\Entity\Field */
			foreach($this->_fields as $field){
				if($field->primary == false){
					if($field->foreign != null){
						switch($field->foreign->type()){
							case ForeignKey::ONE_TO_ONE :
								if(gettype($field->value) != 'object'){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an entity');
								}
								if(!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))){
									throw new MissingEntityException('The foreign key "'.$field->name.'" in "'.$this->_name.'" must be an Entity object');
								}

								if($field->value == null){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
								}
								else{
									$field->value->insert();
									$queryFields .= $field->name.', ';
									$queryValues .= ':'.$field->name.', ';
									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
								}
							break;

							case ForeignKey::MANY_TO_ONE :
								if(gettype($field->value) != 'object'){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an Entity');
								}
								if(!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))){
									throw new MissingEntityException('The foreign key "'.$field->name.'" in "'.$this->_name.'" must be an entity object');
								}

								if($field->value == null){
									if($field->beNull == false){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
									}
									else{
										$sql->vars($field->name, null);
										$queryFields .= $field->name.', ';
										$queryValues .= ':'.$field->name.', ';
									}
								}
								else{
									if($field->value->get($field->value->primary()) == null){
										$field->value->insert();
									}

									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
									$queryFields .= $field->name.', ';
									$queryValues .= ':'.$field->name.', ';
								}
							break;

							case ForeignKey::ONE_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if($field->value != null){
									if(gettype($field->value) != 'object'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}
									if(get_class($field->value) != 'System\Collection\Collection'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}

									/** @var $entity \System\Orm\Entity\Entity */
									foreach($field->value as $entity){
										if($entity != null){
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if($entity->get($entity->primary()) == null){
												array_push($fieldsInsertOneToMany, $entity);
											}
											/** the field is already in database, so after the insert of the entity, we will do an update for it */
											else if($entity->get($entity->primary()) != null){
												array_push($fieldsUpdateOneToMany, $entity);
											}
										}
										else{
											if($field->foreign->belong() == ForeignKey::COMPOSITION){
												throw new MissingEntityException('The Collection field "'.$field->name.'" in "'.$this->_name.'" can\'t have a null item because the relation is a composition');
											}
										}
									}
								}
								else if($field->beNull == false){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
								}
							break;

							case ForeignKey::MANY_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if($field->value != null){
									if(gettype($field->value) != 'object'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}
									if(get_class($field->value) != 'System\Collection\Collection'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}

									$manyToMany = $field;

									/** @var $entity \System\Orm\Entity\Entity */
									foreach($field->value as $entity){
										if($entity != null){
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if($entity->get($entity->primary()) == null){
												array_push($fieldsInsertManyToMany, $entity);
											}
											else{
												array_push($fieldsUpdateManyToMany, $entity);
											}
										}
										else{
											throw new MissingEntityException('The entity "'.get_class($entity).'" in "'.$this->_name.'" can\'t be null');
										}
									}
								}
								else if($field->beNull == false){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
								}
							break;
						}
					}
					else{
						if($field->value == null && $field->beNull == false){
							throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
						}

						if(gettype($field->value) != 'object'){
							if(in_array($field->type, array(Field::INCREMENT, Field::INT, Field::FLOAT))){
								$sql->vars($field->name, array($field->value, sql::PARAM_INT));
							}
							if(in_array($field->type, array(Field::FLOAT))){
								$sql->vars($field->name, array($field->value, sql::PARAM_LOB));
							}
							else if(in_array($field->type, array(Field::CHAR, Field::TEXT, Field::STRING, Field::DATE, Field::DATETIME, Field::TIME, Field::TIMESTAMP))){
								$sql->vars($field->name, array($field->value, sql::PARAM_STR));
							}
							else if(in_array($field->type, array(Field::BOOL))){
								$sql->vars($field->name, array($field->value, sql::PARAM_BOOL));
							}
							else{
								$sql->vars($field->name, $field->value);
							}
						}
						else if(in_array($field->type, array(Field::FILE))){
							$sql->vars($field->name, $field->value->value());
							$field->value->save();
						}

						$queryFields .= $field->name.', ';
						$queryValues .= ':'.$field->name.', ';
					}
				}
			}

			/** Execution of the query */
			$queryFields = preg_replace('#, $#isU', '', $queryFields);
			$queryValues = preg_replace('#, $#isU', '', $queryValues);

			$query = 'INSERT INTO '.$this->_name.'('.$queryFields.') VALUES('.$queryValues.')';

			$sql->query('insert_'.$this->_name.'_'.$this->_token, $query);
			$sql->fetch('insert_'.$this->_name.'_'.$this->_token, Sql::PARAM_FETCHINSERT);

			/** Update primary key */
			$this->_fields[$this->_primary] = self::Database()->db()->lastInsertId();

			/** ######################################################################## */
			/** One to many and many to many need more queries after the principal query */

			/** @var $field \System\Orm\Entity\Field */
			foreach($fieldsInsertOneToMany as $fields){
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach($fields->fields() as $relationFields){
					if($relationFields->foreign != null){
						if($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))){
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->insert();
			}

			foreach($fieldsUpdateOneToMany as $fields){
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach($fields->fields() as $relationFields){
					if($relationFields->foreign != null){
						if($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))){
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->update();
			}

			if($manyToMany != null){
				$currentEntity    = $manyToMany->foreign->entity();
				$currentField     = $manyToMany->foreign->field();
				$referenceEntity  = $manyToMany->foreign->referenceEntity();
				$referenceField   = $manyToMany->foreign->referenceField();

				$current   = strtolower($currentEntity.$currentField);
				$reference = strtolower($referenceEntity.$referenceField);
				$table = array($current, $reference);
				sort($table, SORT_STRING);
				$table = ucfirst($table[0].$table[1]);

				$class = '\Orm\Entity\\'.$manyToMany->foreign->referenceEntity();
				/** @var $referencedEntity \System\Orm\Entity\Entity */
				$referencedEntity = new $class();

				$sql = self::Sql();
				$sql->query('orm-delete-many', 'DELETE FROM '.$table.' WHERE '.$this->name().'_'.$manyToMany->foreign->referenceField().' = '.$this->_fields[$this->_primary]);
				$sql->fetch('orm-delete-many');

				/** @var $fields \System\Orm\Entity\Entity */
				foreach($fieldsInsertManyToMany as $fields){
					$fields->set($manyToMany->foreign->field(), $this);
					$fields->insert();

					$sql->query('orm-insert-many', 'INSERT INTO '.$table.'('.$this->name().'_'.$manyToMany->foreign->referenceField().', '.$referencedEntity->name().'_'.$manyToMany->foreign->field().') VALUES (\''.$this->_fields[$this->_primary].'\', \''.$fields->get($manyToMany->foreign->field()).'\')');
					$sql->fetch('orm-insert-many');
				}

				/** @var $fields \System\Orm\Entity\Entity */
				foreach($fieldsUpdateManyToMany as $fields){
					$sql->query('orm-update-many', 'INSERT INTO '.$table.'('.$this->name().'_'.$manyToMany->foreign->referenceField().', '.$referencedEntity->name().'_'.$manyToMany->foreign->field().') VALUES (\''.$this->_fields[$this->_primary].'\', \''.$fields->get($manyToMany->foreign->field()).'\')');
					$sql->fetch('orm-udpate-many');
				}
			}

			if(!$transaction)
				self::Database()->db()->commit();
		}

		/**
		 * update a line
		 * @access public
		 * @throws MissingEntityException
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function update() {
			if($this->get($this->primary()) == null){
				throw new MissingEntityException('The primary key of the entity "'.get_class($this).'" is null, you can\'t update it');
			}

			$sql = self::Sql();
			$queryFields = '';
			$manyToMany  = null;
			$transaction = self::Database()->db()->inTransaction();

			if(!$transaction)
				self::Database()->db()->beginTransaction();

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertOneToMany = array();
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateOneToMany = array();

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertManyToMany = array();
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateManyToMany = array();

			/** @var $field \System\Orm\Entity\Field */
			foreach($this->_fields as $field){
				if($field->primary == false){
					if($field->foreign != null){
						switch($field->foreign->type()){
							case ForeignKey::ONE_TO_ONE :
								if(gettype($field->value) != 'object'){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an entity');
								}
								if(!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))){
									throw new MissingEntityException('The foreign key "'.$field->name.'" in "'.$this->_name.'" must be an Entity object');
								}

								if($field->value == null){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
								}
								else{
									$field->value->insert();
									$queryFields .= $field->name.' = :'.$field->name.', ';
									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
								}
							break;

							case ForeignKey::MANY_TO_ONE :
								if(gettype($field->value) != 'object'){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an Entity');
								}
								if(!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))){
									throw new MissingEntityException('The foreign key "'.$field->name.'" in "'.$this->_name.'" must be an entity object');
								}

								if($field->value == null){
									if($field->beNull == false){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
									}
									else{
										$sql->vars($field->name, null);
										$queryFields .= $field->name.' = :'.$field->name.', ';
									}
								}
								else{
									if($field->value->get($field->value->primary()) == null){
										$field->value->insert();
									}

									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
									$queryFields .= $field->name.' = :'.$field->name.', ';
								}
							break;

							case ForeignKey::ONE_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if($field->value != null){
									if(gettype($field->value) != 'object'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}
									if(get_class($field->value) != 'System\Collection\Collection'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}

									/** @var $entity \System\Orm\Entity\Entity */
									foreach($field->value as $entity){
										if($entity != null){
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if($entity->get($entity->primary()) == null){
												array_push($fieldsInsertOneToMany, $entity);
											}
											/** the field is already in database, so after the insert of the entity, we will do an update for it */
											else if($entity->get($entity->primary()) != null){
												array_push($fieldsUpdateOneToMany, $entity);
											}
										}
										else{
											if($field->foreign->belong() == ForeignKey::COMPOSITION){
												throw new MissingEntityException('The Collection field "'.$field->name.'" in "'.$this->_name.'" can\'t have a null item because the relation is a composition');
											}
										}
									}
								}
								else if($field->beNull == false){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
								}
								break;

							case ForeignKey::MANY_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if($field->value != null){
									if(gettype($field->value) != 'object'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}
									if(get_class($field->value) != 'System\Collection\Collection'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}

									$manyToMany = $field;

									/** @var $entity \System\Orm\Entity\Entity */
									foreach($field->value as $entity){
										if($entity != null){
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if($entity->get($entity->primary()) == null){
												array_push($fieldsInsertManyToMany, $entity);
											}
											else{

												array_push($fieldsUpdateManyToMany, $entity);
											}
										}
										else{
											throw new MissingEntityException('The entity "'.get_class($entity).'" in "'.$this->_name.'" can\'t be null');
										}
									}
								}
								else if($field->beNull == false){
									throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
								}
								break;
						}
					}
					else{
						if($field->value == null && $field->beNull == false){
							throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" can\'t be null');
						}

						if(gettype($field->value) != 'object'){
							if(in_array($field->type, array(Field::INCREMENT, Field::INT, Field::FLOAT))){
								$sql->vars($field->name, array($field->value, sql::PARAM_INT));
							}
							if(in_array($field->type, array(Field::FLOAT))){
								$sql->vars($field->name, array($field->value, sql::PARAM_LOB));
							}
							else if(in_array($field->type, array(Field::CHAR, Field::TEXT, Field::STRING, Field::DATE, Field::DATETIME, Field::TIME, Field::TIMESTAMP))){
								$sql->vars($field->name, array($field->value, sql::PARAM_STR));
							}
							else if(in_array($field->type, array(Field::BOOL))){
								$sql->vars($field->name, array($field->value, sql::PARAM_BOOL));
							}
							else{
								$sql->vars($field->name, $field->value);
							}
						}
						else if(in_array($field->type, array(Field::FILE))){
							$sql->vars($field->name, $field->value->value());
							$field->value->save();
						}

						$queryFields .= $field->name.' = :'.$field->name.', ';
					}
				}
			}

			$queryFields = preg_replace('#, $#isU', '', $queryFields);

			$query = 'UPDATE '.$this->_name.' SET '.$queryFields.' WHERE '.$this->_fields[$this->_primary]->name.' = '.$this->_fields[$this->_primary]->value;

			$sql->query('update_'.$this->_name.'_'.$this->_token, $query);
			$sql->fetch('update_'.$this->_name.'_'.$this->_token, Sql::PARAM_FETCHUPDATE);

			/** ######################################################################## */
			/** One to many and many to many need more queries after the principal query */

			/** @var $field \System\Orm\Entity\Field */
			foreach($fieldsInsertOneToMany as $fields){
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach($fields->fields() as $relationFields){
					if($relationFields->foreign != null){
						if($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))){
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->insert();
			}

			foreach($fieldsUpdateOneToMany as $fields){
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach($fields->fields() as $relationFields){
					if($relationFields->foreign != null){
						if($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))){
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->update();
			}

			if($manyToMany != null){
				$currentEntity    = $manyToMany->foreign->entity();
				$currentField     = $manyToMany->foreign->field();
				$referenceEntity  = $manyToMany->foreign->referenceEntity();
				$referenceField   = $manyToMany->foreign->referenceField();

				$current   = strtolower($currentEntity.$currentField);
				$reference = strtolower($referenceEntity.$referenceField);
				$table = array($current, $reference);
				sort($table, SORT_STRING);
				$table = ucfirst($table[0].$table[1]);

				$class = '\Orm\Entity\\'.$manyToMany->foreign->referenceEntity();
				/** @var $referencedEntity \System\Orm\Entity\Entity */
				$referencedEntity = new $class();

				$sql = self::Sql();
				$sql->query('orm-delete-many', 'DELETE FROM '.$table.' WHERE '.$this->name().'_'.$manyToMany->foreign->referenceField().' = '.$this->_fields[$this->_primary]);
				$sql->fetch('orm-delete-many');

				/** @var $fields \System\Orm\Entity\Entity */
				foreach($fieldsInsertManyToMany as $fields){
					$fields->set($manyToMany->foreign->field(), $this);
					$fields->insert();

					$sql->query('orm-insert-many', 'INSERT INTO '.$table.'('.$this->name().'_'.$manyToMany->foreign->referenceField().', '.$referencedEntity->name().'_'.$manyToMany->foreign->field().') VALUES (\''.$this->_fields[$this->_primary].'\', \''.$fields->get($manyToMany->foreign->field()).'\')');
					$sql->fetch('orm-insert-many');
				}

				/** @var $fields \System\Orm\Entity\Entity */
				foreach($fieldsUpdateManyToMany as $fields){
					$sql->query('orm-update-many', 'INSERT INTO '.$table.'('.$this->name().'_'.$manyToMany->foreign->referenceField().', '.$referencedEntity->name().'_'.$manyToMany->foreign->field().') VALUES (\''.$this->_fields[$this->_primary].'\', \''.$fields->get($manyToMany->foreign->field()).'\')');
					$sql->fetch('orm-udpate-many');
				}
			}

			if(!$transaction)
				self::Database()->db()->commit();
		}

		/**
		 * delete a line
		 * @access public
		 * @throws MissingEntityException
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		*/

		public function delete() {
			$sql = self::Sql();
			$transaction = self::Database()->db()->inTransaction();

			if(!$transaction)
				self::Database()->db()->beginTransaction();

			foreach($this->_fields as $field){
				if($field->primary == false){
					if($field->foreign != null){
						switch($field->foreign->type()){
							case ForeignKey::ONE_TO_ONE :
								/** if it's a one to one relation, we delete the linked entity */
								if($field->value != null){
									if(gettype($field->value) != 'object'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an Entity');
									}
									if(get_class($field->value) != 'System\Collection\Collection'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an Entity');
									}

									$field->value->delete();
								}
							break;

							case ForeignKey::MANY_TO_ONE :
								if($field->value != null){
									if($field->foreign->belong() == ForeignKey::COMPOSITION){
										if(gettype($field->value) != 'object'){
											throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an Entity');
										}
										if(get_class($field->value) != 'System\Collection\Collection'){
											throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be an Entity');
										}

										$field->value->delete();
									}
								}
							break;

							case ForeignKey::ONE_TO_MANY :
								if($field->value != null){
									if(gettype($field->value) != 'object'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}
									if(get_class($field->value) != 'System\Collection\Collection'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}

									if($field->foreign->belong() == ForeignKey::COMPOSITION){
										/** @var $entity \System\Orm\Entity\Entity */
										foreach($field->value as $entity){
											if($entity != null){
												$field->value->delete();
											}
										}
									}
								}
							break;

							case ForeignKey::MANY_TO_MANY :
								/** Here we must delete from the referenced table and the relation table too */
								if($field->value != null){
									if(gettype($field->value) != 'object'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}
									if(get_class($field->value) != 'System\Collection\Collection'){
										throw new MissingEntityException('The field "'.$field->name.'" in "'.$this->_name.'" must be a Collection');
									}

									if($field->foreign->belong() == ForeignKey::COMPOSITION){
										$currentEntity    = $field->foreign->entity();
										$currentField     = $field->foreign->field();
										$referenceEntity  = $field->foreign->referenceEntity();
										$referenceField   = $field->foreign->referenceField();

										$current   = strtolower($currentEntity.$currentField);
										$reference = strtolower($referenceEntity.$referenceField);
										$table = array($current, $reference);
										sort($table, SORT_STRING);
										$table = ucfirst($table[0].$table[1]);

										$sql = self::Sql();
										$sql->query('orm-delete-many', 'DELETE FROM '.$table.' WHERE '.$this->name().'_'.$field->foreign->referenceField().' = '.$this->_fields[$this->_primary]);
										$sql->fetch('orm-delete-many');

										/** @var $entity \System\Orm\Entity\Entity */
										foreach($field->value as $entity){
											if($entity != null){
												$field->value->delete();
											}
										}
									}
								}
							break;
						}
					}
					else{
						switch($field->type){
							case Field::FILE :
								if($field->unique){
									$field->value->delete();
								}
							break;
						}
					}
				}
			}

			$query = 'DELETE FROM '.$this->_name.' WHERE '.$this->_fields[$this->_primary]->name.' = '.$this->_fields[$this->_primary]->value;

			$sql->query('delete_'.$this->_name.'_'.$this->_token, $query);
			$sql->fetch('delete_'.$this->_name.'_'.$this->_token, Sql::PARAM_FETCHDELETE);

			if(!$transaction)
				self::Database()->db()->commit();
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