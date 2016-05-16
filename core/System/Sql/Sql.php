<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Sql.php
	 | @author : fab@c++
	 | @description : sql better system
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Sql;

	use System\Cache\Cache;
	use System\Database\Database;
	use System\General\di;
	use System\General\error;
	use System\General\facades;
	use System\General\facadesEntity;
	use System\Exception\MissingSqlException;
	use System\Collection\Collection;
	use System\Orm\Entity\Field;
	use System\Orm\Entity\ForeignKey;
	use System\Orm\Entity\Multiple;
	use System\Orm\Entity\Type\File;
	use System\Profiler\Profiler;
	use System\Request\Request;

	class Sql{
		use error, facades, facadesEntity, di;

		/**
		 * @var array
		*/

		protected $_var = [];

		/**
		 * @var string[]
		*/

		protected $_query = [];

		/**
		 * @var \System\Pdo\Pdo
		 */

		protected $_db;

		/**
		 * cache time
		 * @var array
		*/

		protected $_time = [];

		/**
		 * results
		 * @var array
		*/

		protected $_data = [];

		/**
		 * name for cache files
		 * @var array
		*/

		protected $_nameQuery = '';

		/**
		 * @var \System\Cache\Cache
		*/

		protected $_cache ;

		const PARAM_INT                 = 1;
		const PARAM_BOOL                = 5;
		const PARAM_NULL                = 0;
		const PARAM_STR                 = 2;
		const PARAM_LOB                 = 3;
		const PARAM_FETCH               = 0;
		const PARAM_FETCHCOLUMN         = 1;
		const PARAM_FETCHINSERT         = 2;
		const PARAM_FETCHUPDATE         = 3;
		const PARAM_NORETURN            = 4;
		const PARAM_FETCHDELETE         = 5;

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package System\Sql
		*/

		public function __construct (){
			$this->request = Request::getInstance();
			$this->profiler = Profiler::getInstance();
			$this->_db = Database::getInstance()->db();

			$stack = debug_backtrace(0);
			$trace = $this->getStackTraceToString($stack);

			$this->_nameQuery = 'sql_'.$this->request->src.'_'.$this->request->controller.'_'.$this->request->action.'__'.$trace;
		}

		/**
		 * get the trace of execution. it's used to give an explicit name to the caching file
		 * @access protected
		 * @param $stack string
		 * @return string
		 * @since 3.0
		 * @package System\Sql
		*/

		private function getStackTraceToString($stack){
			if(isset($stack[5]['line']))
				return preg_replace('#^(.*)\\\([a-zA-Z0-9_]+)$#isU', '$2', $stack[5]['class']).'_'.$stack[5]['function'].'_'.$stack[5]['line'].'_';
			else
				return preg_replace('#^(.*)\\\([a-zA-Z0-9_]+)$#isU', '$2', $stack[4]['class']).'_'.$stack[4]['function'].'_';
		}

		/**
		 * Add a new query to the instance
		 * @access public
		 * @param $name string : the name of the query. If the name already exists, the old query will be erased
		 * @param $query string : the query with the Pdo syntax
		 * @param $time int : time cache
		 * @return void
		 * @since 3.0
		 * @package System\Sql
		*/
		
		public function query($name, $query, $time = 0){
			$this->_query[''.$name.''] = $query;
			$this->_time[''.$name.''] = $time;
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
		 * @return void
		 * @since 3.0
		 * @package System\Sql
		*/
		
		public function vars($var){
			if(is_array($var)){
				foreach($var as $key => $valeur){
					$this->_var[$key] = $valeur;
				}
			}
			else if(func_num_args() == 2){
				$args = func_get_args();
				$this->_var[$args[0]] = $args[1];
			}

			else if(func_num_args() == 3){
				$args = func_get_args();
				$this->_var[$args[0]] = [$args[1], $args[2]];
			}
		}

		/**
		 * Execute the query. This method returns the pdo object like the real execute() PDO method.
		 * It's useful if you want to use PDO method like lastInsertId()
		 * @access public
		 * @param string $name : name of the query you want to execute
		 * @throws \System\Exception\MissingSqlException
		 * @return \System\Pdo\Pdo
		 * @since 3.0
		 * @package System\Sql
		*/

		public function execute($name){
			try{
				$query = $this->_db->prepare(''.$this->_query[''.$name.''].'');
				
				foreach($this->_var as $key => $value){
					if(preg_match('`:'.$key.'[\s|,|\)|\(%]`', $this->_query[''.$name.''].' ')){
						if(is_array($value)){
							$query->bindValue(":$key", $value[0], $value[1]);
						}
						else{
							switch(gettype($value)){
								case 'boolean' :
									$query->bindValue(":$key", $value, self::PARAM_BOOL);
								break;
								
								case 'integer' :
									$query->bindValue(":$key", $value, self::PARAM_INT);
								break;
								
								case 'double' :
									$query->bindValue(":$key", $value, self::PARAM_STR);
								break;
								
								case 'string' :
									$query->bindValue(":$key", $value, self::PARAM_STR);
								break;
								
								case 'NULL' :
									$query->bindValue(":$key", $value, self::PARAM_NULL);
								break;
								
								default :
									$this->addError('SQL '.$name.'::'.$key.' unrecognized type', __LINE__, __FILE__, ERROR_INFORMATION, LOG_SQL);
								break;
							}
						}
					}
				}

				$query->execute();
				
				return $query;
			}
			catch (\PDOException $e){
				throw new MissingSqlException($e->getMessage().' / '.$e->getCode());
			}
		}

		/**
		 * Fetch a query. This method returns several values, depending on the fetching parameter
		 * @access public
		 * @param $name string : the name of the query you want to fetch
		 * @param $fetch int : type of fetch. 5 values available
		 *  sql::PARAM_FETCH         : correspond to the fetch of PDO. it's usefull for SELECT queries
		 *  sql::PARAM_FETCHCOLUMN   : correspond to the fetchcolumn of PDO. it's usefull for SELECT COUNT queries
		 *  sql::PARAM_FETCHINSERT   : useful for INSERT queries
		 *  sql::PARAM_FETCHUPDATE   : useful for UPDATE queries
		 *  sql::PARAM_FETCHDELETE   : useful for DELETE queries
		 *  default value : sql::PARAM_FETCH
		 * @throws MissingSqlException
		 * @return mixed
		 * @since 3.0
		 * @package System\Sql
		*/

		public function fetch($name, $fetch = self::PARAM_FETCH){
			if($this->_time[''.$name.''] > 0){
				$this->_cache = new Cache($this->_nameQuery.$name.'.sql', "", $this->_time[''.$name.'']);
			}

			if((isset($this->_cache) && $this->_cache->isDie() && $this->_time[''.$name.''] > 0) ||
				$this->_time[''.$name.''] == 0 || $fetch == self::PARAM_FETCHINSERT ||
				$fetch == self::PARAM_FETCHUPDATE || $fetch == self::PARAM_FETCHDELETE){

				try {
					/** @var \System\Pdo\PdoStatement $query */
					$query = $this->_db->prepare(''.$this->_query[''.$name.''].'');
					$this->profiler->addTime($this->_nameQuery.$name);
					$this->profiler->addSql($this->_nameQuery.$name, Profiler::SQL_START);
					
					foreach($this->_var as $key => $value){
						if(preg_match('`:'.$key.'[\s|,|\)|\(%]`', $this->_query[''.$name.''].' ')){
							if(is_array($value)){
								$query->bindValue(":$key", $value[0], $value[1]);
							}
							else{
								switch(gettype($value)){
									case 'boolean' :
										$query->bindValue(":$key", $value, self::PARAM_BOOL);
									break;
									
									case 'integer' :
										$query->bindValue(":$key", $value, self::PARAM_INT);
									break;
									
									case 'double' :
										$query->bindValue(":$key", $value, self::PARAM_STR);
									break;
									
									case 'string' :
										$query->bindValue(":$key", $value, self::PARAM_STR);
									break;
									
									case 'NULL' :
										$query->bindValue(":$key", $value, self::PARAM_NULL);
									break;
									
									default :
										$this->addError($name.'::'.$key.' unrecognized type', __LINE__, __FILE__, ERROR_INFORMATION, LOG_SQL);
									break;
								}
							}
						}
					}

					$query->execute();

					switch($fetch){
						case self::PARAM_FETCH : $this->_data = $query->fetchAll(); break;
						case self::PARAM_FETCHCOLUMN : $this->_data = $query->fetchColumn(); break;
						case self::PARAM_FETCHINSERT : $this->_data = true; break;
						case self::PARAM_FETCHUPDATE : $this->_data = true; break;
						case self::PARAM_FETCHDELETE : $this->_data = true; break;
						case self::PARAM_NORETURN : $this->_data = true; break;
						default : $this->addError('the execution constant '.$fetch.' doesn\'t exist', __LINE__, __FILE__, ERROR_INFORMATION, LOG_SQL); break;
					}

					$this->addError("\n".'['.$this->request->src.'] ['.$this->request->controller.'] ['.$this->request->action.'] ['.$name."]".$query->debugQuery(), __LINE__, __FILE__, ERROR_INFORMATION, LOG_SQL);
					$this->profiler->addSql($this->_nameQuery.$name, Profiler::SQL_END, $query->debugQuery());
					$this->profiler->addTime($this->_nameQuery.$name, Profiler::USER_END);

					switch($fetch){
						case self::PARAM_FETCH :
							if(isset($this->_cache)){
								$this->_cache->setContent($this->_data);
								$this->_cache->setCache();
							}

							return $this->_data; break;
						break;

						case self::PARAM_FETCHCOLUMN :
							if(isset($this->_cache)){
								$this->_cache->setContent($this->_data);
								$this->_cache->setCache();
							}

							return $this->_data;
						break;

						case self::PARAM_FETCHINSERT : return true; break;
						case self::PARAM_FETCHUPDATE : return true; break;
						case self::PARAM_FETCHDELETE : return true; break;
						case self::PARAM_NORETURN : return true; break;
					}
				}
				catch (\PDOException $e) {
					throw new MissingSqlException($e->getMessage().' / '.$e->getCode());
				}
			}
			else{
				if(isset($this->_cache)){
					return $this->_cache->getCache();
				}
				else{
					return false;
				}
			}

			return true;
		}

		/**
		 * return data as an array of entities
		 * @access public
		 * @param $entity string
		 * @return \System\Collection\Collection
		 * @since 3.0
		 * @package System\Sql
		*/

		public function data($entity = ''){
			$entities = [];

			foreach($this->_data as $line){
				if($entity != ''){
					/** @var  $entityObject \System\Orm\Entity\Entity */
					$entityObject = self::Entity()->$entity();

					foreach($line as $key => $field){
						$value = null;
						$parent = '';

						$key = str_replace('count_one_'.$entityObject->name().'_', '', $key);
						$key = str_replace('count_many_'.$entityObject->name().'_', '', $key);
						$key = str_replace($entityObject->name().'_', '', $key);

						if($entityObject->getField($key) != null){
							if(in_array($entityObject->getField($key)->type, [Field::INCREMENT, Field::INT, Field::TEXT, Field::FLOAT, Field::STRING, Field::CHAR, Field::BOOL, Field::DATE, Field::DATETIME, Field::TIME, Field::TIMESTAMP, Field::ENUM])){
								if($entityObject->getField($key)->foreign == null){
									$value = $field;
								}
								else if($entityObject->getField($key)->foreign->type() == ForeignKey::ONE_TO_MANY){
									$value = $field;
								}
								else if($entityObject->getField($key)->foreign->type() == ForeignKey::MANY_TO_MANY){
									$value = $field;
								}
								else{
									foreach ($entityObject->fields() as $fieldIsRightForeign) {
										if ($fieldIsRightForeign->foreign != null) {
											$parent = $fieldIsRightForeign->name . '_';
											$value = $this->_getDataRelation($line, $entityObject->getField(str_replace($fieldIsRightForeign->name . '_', '', $key))->foreign, $parent);
										}
									}

								}
							}
							else{
								switch($entityObject->getField($key)->type){
									case Field::FILE :
										$file = new File('','','','');
										$file->hydrate($field);
										$value = $file;
									break;
								}
							}

							$entityObject->getField(str_replace($parent, '', $key))->value = $value;
						}
					}
				}
				else{
					$entityObject = new Multiple($line);
				}

				array_push($entities, $entityObject);
			}

			return new Collection($entities);
		}

		/**
		 * If the entity used has at least one relation, we have
		 * to get an Entity object instead of the foreign key
		 * @access protected
		 * @param $line \System\Collection\Collection
		 * @param $foreign \System\Orm\Entity\ForeignKey
		 * @return \System\Orm\Entity\Entity
		 * @since 3.0
		 * @package System\Sql
		*/

		protected function _getDataRelation($line, $foreign, $parent){
			/** @var $entity \System\Orm\Entity\Entity */

			$class = $foreign->referenceEntity();
			$entity = self::Entity()->$class();

			if($line[$foreign->field().'_'.$entity->name().'_'.$entity->primary()] != '') {
				foreach ($line as $key => $field) {
					$value = null;

					$key = str_replace($foreign->field().'_'.$entity->name().'_', '', $key);

					if ($entity->getField(str_replace($foreign->field().'_'.$entity->name().'_', '', $key)) != null) {
						if (in_array($entity->getField(str_replace($foreign->field().'_'.$entity->name().'_', '', $key))->type, [Field::INCREMENT, Field::INT, Field::FLOAT, Field::TEXT, Field::STRING, Field::CHAR, Field::BOOL, Field::DATE, Field::DATETIME, Field::TIME, Field::TIMESTAMP, Field::ENUM])) {
							$value = $field;
						}
						else {
							switch ($entity->getField(str_replace($foreign->field().'_'.$entity->name().'_', '', $key))->type) {
								case Field::FILE :
									$file = new File('', '', '', '');
									$file->hydrate($field);
									$value = $file;
								break;
							}
						}

						$entity->getField(str_replace($foreign->field().'_'.$entity->name().'_', '', $key))->value = $value;
					}
				}

				return $entity;
			}
			else{
				return null;
			}
		}

		/**
		 * return data as an array
		 * @access public
		 * @return array
		 * @since 3.0
		 * @package System\Sql
		*/

		public function toArray(){
			return $this->_cache->getCache();
		}

		/**
		 * destructor
		 * @access public
		 * @since 3.0
		 * @package System\Sql
		*/

		public function __destruct(){
		}
	}