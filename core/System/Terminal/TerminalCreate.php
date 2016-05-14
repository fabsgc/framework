<?php
	/*\
	 | ------------------------------------------------------
	 | @file : TerminalCreate.php
	 | @author : fab@c++
	 | @description : terminal command create
	 | @version : 3.0 bêta
	 | ------------------------------------------------------
	\*/
	
	namespace System\Terminal;

	use System\Database\Database;
	use System\Orm\Entity\ForeignKey;
	use System\Sql\Sql;
	use System\Template\Template;

	class TerminalCreate extends TerminalCommand{
		public function module(){
			$src = '';
			$controllers = [];

			//choose the module name
			while(1==1){
				echo ' - choose module name : ';
				$src = ArgvInput::get();

				if(!file_exists(DOCUMENT_ROOT.SRC_PATH.$src.'/')){
					break;
				}
				else{
					echo "[ERROR] this module already exists\n";
				}
			}

			//choose the number of controllers
			while(1==1){
				echo ' - add a controller (keep empty to stop) : ';
				$controller = argvInput::get();
					
				if($controller != ''){
					if(!in_array($controller, $controllers)){
						array_push($controllers, $controller);
					}
					else{
						echo "[ERROR] you have already chosen this controller\n";
					}
				}
				else{
					if(count($controllers) > 0){
						break;
					}
					else{
						echo "[ERROR] you must add at least one controller\n";
					}
				}
			}

			//load all template to fill the new files
			$tpl['cron'] = new Template('.app/system/module/cron', 'terminalCreateCron');
			$tpl['define'] = new Template('.app/system/module/define', 'terminalCreateDefine');
			$tpl['lang'] = new Template('.app/system/module/lang', 'terminalCreateLang');
			$tpl['library'] = new Template('.app/system/module/library', 'terminalCreateLibrary');
			$tpl['route'] = new Template('.app/system/module/route', 'terminalCreateRoute');
			$tpl['firewall'] = new Template('.app/system/module/firewall', 'terminalCreateFirewall');
			$tpl['firewall']->assign('src', $src);

			//creation of directories and files
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_CONTROLLER_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_MODEL_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_CONFIG_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_EVENT_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_LANG_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_LIBRARY_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_REQUEST_PATH);
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_REQUEST_PATH.'/Custom/');
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_REQUEST_PATH.'/Plugin/');
			mkdir(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_TEMPLATE_PATH);

			mkdir(DOCUMENT_ROOT.WEB_PATH.$src);
			mkdir(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_CSS_PATH);
			mkdir(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_FILE_PATH);
			mkdir(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_IMAGE_PATH);
			mkdir(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_JS_PATH);

			$gitignore = "# Ignore everything in this directory\n*\n# Except this file\n!.gitignore";

			file_put_contents(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_CSS_PATH.'/index.html', '');
			file_put_contents(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_FILE_PATH.'/index.html', '');
			file_put_contents(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_IMAGE_PATH.'/index.html', '');
			file_put_contents(DOCUMENT_ROOT.WEB_PATH.$src.'/'.WEB_JS_PATH.'/index.html', '');

			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/.gitignore', $gitignore);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_EVENT_PATH.'.gitignore', $gitignore);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_LIBRARY_PATH.'.gitignore', $gitignore);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_LIBRARY_PATH.'.gitignore', $gitignore);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_REQUEST_PATH.'.gitignore', $gitignore);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_REQUEST_PATH.'/Custom/.gitignore', $gitignore);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_REQUEST_PATH.'/Plugin/.gitignore', $gitignore);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_TEMPLATE_PATH.'.gitignore', $gitignore);

			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_CONFIG_PATH.'cron.xml', $tpl['cron']->show());
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_CONFIG_PATH.'define.xml', $tpl['define']->show());
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_CONFIG_PATH.'firewall.xml', $tpl['firewall']->show());
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_CONFIG_PATH.'library.xml', $tpl['library']->show());
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_CONFIG_PATH.'route.xml', '');

			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_LANG_PATH.'fr.xml', $tpl['lang']->show());

			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_CONTROLLER_FUNCTION_PATH, '');

			$routeGroup = '';

			foreach ($controllers as $value) {
				$tpl['routeGroup'] = new Template('.app/system/module/routeGroup', 'terminalCreateRouteGroup'.$value);
				$tpl['routeGroup']->assign(array('src' => $src, 'controller' => $value));
				$routeGroup .= $tpl['routeGroup']->show();

				$tpl['controller'] = new Template('.app/system/module/controller', 'terminalCreateController'.$value);
				$tpl['controller']->assign(array('src' => $src, 'controller' => ucfirst($value)));
				$tpl['model'] = new Template('.app/system/module/model', 'terminalCreateModel'.$value);
				$tpl['model']->assign(array('src' => $src, 'model' => ucfirst($value)));

				file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_CONTROLLER_PATH.ucfirst($value).EXT_CONTROLLER.'.php', $tpl['controller']->show());
				file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_MODEL_PATH.ucfirst($value).EXT_MODEL.'.php',  $tpl['model']->show());
			}

			$tpl['route']->assign('route', $routeGroup);
			file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_RESOURCE_CONFIG_PATH.'route.xml', $tpl['route']->show());

			$exist = false;
			$xml = simplexml_load_file(APP_CONFIG_SRC);
			$datas =  $xml->xpath('//src');

			foreach ($datas as $data) {
				if($data['name'] == $src)
					$exist = true;
			}

			if($exist == false){
				$node = $xml->addChild('src', null);
				$node->addAttribute('name', $src);

				$dom = new \DOMDocument("1.0");
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
				$dom->loadXML($xml->asXML());
				$dom->save(APP_CONFIG_SRC);
			}

			echo ' - the module has been successfully created';
		}

		public function controller(){
			$src = '';
			$controllers = [];

			//choose the module name
			while(1==1){
				echo ' - choose a module : ';
				$src = ArgvInput::get();

				if(file_exists(DOCUMENT_ROOT.SRC_PATH.$src.'/')){
					break;
				}
				else{
					echo " - [ERROR] this module doesn't exist\n";
				}
			}

			//choose the controllers
			while(1==1){
				echo ' - add a controller (keep empty to stop) : ';
				$controller = argvInput::get();
					
				if($controller != ''){
					if(!in_array($controller, $controllers) AND !file_exists(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_CONTROLLER_PATH.'/'.ucfirst($controller).EXT_CONTROLLER.'.php')){
						array_push($controllers, $controller);
					}
					else{
						echo "[ERROR] you have already chosen this controller or it's already created.\n";
					}
				}
				else{
					if(count($controllers) > 0){
						break;
					}
					else{
						echo "[ERROR] you must add at least one controller\n";
					}
				}
			}

			foreach ($controllers as $value) {
				$tpl['controller'] = new Template('.app/system/module/controller', 'terminalCreateController'.$value);
				$tpl['controller']->assign(array('src' => $src, 'controller' => ucfirst($value)));
				$tpl['model'] = new Template('.app/system/module/model', 'terminalCreateModel'.$value);
				$tpl['model']->assign(array('src' => $src, 'model' => ucfirst($value)));

				file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_CONTROLLER_PATH.ucfirst($value).EXT_CONTROLLER.'.php', $tpl['controller']->show());
				file_put_contents(DOCUMENT_ROOT.SRC_PATH.$src.'/'.SRC_MODEL_PATH.ucfirst($value).EXT_MODEL.'.php',  $tpl['model']->show());
				
				echo "\n - the controller ".$value." have been successfully created";
			}
		}

		public function entity(){
			$table = '';

			if(DATABASE){
				while(1==1){
					echo ' - choose a table (*) : ';
					$table = ArgvInput::get();

					if($table != ''){
						break;
					}
					else{
						$table = ArgvInput::get();
					}
				}

				if($table != '*'){
					TerminalCreate::addEntity($table);
				}
				else{
					$sql = new Sql(Database::getInstance()->db());
					$sql->query('add-entity', 'SHOW TABLES FROM '.Database::getInstance()->db()->getDatabase());
					$data = $sql->fetch('add-entity', Sql::PARAM_FETCH);

					foreach($data as $value){
						$this->addEntity($value[0]);
					}
				}
			}
			else{
				echo ' - you\'re not logged to any database';
			}
		}

		/**
		 * Create Entity
		 * @access public
		 * @param $table string
		 * @return string
		 * @since 3.0
		 * @package System\Terminal
		*/

		private function addEntity($table) {
			//the entity must have a primary key
			$primary = false;
			$property = '';

			$class = ucfirst(preg_replace_callback("/(?:^|_)([a-z])/", function($matches) {
				return strtoupper($matches[1]);
			}, $table));

			if(file_exists(APP_RESOURCE_ENTITY_PATH.$table.EXT_ENTITY.'.php')){
				unlink(APP_RESOURCE_ENTITY_PATH.$table.EXT_ENTITY.'.php');
			}

			$sql = new Sql(Database::getInstance()->db());
			$sql->query('add-entity', 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table');
			$sql->vars(array('db' => Database::getInstance()->db()->getDatabase()));
			$sql->vars(array('table' => $table));

			$field = '';

			foreach($sql->fetch('add-entity', Sql::PARAM_FETCH) as $value){
				/** @var $fieldUnique boolean : we want to know if a field is unique to add the correct relation "one to one" OR "many_to_one" */
				$fieldUnique = false;

				$field .= '			$this->field(\''.$value['COLUMN_NAME'].'\')'."\n";

				if(preg_match('#(PRI)#isU', $value['COLUMN_KEY'])){
					$field .= '				->primary(true)'."\n";
					$field .= '				->unique(true)'."\n";
					$primary = true;
					$fieldUnique = true;
				}
				else if(preg_match('#(UNI)#isU', $value['COLUMN_KEY'])){
					$field .= '				->unique(true)'."\n";
					$fieldUnique = true;
				}

				$columnType = $value['COLUMN_TYPE'];
				$columnTypeNumber = false;

				if(preg_match('#(auto_increment)#isU', $value['EXTRA'])){
					$field .= '				->type(Field::INCREMENT)'."\n";
					$property .= '	  * @property integer '.$value['COLUMN_NAME']."\n";
					$primary = true;
					$columnTypeNumber = true;
				}
				else if(preg_match('#(int)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::INT)'."\n";
					$property .= '	  * @property integer '.$value['COLUMN_NAME']."\n";
					$columnTypeNumber = true;
				}
				else if(preg_match('#(char)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::STRING)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(text)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::TEXT)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(binary)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::STRING)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(decimal)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::FLOAT)'."\n";
					$columnType = str_replace('decimal(', '', $columnType);
					$columnType = str_replace(')', '', $columnType);
					$field .= '				->precision(array(\''.$columnType.'\'))'."\n";
					$property .= '	  * @property float '.$value['COLUMN_NAME']."\n";
					$columnTypeNumber = true;
				}
				else if(preg_match('#(float)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::FLOAT)'."\n";
					$columnType = str_replace('float(', '', $columnType);
					$columnType = str_replace(')', '', $columnType);
					$field .= '				->precision(array(\''.$columnType.'\'))'."\n";
					$property .= '	  * @property float '.$value['COLUMN_NAME']."\n";
					$columnTypeNumber = true;
				}
				else if(preg_match('#(double)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::FLOAT)'."\n";
					$columnType = str_replace('double(', '', $columnType);
					$columnType = str_replace(')', '', $columnType);
					$field .= '				->precision(array(\''.$columnType.'\'))'."\n";
					$property .= '	  * @property float '.$value['COLUMN_NAME']."\n";
					$columnTypeNumber = true;
				}
				else if(preg_match('#(datetime)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::DATETIME)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(date)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::DATE)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(timestamp)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::TIMESTAMP)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(time)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::TIME)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(binary)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::STRING)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else if(preg_match('#(enum)#isU', $value['DATA_TYPE'])){
					$field .= '				->type(Field::ENUM)'."\n";
					$columnType = str_replace('enum(', '', $columnType);
					$columnType = str_replace(')', '', $columnType);
					$field .= '				->enum(array('.$columnType.'))'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}
				else{
					$field .= '				->type(Field::STRING)'."\n";
					$property .= '	  * @property string '.$value['COLUMN_NAME']."\n";
				}

				if($value['CHARACTER_MAXIMUM_LENGTH'] != ''){
					$field .= '				->size('.$value['CHARACTER_MAXIMUM_LENGTH'].')'."\n";
				}

				if($value['IS_NULLABLE'] == "NO"){
					$field .= '				->beNull(false)'."\n";
				}

				if($value['COLUMN_DEFAULT'] != ""){
					$field .= '				->defaultValue(\''.addslashes($value['COLUMN_DEFAULT']).'\')'."\n";
				}

				$sql->query('add-entity-unique-key', 'SELECT COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table AND COLUMN_NAME = :field AND CONSTRAINT_NAME = :primary');
				$sql->vars('field', $value['COLUMN_NAME']);
				$sql->vars('primary', 'my_unique_key');
				$data = $sql->fetch('add-entity-unique-key');

				if(count($data) == 1 && $fieldUnique == false){
					$field .= '				->unique(true)'."\n";
					$fieldUnique = true;
				}

				$sql->query('add-entity-foreign-key', 'SELECT COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table AND COLUMN_NAME = :field AND REFERENCED_TABLE_NAME != \'\' AND REFERENCED_COLUMN_NAME != \'\'');
				$sql->vars('field', $value['COLUMN_NAME']);
				$sql->vars('primary', 'PRIMARY');
				$data = $sql->fetch('add-entity-foreign-key');

				if(count($data) == 1){
					switch($fieldUnique){
						case true :
							$field .= '				->foreign(['."\n";
							$field .= '					\'type\' => ForeignKey::ONE_TO_ONE,'."\n";
							$field .= '					\'reference\' => [\''.ucfirst(strtolower($data[0]['REFERENCED_TABLE_NAME'])).'\', \''.$data[0]['REFERENCED_COLUMN_NAME'].'\']'."\n";
							$field .= '				])'."\n";
						break;

						case false :
							$field .= '				->foreign(['."\n";
							$field .= '					\'type\' => ForeignKey::MANY_TO_ONE,'."\n";
							$field .= '					\'reference\' => [\''.ucfirst(strtolower($data[0]['REFERENCED_TABLE_NAME'])).'\', \''.$data[0]['REFERENCED_COLUMN_NAME'].'\']'."\n";
							$field .= '				])'."\n";
						break;
					}
				}

				$field .= ";\n";
			}

			$field = str_replace("\n;", ';', $field);

			if($primary == true){
				$t = new Template('.app/system/module/orm/entity', 'gcsEntity_'.$table, '0');
				$t->assign(array('class'=> $class, 'table' => $table, 'field'=> $field, 'property' => $property));
				file_put_contents(APP_RESOURCE_ENTITY_PATH.ucfirst($class).EXT_ENTITY.'.php', $t->show());

				echo ' - the entity "'.$class.'" has been successfully created';
			}
			else{
				echo ' - the entity "'.$class.'" must have a primary key';
			}
		}

		public function manytomany(){
			$table = '';

			if(DATABASE){
				while(1==1){
					echo ' - choose an entity : ';
					$table = ArgvInput::get();

					if($table != ''){
						break;
					}
					else{
						$table = ArgvInput::get();
					}
				}

				$class = '\Orm\Entity\\'.str_replace('_', '', ucfirst(strtolower($table)));

				if(class_exists($class)){
					/** @var $entity \System\Orm\Entity\Entity */
					$entity = new $class();

					foreach($entity->fields() as $field){
						if($field->foreign != null && $field->foreign->type() == ForeignKey::MANY_TO_MANY) {
							$class = '\Orm\Entity\\'.$field->foreign->referenceEntity();

							if(class_exists($class)){
								/** @var $referencedEntity \System\Orm\Entity\Entity */
								$referencedEntity = new $class();

								/** We generate the linking table name */
								$current   = ucfirst($entity->name()).($entity->primary());
								$reference = ucfirst($referencedEntity->name()).($field->foreign->referenceField());
								$tableNames = [$current, $reference];
								sort($tableNames, SORT_STRING);
								$tableName = $tableNames[0].strtolower($tableNames[1]);

								$t = new Template('.app/system/module/orm/manytomany', 'gcsEntity_'.$table, '0');
								$t->assign(array(
									'table'   => $tableName,
									'entity1' => $entity->name(),
									'entity2' => $referencedEntity->name(),
									'field1'  => $entity->primary(),
									'field2'  => $field->foreign->referenceField()
								));

								$query = $t->show(Template::TPL_COMPILE_TO_STRING);

								$sql = new Sql();
								$sql->query('create-table-manytomany', $query);
								$sql->fetch('create-table-manytomany', Sql::PARAM_NORETURN);

								echo ' - the many to many table "'.$tableName.'" has been successfully created'."\n";

								$this->addEntity($tableName);
							}
							else{
								echo ' - this entity "'.$field->foreign->referenceEntity().'" does not exist';
							}
						}
					}
				}
				else{
					echo ' - this entity "'.$table.'" does not exist';
				}
			}
			else{
				echo ' - you\'re not logged to any database';
			}
		}
	}