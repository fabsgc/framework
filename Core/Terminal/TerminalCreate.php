<?php
/*\
 | ------------------------------------------------------
 | @file : TerminalCreate.php
 | @author : Fabien Beaujean
 | @description : terminal command create
 | @version : 3.0 bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Terminal;

use Gcs\Framework\Core\Config\Config;
use Gcs\Framework\Core\Database\Database;
use Gcs\Framework\Core\Orm\Entity\ForeignKey;
use Gcs\Framework\Core\Sql\Sql;
use Gcs\Framework\Core\Template\Template;

/**
 * Class TerminalCreate
 * @package Gcs\Framework\Core\Terminal
 */
class TerminalCreate extends TerminalCommand {

    /**
     * return void
     * @access public
     */

    public function module() {
        $src = '';
        $controllers = [];

        //choose the module name
        while (1 == 1) {
            echo ' - choose module name : ';
            $src = ArgvInput::get();

            if (!file_exists(DOCUMENT_ROOT . SRC_PATH . $src . '/')) {
                break;
            }
            else {
                echo "[ERROR] this module already exists\n";
            }
        }

        //choose the number of controllers
        while (1 == 1) {
            echo ' - add a controller (keep empty to stop) : ';
            $controller = ArgvInput::get();

            if ($controller != '') {
                if (!in_array($controller, $controllers)) {
                    array_push($controllers, $controller);
                }
                else {
                    echo "[ERROR] you have already chosen this controller\n";
                }
            }
            else {
                if (count($controllers) > 0) {
                    break;
                }
                else {
                    echo "[ERROR] you must add at least one controller\n";
                }
            }
        }

        //load all template to fill the new files
        $tpl['lang'] = new Template('.app/system/module/lang', 'terminal-create-lang');
        $tpl['route'] = new Template('.app/system/module/route', 'terminal-create-route');
        $tpl['firewall'] = new Template('.app/system/module/firewall', 'terminal-create-firewall');
        $tpl['firewall']->assign('src', $src);

        //creation of directories and files
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src);
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_CONTROLLER_PATH);
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_PATH);
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_CONFIG_PATH);
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_EVENT_PATH);
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_LANG_PATH);
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_REQUEST_PATH);
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_REQUEST_PATH . '/Custom/');
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_REQUEST_PATH . '/Plugin/');
        mkdir(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_TEMPLATE_PATH);

        mkdir(DOCUMENT_ROOT . WEB_PATH . $src);
        mkdir(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_CSS_PATH);
        mkdir(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_FILE_PATH);
        mkdir(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_IMAGE_PATH);
        mkdir(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_JS_PATH);

        file_put_contents(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_CSS_PATH . '/index.html', '');
        file_put_contents(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_FILE_PATH . '/index.html', '');
        file_put_contents(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_IMAGE_PATH . '/index.html', '');
        file_put_contents(DOCUMENT_ROOT . WEB_PATH . $src . '/' . WEB_JS_PATH . '/index.html', '');

        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_EVENT_PATH . '.gitkeep', '');
        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_REQUEST_PATH . '/Custom/.gitkeep', '');
        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_REQUEST_PATH . '/Plugin/.gitkeep', '');
        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_TEMPLATE_PATH . '.gitkeep', '');

        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_CONFIG_PATH . 'firewall.xml', $tpl['firewall']->show());
        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_CONFIG_PATH . 'route.xml', '');

        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_LANG_PATH . 'fr.xml', $tpl['lang']->show());

        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_CONTROLLER_FUNCTION_PATH, '');

        file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_RESOURCE_CONFIG_PATH . 'route.xml', $tpl['route']->show());

        foreach ($controllers as $value) {
            $tpl['controller'] = new Template('.app/system/module/controller', 'terminalCreateController' . $value);
            $tpl['controller']->assign(['php' => '<?php', 'src' => $src, 'controller' => $value]);
            file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_CONTROLLER_PATH . $value . '.php', $tpl['controller']->show());
        }

        echo ' - the module has been successfully created';
    }

    /**
     * return void
     * @access public
     */

    public function controller() {
        $src = '';
        $controllers = [];

        //choose the module name
        while (1 == 1) {
            echo ' - choose a module : ';
            $src = ArgvInput::get();

            if (file_exists(DOCUMENT_ROOT . SRC_PATH . $src . '/')) {
                break;
            }
            else {
                echo " - [ERROR] this module doesn't exist\n";
            }
        }

        //choose the controllers
        while (1 == 1) {
            echo ' - add a controller (keep empty to stop) : ';
            $controller = ArgvInput::get();

            if ($controller != '') {
                if (!in_array($controller, $controllers) AND !file_exists(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_CONTROLLER_PATH . '/' . $controller . '.php')) {
                    array_push($controllers, $controller);
                }
                else {
                    echo "[ERROR] you have already chosen this controller or it's already created.\n";
                }
            }
            else {
                if (count($controllers) > 0) {
                    break;
                }
                else {
                    echo "[ERROR] you must add at least one controller\n";
                }
            }
        }

        foreach ($controllers as $value) {
            $tpl['controller'] = new Template('.app/system/module/controller', 'terminalCreateController' . $value);
            $tpl['controller']->assign(['php' => '<?php', 'src' => $src, 'controller' => $value]);

            file_put_contents(DOCUMENT_ROOT . SRC_PATH . $src . '/' . SRC_CONTROLLER_PATH . $value . '.php', $tpl['controller']->show());

            echo "\n - the controller " . $value . " have been successfully created";
        }
    }

    /**
     * return void
     * @access public
     */

    public function entity() {
        $table = '';

        if (Config::instance()->config['user']['database']['enabled']) {
            while (1 == 1) {
                echo ' - choose a table (*) : ';
                $table = ArgvInput::get();

                if ($table != '') {
                    break;
                }
                else {
                    $table = ArgvInput::get();
                }
            }

            if ($table != '*') {
                TerminalCreate::addEntity($table);
            }
            else {
                $sql = new Sql(Database::instance()->db());
                $sql->query('add-Entity', 'SHOW TABLES FROM ' . Database::instance()->db()->getDatabase());
                $data = $sql->fetch('add-Entity', Sql::PARAM_FETCH);

                foreach ($data as $value) {
                    $this->addEntity($value[0]);
                }
            }
        }
        else {
            echo ' - you\'re not logged to any database';
        }
    }

    /**
     * Create Entity
     * @access public
     * @param $table string
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Terminal
     */

    private function addEntity($table) {
        //the Entity must have a primary key
        $primary = false;
        $collection = false;

        $class = ucfirst(preg_replace_callback("/(?:^|_)([a-z])/", function ($matches) {
            return strtoupper($matches[1]);
        }, $table));

        if (file_exists(APP_RESOURCE_ENTITY_PATH . $table . '.php')) {
            unlink(APP_RESOURCE_ENTITY_PATH . $table . '.php');
        }

        $sql = new Sql(Database::instance()->db());
        $sql->query('add-Entity', 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table');
        $sql->vars(['db' => Database::instance()->db()->getDatabase()]);
        $sql->vars(['table' => $table]);

        $fields = [];

        foreach ($sql->fetch('add-Entity', Sql::PARAM_FETCH) as $value) {
            /** @var $fieldUnique boolean : we want to know if a field is unique to add the correct relation "one to one" OR "many_to_one" */
            $fieldUnique = false;

            $fieldData = ['name' => $value['COLUMN_NAME'], 'type-php' => '', 'type-orm' => '', 'unique' => false, 'primary' => false, 'size' => 0, 'beNull' => true, 'defaultValue' => '', 'precision' => '', 'enum' => '', 'collection' => false, 'foreign' => ['enabled' => false, 'type' => '', 'to' => '']];

            if (preg_match('#(PRI)#isU', $value['COLUMN_KEY'])) {
                $fieldData['primary'] = true;
                $fieldData['unique'] = true;
                $primary = true;
                $fieldUnique = true;
            }
            else if (preg_match('#(UNI)#isU', $value['COLUMN_KEY'])) {
                $fieldData['unique'] = true;
                $fieldUnique = true;
            }

            $columnType = $value['COLUMN_TYPE'];

            if (preg_match('#(auto_increment)#isU', $value['EXTRA'])) {
                $fieldData['type-orm'] = 'INCREMENT';
                $fieldData['type-php'] = 'integer';
                $primary = true;
            }
            else if (preg_match('#(int)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'INT';
                $fieldData['type-php'] = 'integer';
            }
            else if (preg_match('#(char)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'STRING';
                $fieldData['type-php'] = 'string';
            }
            else if (preg_match('#(text)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'TEXT';
                $fieldData['type-php'] = 'string';
            }
            else if (preg_match('#(binary)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'STRING';
                $fieldData['type-php'] = 'string';
            }
            else if (preg_match('#(decimal)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'FLOAT';
                $fieldData['type-php'] = 'float';

                $columnType = str_replace('decimal(', '', $columnType);
                $columnType = str_replace(')', '', $columnType);

                $fieldData['precision'] = $columnType;
            }
            else if (preg_match('#(float)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'FLOAT';
                $fieldData['type-php'] = 'float';

                $columnType = str_replace('float(', '', $columnType);
                $columnType = str_replace(')', '', $columnType);

                $fieldData['precision'] = $columnType;
            }
            else if (preg_match('#(double)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'FLOAT';
                $fieldData['type-php'] = 'float';

                $columnType = str_replace('double(', '', $columnType);
                $columnType = str_replace(')', '', $columnType);

                $fieldData['precision'] = $columnType;
            }
            else if (preg_match('#(datetime)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'DATETIME';
                $fieldData['type-php'] = '\DateTime';
            }
            else if (preg_match('#(date)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'DATETIME';
                $fieldData['type-php'] = '\DateTime';
            }
            else if (preg_match('#(timestamp)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'DATETIME';
                $fieldData['type-php'] = '\DateTime';
            }
            else if (preg_match('#(binary)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'STRING';
                $fieldData['type-php'] = 'string';
            }
            else if (preg_match('#(enum)#isU', $value['DATA_TYPE'])) {
                $fieldData['type-orm'] = 'ORM';
                $fieldData['type-php'] = 'string';

                $columnType = str_replace('enum(', '', $columnType);
                $columnType = str_replace(')', '', $columnType);

                $fieldData['enum'] = $columnType;
            }
            else {
                $fieldData['type-orm'] = 'STRING';
                $fieldData['type-php'] = 'string';
            }

            if ($value['CHARACTER_MAXIMUM_LENGTH'] != '') {
                $fieldData['size'] = $value['CHARACTER_MAXIMUM_LENGTH'];
            }

            if ($value['IS_NULLABLE'] == "NO") {
                $fieldData['beNull'] = false;
            }

            if ($value['COLUMN_DEFAULT'] != "") {
                $fieldData['defaultValue'] = $value['COLUMN_DEFAULT'];
            }

            $sql->query('add-Entity-unique-key', 'SELECT COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table AND COLUMN_NAME = :field AND CONSTRAINT_NAME = :primary');
            $sql->vars('field', $value['COLUMN_NAME']);
            $sql->vars('primary', 'my_unique_key');
            $data = $sql->fetch('add-Entity-unique-key');

            if (count($data) == 1 && $fieldUnique == false) {
                $fieldData['unique'] = true;
                $fieldUnique = true;
            }

            $sql->query('add-Entity-foreign-key', 'SELECT COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table AND COLUMN_NAME = :field AND REFERENCED_TABLE_NAME != \'\' AND REFERENCED_COLUMN_NAME != \'\'');
            $sql->vars('field', $value['COLUMN_NAME']);
            $sql->vars('primary', 'PRIMARY');
            $data = $sql->fetch('add-Entity-foreign-key');

            if (count($data) == 1) {
                switch ($fieldUnique) {
                    case true :
                        $fieldData['type-php'] = ucfirst(preg_replace_callback("/(?:^|_)([a-z])/", function ($matches) {
                            return strtoupper($matches[1]);
                        }, $data[0]['REFERENCED_TABLE_NAME']));

                        $fieldData['foreign'] = ['enabled' => true, 'type' => 'OneToOne', 'to' => ucfirst(strtolower($data[0]['REFERENCED_TABLE_NAME'])) . '.' . $data[0]['REFERENCED_COLUMN_NAME']];
                        break;

                    case false :
                        $fieldData['type-php'] = ucfirst(preg_replace_callback("/(?:^|_)([a-z])/", function ($matches) {
                            return strtoupper($matches[1]);
                        }, $data[0]['REFERENCED_TABLE_NAME']));

                        $fieldData['foreign'] = ['enabled' => true, 'type' => 'ManyToOne', 'to' => ucfirst(strtolower($data[0]['REFERENCED_TABLE_NAME'])) . '.' . $data[0]['REFERENCED_COLUMN_NAME']];
                        break;
                }
            }

            $fields[$value['COLUMN_NAME']] = $fieldData;
        }

        if ($primary == true) {
            $t = new Template('.app/system/module/orm/Entity', 'gcsEntity_' . $table, '0');
            $t->assign(['php' => '<?php', 'class' => $class, 'collection' => $collection, 'table' => $table, 'form' => 'form-' . $table, 'fields' => $fields]);
            file_put_contents(APP_RESOURCE_ENTITY_PATH . ucfirst($class) . '.php', $t->show());

            echo ' - the Entity "' . $class . '" has been successfully created';
        }
        else {
            echo ' - the Entity "' . $class . '" must have a primary key';
        }
    }

    /**
     * return void
     * @access public
     */

    public function manytomany() {
        $table = '';

        if (Config::config()['user']['database']['enabled']) {
            while (1 == 1) {
                echo ' - choose an Entity : ';
                $table = ArgvInput::get();

                if ($table != '') {
                    break;
                }
                else {
                    $table = ArgvInput::get();
                }
            }

            $class = '\Orm\Entity\\' . str_replace('_', '', ucfirst(strtolower($table)));

            if (class_exists($class)) {
                /** @var $entity \Gcs\Framework\Core\Orm\Entity\Entity */
                $entity = new $class();

                foreach ($entity->fields() as $field) {
                    if ($field->foreign != null && $field->foreign->type() == ForeignKey::MANY_TO_MANY) {
                        $class = '\Orm\Entity\\' . $field->foreign->referenceEntity();

                        if (class_exists($class)) {
                            /** @var $referencedEntity \Gcs\Framework\Core\Orm\Entity\Entity */
                            $referencedEntity = new $class();

                            /** We generate the linking table name */
                            $current = ucfirst($entity->name()) . ($entity->primary());
                            $reference = ucfirst($referencedEntity->name()) . ($field->foreign->referenceField());
                            $tableNames = [$current, $reference];
                            sort($tableNames, SORT_STRING);
                            $tableName = $tableNames[0] . strtolower($tableNames[1]);

                            $t = new Template('.app/system/module/orm/manytomany', 'gcsEntity_' . $table, '0');
                            $t->assign(['table' => $tableName, 'entity1' => $entity->name(), 'entity2' => $referencedEntity->name(), 'field1' => $entity->primary(), 'field2' => $field->foreign->referenceField()]);

                            $query = $t->show(Template::TPL_COMPILE_TO_STRING);

                            $sql = new Sql();
                            $sql->query('create-table-manytomany', $query);
                            $sql->fetch('create-table-manytomany', Sql::PARAM_NORETURN);

                            echo ' - the many to many table "' . $tableName . '" has been successfully created' . "\n";

                            $this->addEntity($tableName);
                        }
                        else {
                            echo ' - this Entity "' . $field->foreign->referenceEntity() . '" does not exist';
                        }
                    }
                }
            }
            else {
                echo ' - this Entity "' . $table . '" does not exist';
            }
        }
        else {
            echo ' - you\'re not logged to any database';
        }
    }
}