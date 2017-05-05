<?php
	/*\
 	 | ------------------------------------------------------
	 | @file : Entity.php
	 | @author : Fabien Beaujean
	 | @description : sql table representation
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/

	namespace System\Orm\Entity;

	use System\Annotation\Annotation;
	use System\Collection\Collection;
	use System\Database\Database;
	use System\Exception\MissingEntityException;
	use System\Exception\MissingFieldException;
	use System\Orm\Builder;
	use System\Orm\Entity\Type\File;
	use System\Orm\Validation\Validation;
	use System\Request\Data;
	use System\Sql\Sql;

	/**
	 * Class Entity
	 * @package System\Orm\Entity
	 */

	abstract class Entity {
		/**
		 * @var string
		 */

		protected $_name = '';

		/**
		 * @var \System\Orm\Entity\Field[]
		 */

		protected $_fields = [];

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
		 * We put errors inside
		 * @var \System\Orm\Validation\Validation
		 */

		protected $validation = null;

		/**
		 * post, put, get, patch data
		 * @var $_data []
		 */

		protected $_data;

		/**
		 * name of the form
		 * @var string
		 */

		protected $_form = '';

		/**
		 * the form is already sent and checked
		 * @var bool
		 */

		protected $_sent = false;

		/**
		 * Constructor
		 * @access public
		 * @throws MissingEntityException
		 * @return \System\Orm\Entity\Entity
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		final public function __construct() {
			$this->_tableDefinition();
			$this->getPrimary();

			$this->_token = rand(0, 10000);
			$this->validation = new Validation($this);

			if ($this->_primary == '') {
				throw new MissingEntityException('The entity ' . get_class($this) . ' does not have any primary key');
			}

			$requestData = Data::instance();

			switch ($requestData->method) {
				case 'get' :
					$this->_data = $requestData->get;
				break;

				case 'post' :
					$this->_data = $requestData->post;
				break;

				case 'put' :
					$this->_data = $requestData->post;
				break;

				case 'patch' :
					$this->_data = $requestData->post;
				break;

				case 'delete' :
					$this->_data = $requestData->post;
				break;
			}

			return $this;
		}

		/**
		 * Creation of the table
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		final protected function _tableDefinition() {
			$annotation = Annotation::getClass($this);

			/** @var \ReflectionClass $fieldClass */
			$fieldClass = new \ReflectionClass('\\System\\Orm\\Entity\\Field');
			/** @var \ReflectionClass $foreignKeyClass */
			$foreignKeyClass = new \ReflectionClass('\\System\\Orm\\Entity\\ForeignKey');
			/** @var \ReflectionClass $builderClass */
			$builderClass = new \ReflectionClass('\\System\\Orm\\Builder');

			foreach ($annotation['class'] as $annotationClass){
				$instance = $annotationClass[0]['instance'];

				switch($annotationClass[0]['annotation']){
					case 'Table':
						$this->name($instance->name);
					break;

					case 'Form':
						$this->form($instance->name);
					break;
				}
			}

			foreach ($annotation['properties'] as $name => $annotationProperties){
				foreach ($annotationProperties as $annotationProperty){
					$instance = $annotationProperty['instance'];

					if($annotationProperty['annotation'] == 'Column'){
						$this->field($name)
							->type($fieldClass->getConstant($instance->type))
							->size($instance->size)
							->beNull($instance->null == 'true' ? true : false)
							->primary($instance->primary == 'true' ? true : false)
							->unique($instance->unique == 'true' ? true : false)
							->precision($instance->precision)
							->defaultValue($instance->default)
							->enum($instance->enum != '' ? explode(',', $instance->enum) : []);

						$fieldType = $fieldClass->getConstant($instance->type);

						if(in_array($fieldType, [Field::DATETIME, Field::DATE, Field::TIMESTAMP])){
							$this->set($name, new \DateTime());
						}
					}
					else{
						$this->field($name)
							->foreign([
								'type'      => $foreignKeyClass->getConstant($instance->type),
								'reference' => explode('.', $instance->to),
								'current'   => explode('.', $instance->from),
								'belong'    => $foreignKeyClass->getConstant($instance->belong),
								'join'      => $builderClass->getConstant($instance->join),
							]);

						$foreignType = $foreignKeyClass->getConstant($instance->type);

						if(in_array($foreignType, [ForeignKey::MANY_TO_MANY, ForeignKey::ONE_TO_MANY])){
							$this->set($name, new Collection());
						}
					}
				}
			}
		}

		/**
		 * Set form name
		 * @access public
		 * @param $form string
		 * @return void
		 * @since 3.0
		 * @package System\Request
		 */

		public function form($form = '') {
			$this->_form = $form;
		}

		/**
		 * Get form name
		 * @access public
		 * @return string
		 * @since 3.0
		 * @package System\Request
		 */

		public function getForm() {
			return $this->_form;
		}

		/**
		 * Get primary key name
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function getPrimary() {
			foreach ($this->_fields as $key => $field) {
				if ($field->primary == true) {
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
			if ($name != '') {
				$this->_name = $name;
			}
			else {
				return $this->_name;
			}

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
			$this->_fields['' . $name . ''] = new Field($name, $this->_name);
			return $this->_fields['' . $name . ''];
		}

		/**
		 * permit to set a column value
		 * @access public
		 * @param $key   string
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
		 * @param $key   string
		 * @throws MissingEntityException
		 * @param $value mixed integer,boolean,string,\System\Orm\Entity\Type\Type
		 * @return void
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function set($key, $value) {
			if (array_key_exists($key, $this->_fields)) {
				if (gettype($this->_fields['' . $key . '']) == 'object') {
					$this->_fields['' . $key . '']->value = $value;
				}
				else {
					$this->_fields['' . $key . ''] = $value;
				}

				$this->$key = $value;
			}
			else {
				throw new MissingEntityException('The field "' . $key . '" doesn\'t exist in ' . $this->_name);
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
			if (array_key_exists($key, $this->_fields)) {
				if (gettype($this->_fields['' . $key . '']) == 'object') {
					return $this->_fields['' . $key . '']->value;
				}
				else {
					return $this->_fields['' . $key . ''];
				}
			}
			else {
				throw new MissingEntityException('The field "' . $key . '" doesn\'t exist in ' . $this->_name);
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
			return isset($this->_fields['' . $key . '']);
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
			if (array_key_exists($key, $this->_fields)) {
				return $this->_fields['' . $key . ''];
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

		public static function find() {
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

		public static function raw($query) {
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

		public static function distinct($distinct) {
			/** @var \System\Orm\Entity\Entity $obj */
			$obj = new static();
			$builder = new Builder($obj);
			return $builder->findDistinct($distinct);
		}

		/**
		 * count number of line
		 * @access public
		 * @return \System\Orm\Builder
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public static function count() {
			/** @var \System\Orm\Entity\Entity $obj */
			$obj = new static();
			$builder = new Builder($obj);
			return $builder->findCount();
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
			$sql = new Sql();
			$queryFields = '';
			$queryValues = '';
			$manyToMany = null;
			$transaction = Database::instance()->db()->inTransaction();

			if (!$transaction) {
				Database::instance()->db()->beginTransaction();
			}

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertOneToMany = [];
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateOneToMany = [];

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertManyToMany = [];
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateManyToMany = [];

			/** @var $field \System\Orm\Entity\Field */
			foreach ($this->_fields as $field) {
				if ($field->primary == false) {
					if ($field->foreign != null) {
						switch ($field->foreign->type()) {
							case ForeignKey::ONE_TO_ONE :
								if (gettype($field->value) != 'object') {
									if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an entity');
									}
								}
								if (!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))) {
									throw new MissingEntityException('The foreign key "' . $field->name . '" in "' . $this->_name . '" must be an Entity object');
								}

								if ($field->value == null) {
									throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
								}
								else {
									$field->value->insert();
									$queryFields .= $field->name . ', ';
									$queryValues .= ':' . $field->name . ', ';
									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
								}
							break;

							case ForeignKey::MANY_TO_ONE :
								if (gettype($field->value) != 'object') {
									if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an Entity');
									}
								}
								if (!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))) {
									//throw new MissingEntityException('The foreign key "'.$field->name.'" in "'.$this->_name.'" must be an entity object');
								}

								if ($field->value == null) {
									if ($field->beNull == false) {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
									}
									else {
										$sql->vars($field->name, null);
										$queryFields .= $field->name . ', ';
										$queryValues .= ':' . $field->name . ', ';
									}
								}
								else {
									if ($field->value->get($field->value->primary()) == null) {
										$field->value->insert();
									}

									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
									$queryFields .= $field->name . ', ';
									$queryValues .= ':' . $field->name . ', ';
								}
							break;

							case ForeignKey::ONE_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if ($field->value != null) {
									if (gettype($field->value) != 'object') {
										if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
											throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
										}
									}
									if (get_class($field->value) != 'System\Collection\Collection') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}

									/** @var $entity \System\Orm\Entity\Entity */
									foreach ($field->value as $entity) {
										if ($entity != null) {
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if ($entity->get($entity->primary()) == null) {
												array_push($fieldsInsertOneToMany, $entity);
											}
											/** the field is already in database, so after the insert of the entity, we will do an update for it */
											else if ($entity->get($entity->primary()) != null) {
												array_push($fieldsUpdateOneToMany, $entity);
											}
										}
										else {
											if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
												throw new MissingEntityException('The Collection field "' . $field->name . '" in "' . $this->_name . '" can\'t have a null item because the relation is a composition');
											}
										}
									}
								}
								else if ($field->beNull == false) {
									throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
								}
							break;

							case ForeignKey::MANY_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if ($field->value != null) {
									if (gettype($field->value) != 'object') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}
									if (get_class($field->value) != 'System\Collection\Collection') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}

									$manyToMany = $field;

									/** @var $entity \System\Orm\Entity\Entity */
									foreach ($field->value as $entity) {
										if ($entity != null) {
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if ($entity->get($entity->primary()) == null) {
												array_push($fieldsInsertManyToMany, $entity);
											}
											else {
												array_push($fieldsUpdateManyToMany, $entity);
											}
										}
										else {
											throw new MissingEntityException('The entity "' . get_class($entity) . '" in "' . $this->_name . '" can\'t be null');
										}
									}
								}
								else if ($field->beNull == false) {
									throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
								}
							break;
						}
					}
					else {
						if ($field->value == null && $field->beNull == false && $field->default == '') {
							throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
						}

						if ($field->value == null) {
							$field->value = $field->default;
						}

						if (gettype($field->value) != 'object') {
							if (in_array($field->type, [Field::INCREMENT, Field::INT, Field::FLOAT])) {
								$sql->vars($field->name, [$field->value, Sql::PARAM_INT]);
							}
							else if (in_array($field->type, [Field::CHAR, Field::TEXT, Field::STRING, Field::DATE, Field::DATETIME, Field::TIMESTAMP])) {
								$sql->vars($field->name, [$field->value, Sql::PARAM_STR]);
							}
							else if (in_array($field->type, [Field::BOOL])) {
								$sql->vars($field->name, [$field->value, Sql::PARAM_BOOL]);
							}
							else {
								$sql->vars($field->name, $field->value);
							}
						}
						elseif (in_array($field->type, [Field::DATETIME, Field::TIMESTAMP])){
							$sql->vars($field->name, [$field->value->format("Y-m-d H:i:s"), Sql::PARAM_STR]);
						}
						elseif (in_array($field->type, [Field::DATE])){
							$sql->vars($field->name, [$field->value->format("Y-m-d"), Sql::PARAM_STR]);
						}
						else if (in_array($field->type, [Field::FILE])) {
							$sql->vars($field->name, $field->value->value());
							$field->value->save();
						}

						$queryFields .= $field->name . ', ';
						$queryValues .= ':' . $field->name . ', ';
					}
				}
			}

			/** Execution of the query */
			$queryFields = substr(trim($queryFields, ','), 0, strlen($queryFields) - 2);
			$queryValues = substr(trim($queryValues, ','), 0, strlen($queryValues) - 2);

			$query = 'INSERT INTO ' . $this->_name . '(' . $queryFields . ') VALUES(' . $queryValues . ')';

			$sql->query('insert_' . $this->_name . '_' . $this->_token, $query);
			$sql->fetch('insert_' . $this->_name . '_' . $this->_token, Sql::PARAM_FETCHINSERT);

			/** Update primary key */
			$this->_fields[$this->_primary]->value = Database::instance()->db()->lastInsertId();

			/** ######################################################################## */
			/** One to many and many to many need more queries after the principal query */

			/** @var $field \System\Orm\Entity\Field */
			foreach ($fieldsInsertOneToMany as $fields) {
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach ($fields->fields() as $relationFields) {
					if ($relationFields->foreign != null) {
						if ($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))) {
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->insert();
			}

			foreach ($fieldsUpdateOneToMany as $fields) {
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach ($fields->fields() as $relationFields) {
					if ($relationFields->foreign != null) {
						if ($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))) {
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->update();
			}

			if ($manyToMany != null) {
				$currentEntity = $manyToMany->foreign->entity();
				$currentField = $manyToMany->foreign->field();
				$referenceEntity = $manyToMany->foreign->referenceEntity();
				$referenceField = $manyToMany->foreign->referenceField();

				$current = strtolower($currentEntity . $currentField);
				$reference = strtolower($referenceEntity . $referenceField);
				$table = [$current, $reference];
				sort($table, SORT_STRING);
				$table = ucfirst($table[0] . $table[1]);

				$class = '\Orm\Entity\\' . $manyToMany->foreign->referenceEntity();
				/** @var $referencedEntity \System\Orm\Entity\Entity */
				$referencedEntity = new $class();

				$sql = new Sql();
				$sql->query('orm-delete-many', 'DELETE FROM ' . $table . ' WHERE ' . $this->name() . '_' . $manyToMany->foreign->referenceField() . ' = ' . $this->_fields[$this->_primary]->value);
				$sql->fetch('orm-delete-many');

				/** @var $fields \System\Orm\Entity\Entity */
				foreach ($fieldsInsertManyToMany as $fields) {
					$fields->set($manyToMany->foreign->field(), $this);
					$fields->insert();

					$sql->query('orm-insert-many', 'INSERT INTO ' . $table . '(' . $this->name() . '_' . $manyToMany->foreign->referenceField() . ', ' . $referencedEntity->name() . '_' . $manyToMany->foreign->field() . ') VALUES (\'' . $this->_fields[$this->_primary]->value . '\', \'' . $fields->get($manyToMany->foreign->field()) . '\')');
					$sql->fetch('orm-insert-many');
				}

				/** @var $fields \System\Orm\Entity\Entity */
				foreach ($fieldsUpdateManyToMany as $fields) {
					$sql->query('orm-update-many',
						'INSERT INTO ' .
						$table . '(' .
						$this->name() . '_' .
						$manyToMany->foreign->referenceField() . ', ' .
						$referencedEntity->name() . '_' .
						$manyToMany->foreign->field() . ') VALUES (\'' .
						$this->_fields[$this->_primary]->value . '\', \'' .
						$fields->get($manyToMany->foreign->field()) . '\')');
					$sql->fetch('orm-update-many');
				}
			}

			if (!$transaction) {
				Database::instance()->db()->commit();
			}
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
			if ($this->get($this->primary()) == null) {
				throw new MissingEntityException('The primary key of the entity "' . get_class($this) . '" is null, you can\'t update it');
			}

			$sql = new Sql();
			$queryFields = '';
			$manyToMany = null;
			$transaction = Database::instance()->db()->inTransaction();

			if (!$transaction) {
				Database::instance()->db()->beginTransaction();
			}

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertOneToMany = [];
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateOneToMany = [];

			/** @var $fieldsInsertOneToMany \System\Orm\Entity\Entity[] */
			$fieldsInsertManyToMany = [];
			/** @var $fieldsUpdateOneToMany \System\Orm\Entity\Entity[] */
			$fieldsUpdateManyToMany = [];

			/** @var $field \System\Orm\Entity\Field */
			foreach ($this->_fields as $field) {
				if ($field->primary == false) {
					if ($field->foreign != null) {
						switch ($field->foreign->type()) {
							case ForeignKey::ONE_TO_ONE :
								if (gettype($field->value) != 'object') {
									if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an entity');
									}
								}
								if (!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))) {
									throw new MissingEntityException('The foreign key "' . $field->name . '" in "' . $this->_name . '" must be an Entity object');
								}

								if ($field->value == null) {
									throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
								}
								else {
									$field->value->insert();
									$queryFields .= $field->name . ' = :' . $field->name . ', ';
									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
								}
							break;

							case ForeignKey::MANY_TO_ONE :
								if (gettype($field->value) != 'object') {
									if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an Entity');
									}
								}
								if (!preg_match('#Orm\\\Entity\\\#isU', get_class($field->value))) {
									throw new MissingEntityException('The foreign key "' . $field->name . '" in "' . $this->_name . '" must be an entity object');
								}

								if ($field->value == null) {
									if ($field->beNull == false) {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
									}
									else {
										$sql->vars($field->name, null);
										$queryFields .= $field->name . ' = :' . $field->name . ', ';
									}
								}
								else {
									if ($field->value->get($field->value->primary()) == null) {
										$field->value->insert();
									}

									$sql->vars($field->name, $field->value->get($field->foreign->referenceField()));
									$queryFields .= $field->name . ' = :' . $field->name . ', ';
								}
							break;

							case ForeignKey::ONE_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if ($field->value != null) {
									if (gettype($field->value) != 'object') {
										if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
											throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
										}
									}
									if (get_class($field->value) != 'System\Collection\Collection') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}

									/** @var $entity \System\Orm\Entity\Entity */
									foreach ($field->value as $entity) {
										if ($entity != null) {
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if ($entity->get($entity->primary()) == null) {
												array_push($fieldsInsertOneToMany, $entity);
											}
											/** the field is already in database, so after the insert of the entity, we will do an update for it */
											else if ($entity->get($entity->primary()) != null) {
												array_push($fieldsUpdateOneToMany, $entity);
											}
										}
										else {
											if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
												throw new MissingEntityException('The Collection field "' . $field->name . '" in "' . $this->_name . '" can\'t have a null item because the relation is a composition');
											}
										}
									}
								}
								else if ($field->beNull == false) {
									throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
								}
							break;

							case ForeignKey::MANY_TO_MANY :
								/** If we insert a new line and the are already sub-objects, we have to insert or update them */
								if ($field->value != null) {
									if (gettype($field->value) != 'object') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}
									if (get_class($field->value) != 'System\Collection\Collection') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}

									$manyToMany = $field;

									/** @var $entity \System\Orm\Entity\Entity */
									foreach ($field->value as $entity) {
										if ($entity != null) {
											/** the field is not in database yet, so after the insert of the entity, we will do an insert for it */
											if ($entity->get($entity->primary()) == null) {
												array_push($fieldsInsertManyToMany, $entity);
											}
											else {

												array_push($fieldsUpdateManyToMany, $entity);
											}
										}
										else {
											throw new MissingEntityException('The entity "' . get_class($entity) . '" in "' . $this->_name . '" can\'t be null');
										}
									}
								}
								else if ($field->beNull == false) {
									throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
								}
							break;
						}
					}
					else {
						if ($field->value == null && $field->beNull == false) {
							throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" can\'t be null');
						}

                        if ($field->value == null) {
                            $field->value = $field->default;
                        }

						if (gettype($field->value) != 'object') {
							if (in_array($field->type, [Field::INCREMENT, Field::INT, Field::FLOAT])) {
								$sql->vars($field->name, [$field->value, Sql::PARAM_INT]);
							}
							else if (in_array($field->type, [Field::CHAR, Field::TEXT, Field::STRING, Field::DATE, Field::DATETIME, Field::TIMESTAMP])) {
								$sql->vars($field->name, [$field->value, Sql::PARAM_STR]);
							}
							else if (in_array($field->type, [Field::BOOL])) {
								$sql->vars($field->name, [$field->value, Sql::PARAM_BOOL]);
							}
							else {
								$sql->vars($field->name, $field->value);
							}
						}
						elseif (in_array($field->type, [Field::DATETIME, Field::TIMESTAMP])){
							$sql->vars($field->name, [$field->value->format("Y-m-d H:i:s"), Sql::PARAM_STR]);
						}
						elseif (in_array($field->type, [Field::DATE])){
							$sql->vars($field->name, [$field->value->format("Y-m-d"), Sql::PARAM_STR]);
						}
						else if (in_array($field->type, [Field::FILE])) {
							$sql->vars($field->name, $field->value->value());
							$field->value->save();
						}

						$queryFields .= $field->name . ' = :' . $field->name . ', ';
					}
				}
			}

			$queryFields = substr(trim($queryFields, ','), 0, strlen($queryFields) - 2);

			$query = 'UPDATE ' . $this->_name . ' SET ' . $queryFields . ' WHERE ' . $this->_fields[$this->_primary]->name . ' = ' . $this->_fields[$this->_primary]->value;

			$sql->query('update_' . $this->_name . '_' . $this->_token, $query);
			$sql->fetch('update_' . $this->_name . '_' . $this->_token, Sql::PARAM_FETCHUPDATE);

			/** ######################################################################## */
			/** One to many and many to many need more queries after the principal query */

			/** @var $field \System\Orm\Entity\Field */
			foreach ($fieldsInsertOneToMany as $fields) {
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach ($fields->fields() as $relationFields) {
					if ($relationFields->foreign != null) {
						if ($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))) {
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->insert();
			}

			foreach ($fieldsUpdateOneToMany as $fields) {
				$relation = '';

				/** @var $relationFields \System\Orm\Entity\Field */
				foreach ($fields->fields() as $relationFields) {
					if ($relationFields->foreign != null) {
						if ($relationFields->foreign->referenceEntity() == ucfirst(strtolower($this->_name))) {
							$relation = $relationFields->foreign->field();
						}
					}
				}

				$fields->set($relation, $this);
				$fields->update();
			}

			if ($manyToMany != null) {
				$currentEntity = $manyToMany->foreign->entity();
				$currentField = $manyToMany->foreign->field();
				$referenceEntity = $manyToMany->foreign->referenceEntity();
				$referenceField = $manyToMany->foreign->referenceField();

				$current = strtolower($currentEntity . $currentField);
				$reference = strtolower($referenceEntity . $referenceField);
				$table = [$current, $reference];
				sort($table, SORT_STRING);
				$table = ucfirst($table[0] . $table[1]);

				$class = '\Orm\Entity\\' . $manyToMany->foreign->referenceEntity();
				/** @var $referencedEntity \System\Orm\Entity\Entity */
				$referencedEntity = new $class();

				$sql = new Sql();
				$sql->query('orm-delete-many', 'DELETE FROM ' . $table . ' WHERE ' . $this->name() . '_' . $manyToMany->foreign->referenceField() . ' = ' . $this->_fields[$this->_primary]->value);
				$sql->fetch('orm-delete-many');

				/** @var $fields \System\Orm\Entity\Entity */
				foreach ($fieldsInsertManyToMany as $fields) {
					$fields->set($manyToMany->foreign->field(), $this);
					$fields->insert();

					$sql->query('orm-insert-many', 'INSERT INTO ' . $table . '(' . $this->name() . '_' . $manyToMany->foreign->referenceField() . ', ' . $referencedEntity->name() . '_' . $manyToMany->foreign->field() . ') VALUES (\'' . $this->_fields[$this->_primary]->value . '\', \'' . $fields->get($manyToMany->foreign->field()) . '\')');
					$sql->fetch('orm-insert-many');
				}

				/** @var $fields \System\Orm\Entity\Entity */
				foreach ($fieldsUpdateManyToMany as $fields) {
					$sql->query('orm-update-many', 'INSERT INTO ' . $table . '(' . $this->name() . '_' . $manyToMany->foreign->referenceField() . ', ' . $referencedEntity->name() . '_' . $manyToMany->foreign->field() . ') VALUES (\'' . $this->_fields[$this->_primary]->value . '\', \'' . $fields->get($manyToMany->foreign->field()) . '\')');
					$sql->fetch('orm-update-many');
				}
			}

			if (!$transaction) {
				Database::instance()->db()->commit();
			}
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
			$sql = new Sql();
			$transaction = Database::instance()->db()->inTransaction();

			if (!$transaction) {
				Database::instance()->db()->beginTransaction();
			}

			foreach ($this->_fields as $field) {
				if ($field->primary == false) {
					if ($field->foreign != null) {
						switch ($field->foreign->type()) {
							case ForeignKey::ONE_TO_ONE :
								/** if it's a one to one relation, we delete the linked entity */
								if ($field->value != null) {
									if (gettype($field->value) != 'object') {
										if ($field->foreign->type() == Builder::JOIN_INNER) {
											throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an Entity');
										}
									}
									if (get_class($field->value) != 'System\Collection\Collection') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an Entity');
									}

									$field->value->delete();
								}
							break;

							case ForeignKey::MANY_TO_ONE :
								if ($field->value != null) {
									if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
										if (gettype($field->value) != 'object') {
											throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an Entity');
										}
										if (get_class($field->value) != 'System\Collection\Collection') {
											throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be an Entity');
										}

										$field->value->delete();
									}
								}
							break;

							case ForeignKey::ONE_TO_MANY :
								if ($field->value != null) {
									if (gettype($field->value) != 'object') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}
									if (get_class($field->value) != 'System\Collection\Collection') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}

									if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
										/** @var $entity \System\Orm\Entity\Entity */
										foreach ($field->value as $entity) {
											if ($entity != null) {
												$field->value->delete();
											}
										}
									}
								}
							break;

							case ForeignKey::MANY_TO_MANY :
								/** Here we must delete from the referenced table and the relation table too */
								if ($field->value != null) {
									if (gettype($field->value) != 'object') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}
									if (get_class($field->value) != 'System\Collection\Collection') {
										throw new MissingEntityException('The field "' . $field->name . '" in "' . $this->_name . '" must be a Collection');
									}

									if ($field->foreign->belong() == ForeignKey::COMPOSITION) {
										$currentEntity = $field->foreign->entity();
										$currentField = $field->foreign->field();
										$referenceEntity = $field->foreign->referenceEntity();
										$referenceField = $field->foreign->referenceField();

										$current = strtolower($currentEntity . $currentField);
										$reference = strtolower($referenceEntity . $referenceField);
										$table = [$current, $reference];
										sort($table, SORT_STRING);
										$table = ucfirst($table[0] . $table[1]);

										$sql = new Sql();
										$sql->query('orm-delete-many', 'DELETE FROM ' . $table . ' WHERE ' . $this->name() . '_' . $field->foreign->referenceField() . ' = ' . $this->_fields[$this->_primary]);
										$sql->fetch('orm-delete-many');

										/** @var $entity \System\Orm\Entity\Entity */
										foreach ($field->value as $entity) {
											if ($entity != null) {
												$field->value->delete();
											}
										}
									}
								}
							break;
						}
					}
					else {
						switch ($field->type) {
							case Field::FILE :
								if ($field->unique) {
									$field->value->delete();
								}
							break;
						}
					}
				}
			}

			$query = 'DELETE FROM ' . $this->_name . ' WHERE ' . $this->_fields[$this->_primary]->name . ' = ' . $this->_fields[$this->_primary]->value;

			$sql->query('delete_' . $this->_name . '_' . $this->_token, $query);
			$sql->fetch('delete_' . $this->_name . '_' . $this->_token, Sql::PARAM_FETCHDELETE);

			if (!$transaction) {
				Database::instance()->db()->commit();
			}
		}

		/**
		 * We can check the validity of a GET or POST request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		 */

		public function beforeInsert() {
		}

		/**
		 * We can check the validity of a PUT request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		 */

		public function beforeUpdate() {
		}

		/**
		 * We can check the validity of a PATCH request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		 */

		public function beforePatch() {
		}

		/**
		 * We can check the validity of a DELETE request thanks to this method that you can override
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		 */

		public function beforeDelete() {
		}

		/**
		 * Check
		 * @access public
		 * @return void
		 * @since 3.0
		 * @package System\Request
		 */

		public function check() {
			$this->validation->check();
			$this->_sent = true;
		}

		/**
		 * Is the entity valid ?
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Request
		 */

		public function valid() {
			return $this->validation->valid();
		}

		/**
		 * Is the form sent ?
		 * @access public
		 * @return boolean
		 * @since 3.0
		 * @package System\Request
		 */

		public function sent() {
			return $this->_sent;
		}

		/**
		 * get errors list
		 * @access public
		 * @return string[]
		 * @since 3.0
		 * @package System\Request
		 */

		public function errors() {
			return $this->validation->errors();
		}

		/**
		 * Before validation, we must inserting all the data
		 * @access public
		 * @param $prefix string : If we want to hydrate a sub Entity (from a relation), we need to know the name of the parent
		 * @throws MissingFieldException
		 * @since 3.0
		 * @package System\Request
		 */

		public function hydrate($prefix = '') {
			$table = strtolower($this->_name);

			/** First, we check if the primary key is specified or not */
			/** It's possible to have a sub entity with an existing primary key and to override its field values */

			if (isset($this->_data[$prefix . $this->primary()])) {
				$entityName = '\Orm\Entity\\' . lcfirst($table);
				$field = lcfirst($table) . '.' . $this->primary();

				/** @var \System\Orm\Entity\Entity $entityName */
				$data = $entityName::find()
					->where($field . ' = :id')
					->vars(['id' => $this->_data[$table . '_' . $this->primary()]])
					->fetch()
					->first();

				if ($data != null) {
					$this->_fields = $data->fields();
				}
			}

			foreach ($this->_fields as $field) {
				if ($field->foreign != null) {
					$in = '';
					$inVars = [];
					$entityName = '\Orm\Entity\\' . $field->foreign->referenceEntity();
					$fieldName = $prefix . lcfirst($field->name);
					$fieldFormName = ucfirst($field->foreign->referenceEntity()) . '.' . lcfirst($field->foreign->referenceField());
					$entityJoin = new $entityName();

					switch ($field->foreign->type()) {
						case ForeignKey::ONE_TO_ONE : {
							//If the primary key exists, we get it in the database
							if (isset($this->_data[$fieldName])) {
								if ($this->_data[$fieldName] != '') {
									$builder = new Builder($entityJoin);
									$field->value = $builder->find()
										->where($fieldFormName . ' = :id')
										->vars(['id' => $this->_data[$fieldName]])
										->fetch()
										->first();

									if ($field->value != null) {
										$field->value->hydrate($prefix . lcfirst($field->name) . '_');
									}
								}
							}
							else { //if it doesn't exist, we try to get data from the form and check if it exists input values
								$entity = $field->foreign->referenceEntity();

								/** @var $entity \System\Orm\Entity\Entity */
								$entity = new $entity();
								$canHydrate = true;

								foreach ($entity->fields() as $fieldName) {
									if (empty($this->_data[$prefix . $table . '_' . $entity->name() . '_' . $fieldName->name]) && $fieldName->primary == false && $fieldName->beNull == false) {
										$canHydrate = false;
										//throw new MissingFieldException('The field "' . $prefix . $entity->name() . '_' . $fieldName->name . '" can\'t be hydrated from the form because it miss one or several input');
									}
								}

								if ($canHydrate) {
									$entity->hydrate($prefix . lcfirst($field->name) . '_');
									$field->value = $entity;
								}
							}
						}
						break;

						case ForeignKey::MANY_TO_ONE :
							//If the primary key exists, we get it in the database
							if (isset($this->_data[$fieldName])) {
								if ($this->_data[$fieldName] != '') {
									$builder = new Builder($entityJoin);
									$field->value = $builder->find()
										->where($fieldFormName . ' = :id')
										->vars(['id' => $this->_data[$fieldName]])
										->fetch()
										->first();

									if ($field->value != null) {
										$field->value->hydrate($prefix . lcfirst($field->name) . '_');
									}
								}
							}
							else { //if it doesn't exist, we try to get data from the form and check if it exists input values
								$entity = 'Orm\Entity\\' . $field->foreign->referenceEntity();

								/** @var $entity \System\Orm\Entity\Entity */
								$entity = new $entity();
								$canHydrate = true;

								foreach ($entity->fields() as $fieldName) {
									if (empty($this->_data[$prefix . $table . '_' . $entity->name() . '_' . $fieldName->name]) && $fieldName->primary == false && $fieldName->beNull == false) {
										$canHydrate = false;
										//throw new MissingFieldException('The field "' . $prefix.$entity->name().'_'.$fieldName->name . '" can\'t be hydrated from the form because it miss one or several input');
									}
								}

								if ($canHydrate) {
									$entity->hydrate($prefix . lcfirst($field->name) . '_');
									$field->value = $entity;
								}
							}
						break;

						case ForeignKey::ONE_TO_MANY :
							if (isset($this->_data[$fieldName])) {
								foreach ($this->_data[$fieldName] as $key => $manyJoin) {
									$in .= ' :join' . $key . ',';
									$inVars['join' . $key] = $manyJoin;
								}

								$in = trim($in, ',');

								$builder = new Builder($entityJoin);
								$field->value = $builder->find()
									->where($fieldFormName . ' IN(' . $in . ')')
									->vars($inVars)
									->fetch();
							}
						break;

						case ForeignKey::MANY_TO_MANY :
							if (isset($this->_data[$fieldName])) {
								foreach ($this->_data[$fieldName] as $key => $manyJoin) {
									$in .= ' :join' . $key . ',';
									$inVars['join' . $key] = $manyJoin;
								}

								$in = trim($in, ',');

								$builder = new Builder($entityJoin);
								$field->value = $builder->find()
									->where($fieldFormName . ' IN(' . $in . ')')
									->vars($inVars)
									->fetch();
							}
						break;
					}
				}
				else if (in_array($field->type, [Field::INCREMENT, Field::INT, Field::FLOAT])) {
					if (isset($this->_data[$prefix . $field->name])) {
						$this->set($field->name, $this->_data[$prefix . $field->name]);
					}
				}
				else if (in_array($field->type, [Field::CHAR, Field::TEXT, Field::STRING, Field::DATE, Field::DATETIME, Field::TIMESTAMP])) {
					if (isset($this->_data[$prefix . $field->name])) {
						$this->set($field->name, $this->_data[$prefix . $field->name]);
					}
				}
				else if (in_array($field->type, [Field::BOOL])) {
					if (isset($this->_data[$prefix . $field->name])) {
						$this->set($field->name, true);
					}
				}
				else if (in_array($field->type, [Field::FILE])) {
					$data = Data::instance()->file;

					if (isset($data[$prefix . $field->name])) {
						if (isset($data[$prefix . $field->name]) && $data[$prefix . $field->name]['error'] != 4) {
							$tmp = $data[$prefix . $field->name];
							$file = new File($tmp['name'], file_get_contents($tmp['tmp_name']), $tmp['type']);
							$this->set($field->name, $file);
						}
					}
				}
				else {
					$this->set($field->name, null);
				}
			}
		}

		/**
		 * Destructor
		 * @access public
		 * @since 3.0
		 * @package System\Orm\Entity
		 */

		public function __destruct() {
		}
	}