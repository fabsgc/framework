<?php
	/*\
	 | ------------------------------------------------------
	 | @file : Database.php
	 | @author : Fabien Beaujean
	 | @description : enable database connection
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/

	namespace Gcs\Framework\Core\Database;

	use Gcs\Framework\Core\Config\Config;
	use Gcs\Framework\Core\Exception\MissingDatabaseException;
	use Gcs\Framework\Core\General\Singleton;
	use Gcs\Framework\Core\Pdo\Pdo;

	/**
	 * Class Database
	 * @package Gcs\Framework\Core\Database
	 */

	class Database {
		use Singleton;

		/**
		 * @var \System\Pdo\Pdo
		 */

		protected $db;

		/**
		 * constructor
		 * @access public
		 * @since 3.0
		 * @package Gcs\Framework\Core\Response
		 */

		private function __construct() {
			$this->connect();
		}

		/**
		 * singleton
		 * @access public
		 * @param $db []
		 * @return \System\Database\Database
		 * @since 3.0
		 * @package Gcs\Framework\Core\Request
		 */

		public static function instance() {
			if (is_null(self::$_instance)) {
				if (Config::config()['user']['database']['enabled']) {
					self::$_instance = new Database();
				}
				else {
					self::$_instance = new Database();
				}
			}

			return self::$_instance;
		}

		/**
		 * create the database connection
		 * @access public
		 * @param $db []
		 * @throws MissingDatabaseException
		 * @return mixed
		 * @since 3.0
		 * @package Gcs\Framework\Core\Database
		 */

		protected function connect($db = []) {
			$db = Config::config()['user']['database'];

			if ($db['enabled']) {
				switch ($db['driver']) {
					case 'pdo' :
						$options = [
							Pdo::ATTR_STATEMENT_CLASS => ['\System\Pdo\PdoStatement', []]
						];

						switch ($db['type']) {
							case 'mysql':
								try {
									$this->db = new Pdo('mysql:host=' . $db['hostname'] . ';dbname=' . $db['database'], $db['username'], $db['password'], $options);
									//self::$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									$this->db->exec('SET NAMES ' . strtoupper($db['charset']));
								}
								catch (\PDOException $e) {
									throw new MissingDatabaseException($e->getMessage() . ' / ' . $e->getCode());
								}
							break;

							case 'pgsql':
								try {
									$this->db = new Pdo('mysql:host=' . $db['hostname'] . ';dbname=' . $db['database'], $db['username'], $db['password'], $options);
									//self::$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									$this->db->exec('SET NAMES ' . strtoupper($db['charset']));
								}
								catch (\PDOException $e) {
									throw new MissingDatabaseException($e->getMessage() . ' / ' . $e->getCode());
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

				return $this->db;
			}
			else {
				return null;
			}
		}

		public function db() {
			return $this->db;
		}
	}