<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Database.php
	 | @author : fab@c++
	 | @description : enable database connection
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Database;

	use System\Pdo\Pdo;
	use System\General\error;
	use System\General\facades;
	use System\General\singleton;
	use System\Exception\MissingDatabaseException;

	class Database{
		use error, facades, singleton;

		/**
		 * @var \System\Pdo\Pdo
		*/

		protected static $sql;

		/**
		 * constructor
		 * @access public
		 * @param $db array
		 * @since 3.0
		 * @package System\Response
		 */

		private function __construct ($db){
			$this->connect($db);
		}

		/**
		 * singleton
		 * @access public
		 * @since 3.0
		 * @package System\Request
		*/

		public static function getInstance($db = array()){
			if (is_null(self::$_instance)){;
				if(DATABASE == true)
					self::$_instance = new Database($db[0]);
				else
					self::$_instance = new Database(array());
			}

			return self::$_instance;
		}

		/**
		 * create the database connection
		 * @access public
		 * @param $db array
		 * @throws MissingDatabaseException
		 * @return mixed
		 * @since 3.0
		 * @package System\Database
		*/

		protected function connect($db){
			if(DATABASE == true){
				switch ($db['driver']){
					case 'pdo' :
						$options = [
							Pdo::ATTR_STATEMENT_CLASS => array('\System\Pdo\PdoStatement', array())
						];

						switch ($db['type']){
							case 'mysql':
								try{
									self::$sql = new Pdo('mysql:host='.$db['hostname'].';dbname='.$db['database'], $db['username'], $db['password'], $options);
									self::$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									self::$sql->exec('SET NAMES '.strtoupper($db['charset']));
								}
								catch (\PDOException $e){
									throw new MissingDatabaseException($e->getMessage().' / '.$e->getCode());
								}
							break;

							case 'pgsql':
								try{
									self::$sql = new Pdo('mysql:host='.$db['hostname'].';dbname='.$db['database'], $db['username'], $db['password'], $options);
									self::$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									self::$sql->exec('SET NAMES '.strtoupper($db['charset']));
								}
								catch (\PDOException $e){
									throw new MissingDatabaseException($e->getMessage().' / '.$e->getCode());
								}
							break;

							default :
								throw new MissingDatabaseException("Can't connect to SQL Database because the driver is not supported");
							break;
						}
					break;

					default :
						throw new MissingDatabaseException("Can't connect to SQL Database because the API is unrecognized");
					break;
				}

				return self::$sql;
			}
			else{
				return null;
			}
		}

		public function db(){
			return self::$sql;
		}
	}