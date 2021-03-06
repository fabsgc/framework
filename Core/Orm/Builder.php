<?php
/*\
 | ------------------------------------------------------
 | @file : Builder.php
 | @author : Fabien Beaujean
 | @description : Query Builder
 | @version : 3.0 bêta
 | ------------------------------------------------------
\*/

namespace Gcs\Framework\Core\Orm;

use Gcs\Framework\Core\Collection\Collection;
use Gcs\Framework\Core\Exception\MissingEntityException;
use Gcs\Framework\Core\Facade\Facades;
use Gcs\Framework\Core\Orm\Entity\ForeignKey;
use Gcs\Framework\Core\Sql\Sql;

/**
 * Class Builder
 * @method Entity
 * @package Gcs\Framework\Core\Orm
 */
class Builder {
    use Facades;

    const QUERY_SELECT      = 0;
    const QUERY_DISTINCT    = 1;
    const QUERY_RAW         = 2;
    const QUERY_COUNT       = 3;
    const JOIN_LEFT         = 'LEFT JOIN';
    const JOIN_RIGHT        = 'RIGHT JOIN';
    const JOIN_INNER        = 'INNER JOIN';
    const JOIN_FULL         = 'FULL JOIN';
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
     * the Entity used to make the query
     * @var \Gcs\Framework\Core\Orm\Entity\Entity
     */

    protected $_entity;

    /**
     * We use an object format for the field : Post.id, so we must know which tables are used in the query
     * @var string[]
     */

    protected $_entities = [];

    /**
     * the type (SELECT, SELECT DISTINCT)
     * @var integer
     */

    protected $_type = self::QUERY_SELECT;

    /**
     * token, to have unique cache file
     * @var string
     */

    protected $_token = '';

    /**
     * Constructor
     * @access public
     * @param $entity \Gcs\Framework\Core\Orm\Entity\Entity
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function __construct($entity) {
        $this->_entity = $entity;
        $this->_token = rand(0, 10000);
        array_push($this->_entities, str_replace('Orm\Entity\\', '', get_class($entity)));

        return $this;
    }

    /**
     * create a select distinct query
     * @access public
     * @param $distinct string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function findDistinct($distinct) {
        $this->_detectEntity($distinct);
        $this->_distinct = $distinct;
        $this->_type = self::QUERY_DISTINCT;
        $this->_getSelect();

        return $this;
    }

    /**
     * detect new Entity in the query
     * @access public
     * @param $query string
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    protected function _detectEntity($query) {
        preg_replace_callback('`([a-zA-Z]+)\.([a-zA-Z]+)`sU', ['System\Orm\Builder', '_detectEntityCallback'], $query);
    }

    /**
     * When you use fetch, it generates the SELECT .....
     * @access public
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    protected function _getSelect() {
        $fields = $this->_entity->fields();

        switch ($this->_type) {
            case self::QUERY_SELECT :
                $this->_query .= 'SELECT ';

                $nFields = count($fields);
                $i = 0;

                //We add all fields to the select
                foreach ($fields as $value) {
                    //We mustn't add fields which don't exist in the SQL table
                    if ($value->foreign == null || in_array($value->foreign->type(), [ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE])) {
                        $this->_query .= $this->_entity->name() . '.' . $value->name . ' AS ' . $this->_entity->name() . '_' . $value->name;

                        if ($i < $nFields - 1) {
                            $this->_query .= ', ';
                        }
                    }
                    //To optimize the relation ONE TO MANY, we replace the join value by the number of element in this join
                    else if ($value->foreign != null && $value->foreign->type() == ForeignKey::ONE_TO_MANY) {
                        $currentEntity = $value->foreign->entity();
                        $currentField = $value->foreign->field();
                        $referenceEntity = $value->foreign->referenceEntity();
                        $referenceField = $value->foreign->referenceField();

                        $this->_query .= '(SELECT COUNT(*) FROM ' . $referenceEntity . ' WHERE ' . $referenceEntity . '.' . $referenceField . ' = ' . $currentEntity . '.' . $currentField . ') AS count_one_' . $this->_entity->name() . '_' . $value->name;

                        if ($i < $nFields - 1) {
                            $this->_query .= ', ';
                        }
                    }
                    //To optimize the relation MANY TO MANY, we replace the join value by the number of element in this join
                    else if ($value->foreign != null && $value->foreign->type() == ForeignKey::MANY_TO_MANY) {
                        $currentEntity = $value->foreign->entity();
                        $currentField = $this->_entity->primary();
                        $referenceEntity = $value->foreign->referenceEntity();
                        $referenceField = $value->foreign->referenceField();
                        $current = strtolower($currentEntity . $currentField);
                        $reference = strtolower($referenceEntity . $referenceField);
                        $table = [$current, $reference];
                        sort($table, SORT_STRING);
                        $table = ucfirst($table[0] . $table[1]);

                        $this->_query .= '(SELECT COUNT(*) FROM ' . $table . ' WHERE ' . $table . '.' . $currentEntity . '_' . $currentField . ' = ' . $currentEntity . '.' . $currentField . ') AS count_many_' . $this->_entity->name() . '_' . $value->name;

                        if ($i < $nFields - 1) {
                            $this->_query .= ', ';
                        }
                    }

                    $i++;
                }

                $this->_query = preg_replace('#^(.*)(, )$#isU', '$1', $this->_query);

                //if some fields have a relation one to one or many to one, we had a join
                foreach ($fields as $value) {
                    if ($value->foreign != null && in_array($value->foreign->type(), [ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE])) {
                        $this->_query .= ', ';

                        $class = $this->_getTableEntity($value->foreign->referenceEntity());

                        $fieldsRelation = $class->fields();
                        $nFieldsRelation = count($fieldsRelation);
                        $i = 0;

                        foreach ($fieldsRelation as $relation) {
                            if ($relation->foreign == null || in_array($relation->foreign->type(), [ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE])) {
                                $this->_query .= $value->name . '_' . $class->name() . '.' . $relation->name . ' AS ' . $value->name . '_' . $class->name() . '_' . $relation->name;

                                if ($i < $nFieldsRelation - 1) {
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
                $primaryColumn = $this->_entity->name() . '.' . $this->_entity->fields()[$this->_entity->primary()]->name;

                if (preg_match('(' . $primaryColumn . ')', $this->_distinct)) {
                    $this->_query .= 'SELECT DISTINCT ' . $this->_distinct . ' ';
                }
                else {
                    $this->_query .= 'SELECT DISTINCT ' . $this->_distinct . ', ' . $primaryColumn . ' ';
                }
                break;

            case self::QUERY_COUNT :
                $this->_query .= 'SELECT COUNT(*) ';
                break;
        }

        $this->_query .= ' FROM ' . $this->_entity->name();

        /** If there are relations like one to one, many to one or many to many */
        foreach ($fields as $value) {
            if ($value->foreign != null && in_array($value->foreign->type(), [ForeignKey::ONE_TO_ONE, ForeignKey::MANY_TO_ONE])) {
                $entity = $value->foreign->entity();
                $field = $value->foreign->field();
                $referenceEntity = $value->foreign->referenceEntity();
                $referenceField = $value->foreign->referenceField();
                $this->join($value->foreign->join(), $referenceEntity, $entity . '.' . $field . ' = ' . $field . '_' . $referenceEntity . '.' . $referenceField, $field);
            }
            else if ($value->foreign != null && in_array($value->foreign->type(), [ForeignKey::MANY_TO_MANY])) {
                //We add here two join (relation table and table linked)
                $currentEntity = $value->foreign->entity();
                $currentField = $this->_entity->primary();
                $referenceEntity = $value->foreign->referenceEntity();
                $referenceField = $value->foreign->referenceField();

                $current = strtolower($currentEntity . $currentField);
                $reference = strtolower($referenceEntity . $referenceField);
                $table = [$current, $reference];
                sort($table, SORT_STRING);
                $table = ucfirst($table[0] . $table[1]);

                $this->join($value->foreign->join(), $table, $table . '.' . $this->_entity->name() . '_' . $currentField . ' = ' . $this->_entity->name() . '.' . $currentField);
                $this->join($value->foreign->join(), $value->foreign->referenceEntity(), $table . '.' . $referenceEntity . '_' . $referenceField . ' = ' . $referenceEntity . '.' . $referenceField);
            }
        }
    }

    /**
     * return an Entity from his name
     * @access protected
     * @param $entity string
     * @throws MissingEntityException
     * @return \Gcs\Framework\Core\Orm\Entity\Entity
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    protected function _getTableEntity($entity = '') {
        /** @var $class \Gcs\Framework\Core\Orm\Entity\Entity */

        $entity = ucfirst(preg_replace_callback("/(?:^|_)([a-z])/", function ($matches) {
            return strtoupper($matches[1]);
        }, $entity));

        $className = '\Orm\Entity\\' . ucfirst($entity);

        if (class_exists($className)) {
            return self::Entity()->$entity();
        }
        else {
            throw new MissingEntityException('The Entity ' . $entity . ' does not exist');
        }
    }

    /**
     * add join
     * @access public
     * @param $type      string
     * @param $table     string
     * @param $on        string
     * @param $reference string : reference field
     * @throws MissingEntityException
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function join($type = self::JOIN_INNER, $table, $on, $reference = '') {
        $class = $this->_getTableEntity($table);

        if ($table != $class->name()) {
            $table = $class->name();
        }

        $this->_detectEntity($on);

        if ($reference != '') {
            $this->_query .= ' ' . $type . ' ' . $table . ' AS ' . $reference . '_' . $table . ' ON ' . $on;
        }

        return $this;
    }

    /**
     * create a query without sql completion
     * @access public
     * @param $query string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function findRaw($query) {
        $this->_type = self::QUERY_RAW;
        $this->_query = $query;

        return $this;
    }

    /**
     * create a select count query
     * @access public
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function findCount() {
        $this->_type = self::QUERY_COUNT;
        $this->_getSelect();

        return $this;
    }

    /**
     * add and where clause
     * @access public
     * @param $where string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function andWhere($where) {
        $this->_detectEntity($where);
        $this->_query .= ' AND ' . $where;

        return $this;
    }

    /**
     * add or where clause
     * @access public
     * @param $where string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function orWhere($where) {
        $this->_detectEntity($where);
        $this->_query .= ' OR ' . $where;

        return $this;
    }

    /**
     * add order by
     * @access public
     * @param $orderBy string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function orderBy($orderBy) {
        $this->_detectEntity($orderBy);
        $this->_query .= ' ORDER BY ' . $orderBy;

        return $this;
    }

    /**
     * add group by
     * @access public
     * @param $groupBy string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function groupBy($groupBy) {
        $this->_detectEntity($groupBy);
        $this->_query .= ' GROUP BY ' . $groupBy;

        return $this;
    }

    /**
     * add having
     * @access public
     * @param $having string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function having($having) {
        $this->_detectEntity($having);
        $this->_query .= ' HAVING ' . $having;

        return $this;
    }

    /**
     * add limit
     * @access public
     * @param $offset integer
     * @param $number integer
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function limit($offset, $number) {
        $limit = rand(0, 50);
        $this->_query .= ' LIMIT :limit_offset_' . $limit . ', :limit_number_' . $limit;

        $this->vars('limit_offset_' . $limit, [$offset, Sql::PARAM_INT]);
        $this->vars('limit_number_' . $limit, [$number, Sql::PARAM_INT]);

        return $this;
    }

    /**
     * add variables to the instance
     * @access public
     * @param $var  mixed : contain the list of the variable that will be used in the queries.
     *              first syntax  : array('id' => array(31, Sql::PARAM_INT), 'pass' => array("fuck", sql::PARAM_STR))
     *              second syntax : array('id' => 31, 'pass' => "fuck"). If you don't define the type of the variable, the class will assign itself the correct type
     *              If you have only one variable to pass, you can use the 2/3 parameters form
     *              first syntax  : ('id', 'value')
     *              second syntax : ('id', 'value', Sql::PARAM_INT)
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function vars($var) {
        if (is_array($var)) {
            foreach ($var as $key => $valeur) {
                $this->_vars[$key] = $valeur;
            }
        }
        else if (func_num_args() == 2) {
            $args = func_get_args();
            $this->_vars[$args[0]] = $args[1];
        }

        else if (func_num_args() == 3) {
            $args = func_get_args();
            $this->_vars[$args[0]] = [$args[1], $args[2]];
        }

        return $this;
    }

    /**
     * add raw
     * @access public
     * @param $raw string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function raw($raw) {
        $this->_query .= ' ' . $raw;

        return $this;
    }

    /**
     * fetch the current query
     * @access public
     * @param $return integer
     * @return \Gcs\Framework\Core\Collection\Collection|\Gcs\Framework\Core\Orm\Entity\
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function fetch($return = self::RETURN_COLLECTION) {
        /** We replace Post.xx by post.xx */
        foreach ($this->_entities as $entity) {
            $class = $this->_getTableEntity($entity);

            if ($entity != $class->name()) {
                $this->_query = preg_replace('#(.*)' . $entity . '\.(.*)#isU', '$1' . $class->name() . '.$2', $this->_query);
            }
        }

        /** Query execution */
        $sql = new Sql();
        $sql->vars($this->_vars);
        $sql->query('orm-' . $this->_token, $this->_query);

        if ($this->_type == self::QUERY_COUNT) {
            return $sql->fetch('orm-' . $this->_token, Sql::PARAM_FETCHCOLUMN);
        }
        else {
            $sql->fetch('orm-' . $this->_token, Sql::PARAM_FETCH);
        }

        $collection = $sql->data($this->_getEntityName($this->_entity->name()));

        /** We can do SELECT OR SELECT DISTINCT OR SELECT RAW */
        if (in_array($this->_type, [self::QUERY_DISTINCT, self::QUERY_RAW])) {
            $nLines = $collection->count();
            $in = '';

            /** @var $value \Gcs\Framework\Core\Orm\Entity\Entity */
            foreach ($collection as $key => $value) {
                $in .= $value->fields()[$value->primary()]->value;

                if ($key < $nLines - 1) {
                    $in .= ', ';
                }
            }

            $builder = new Builder($this->_entity);
            $collection = $builder->find()->where($this->_entity->name() . '.' . $this->_entity->primary() . ' IN (' . $in . ')')->fetch();
        }

        /** If we have fields with a relation ONE TO MANY OR MANY TO MANY */
        foreach ($this->_entity->fields() as $field) {
            if ($field->foreign != null && in_array($field->foreign->type(), [ForeignKey::ONE_TO_MANY, ForeignKey::MANY_TO_MANY])) {
                /** we loop through the results to add each time */
                switch ($field->foreign->type()) {
                    case ForeignKey::ONE_TO_MANY :
                        $collection = $this->_dataOneToMany($field, $collection);
                        break;

                    case ForeignKey::MANY_TO_MANY :
                        $collection = $this->_dataManyToMany($field, $collection);
                        break;
                }
            }
        }

        if ($return == self::RETURN_ENTITY && $collection->count() <= 1) {
            return $collection->first();
        }
        else {
            return $collection;
        }
    }

    /**
     * return an Entity name
     * @access protected
     * @param $entity string
     * @throws MissingEntityException
     * @return string
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    protected function _getEntityName($entity = '') {
        /** @var $class \Gcs\Framework\Core\Orm\Entity\Entity */

        $entity = ucfirst(preg_replace_callback("/(?:^|_)([a-z])/", function ($matches) {
            return strtoupper($matches[1]);
        }, $entity));

        $className = '\Orm\Entity\\' . ucfirst($entity);

        if (class_exists($className)) {
            return $entity;
        }
        else {
            throw new MissingEntityException('The Entity ' . $entity . ' does not exist');
        }
    }

    /**
     * add where clause
     * @access public
     * @param $where string
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function where($where) {
        $this->_detectEntity($where);
        $this->_query .= ' WHERE ' . $where;

        return $this;
    }

    /**
     * create a select query
     * @access public
     * @return \Gcs\Framework\Core\Orm\Builder
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function find() {
        $this->_type = self::QUERY_SELECT;
        $this->_getSelect();

        return $this;
    }

    /**
     * If the Entity has at least one to many relation, we had a collection to the right field
     * @access public
     * @param $field \Gcs\Framework\Core\Orm\Entity\Field : foreign key field
     * @param $collection \Gcs\Framework\Core\Collection\Collection
     * @return \Gcs\Framework\Core\Collection\Collection
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    protected function _dataOneToMany($field, $collection) {
        /**
         * Instead of making only one query by line, we assemble all the lines IDs and we make a big query (time saving)
         * First, we make the query, using IN()
         * Then, we loop through the query lines and we add to each line her data from join
         */

        $in = '';
        $inVars = [];
        $currentField = $field->foreign->field();
        $referenceEntity = $field->foreign->referenceEntity();
        $referenceField = $field->foreign->referenceField();
        $fieldFormName = lcfirst($referenceEntity) . '.' . lcfirst($referenceField);

        /** @var $line \Gcs\Framework\Core\Orm\Entity\Entity */
        foreach ($collection as $key => $line) {
            $in .= ' :join' . $key . ',';
            $inVars['join' . $key] = $line->get($currentField);
        }

        $in = trim($in, ',');

        /** @var $datasJoin \Gcs\Framework\Core\Collection\Collection */
        $datasJoin = self::Entity()->$referenceEntity()->find()->where($fieldFormName . ' IN(' . $in . ')')->orderBy($fieldFormName . ' ASC')->vars($inVars)->fetch();

        $data = null;

        /** @var $line \Gcs\Framework\Core\Orm\Entity\Entity */
        foreach ($collection as $line) {
            $count = $line->get($field->name);
            $data = new Collection();

            /** @var $dataJoin \Gcs\Framework\Core\Orm\Entity\Entity */
            foreach ($datasJoin as $key2 => $dataJoin) {
                /**
                 * The lines are ordered by reference Entity ID, so when we find the first line, we have just to
                 * get the following lines thanks to $count
                 */
                if ($count > 0 && $dataJoin->get($referenceField)->get($currentField) == $line->get($currentField)) {
                    $data = new Collection($datasJoin->getRange($key2, $count));
                    $datasJoin->deleteRange($key2, $count);
                    break;
                }
            }

            $line->set($field->name, $data);
        }

        /** @var $line \Gcs\Framework\Core\Orm\Entity\Entity */
        /*foreach($collection as $line){
            $currentEntity = $field->foreign->Entity();
            $currentField  = $field->foreign->field();
            $referenceEntity = $field->foreign->referenceEntity();

            $where = $currentEntity.'.'.$currentField.' = '.$line->get($line->primary());
            $data = self::Entity()->$referenceEntity()->find()->where($where)->fetch();
            $line->set($field->name, $data);
        }*/

        return $collection;
    }

    /**
     * If the Entity has at least many to many relation, we had a collection to the right field
     * @access public
     * @param $field \Gcs\Framework\Core\Orm\Entity\Field
     * @param $collection \Gcs\Framework\Core\Collection\Collection
     * @return \Gcs\Framework\Core\Collection\Collection
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    protected function _dataManyToMany($field, $collection) {
        /**
         * Instead of making only one query by line, we assemble all the lines IDs and we make a big query (time saving)
         * First, we make the query, using IN()
         * Then, we loop through the query lines and we add to each line her data from join
         */

        $in = '';
        $inVars = [];

        /** we built the relation table name : $table */
        $currentEntity = $field->foreign->entity();
        $currentField = $this->_entity->primary();
        $referenceEntity = $field->foreign->referenceEntity();
        $referenceField = $field->foreign->referenceField();
        $fieldFormName = lcfirst($this->_entity->name()) . '_' . lcfirst($currentField) . '_' . lcfirst($this->_entity->name()) . '.' . lcfirst($currentField);
        $fieldRelation = $this->_getTableEntity($referenceEntity)->name() . '_' . $referenceField;

        /** We must know the join table name */
        $current = strtolower($currentEntity . $currentField);
        $reference = strtolower($referenceEntity . $referenceField);
        $table = [$current, $reference];
        sort($table, SORT_STRING);
        $table = ucfirst($table[0] . $table[1]);

        /** @var $line \Gcs\Framework\Core\Orm\Entity\Entity */
        foreach ($collection as $key => $line) {
            $in .= ' :join' . $key . ',';
            $inVars['join' . $key] = $line->get($currentField);
        }

        $in = trim($in, ',');

        /** @var $datasJoin \Gcs\Framework\Core\Collection\Collection */
        $datasJoin = self::Entity()->$table()->find()->where($fieldFormName . ' IN(' . $in . ')')->orderBy($fieldFormName . ' ASC')->vars($inVars)->fetch();

        $data = null;

        /** @var $line \Gcs\Framework\Core\Orm\Entity\Entity */
        foreach ($collection as $line) {
            $count = $line->get($field->name);
            $data = new Collection();

            /** @var $dataJoin \Gcs\Framework\Core\Orm\Entity\Entity */
            foreach ($datasJoin as $key2 => $dataJoin) {
                /**
                 * The lines are ordered by reference Entity ID, so when we find the first line, we have just to
                 * get the following lines thanks to $count
                 */

                if ($count > 0) {
                    $data->add($dataJoin->get($fieldRelation));
                    $datasJoin->delete($key2);
                }
            }

            $line->set($field->name, $data);
        }

        return $collection;
    }

    /**
     * Destructor
     * @access public
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    public function __destruct() {
    }

    /**
     * detect new Entity in the query [callback]
     * @access public
     * @param $m string[]
     * @return void
     * @since 3.0
     * @package Gcs\Framework\Core\Orm
     */

    protected function _detectEntityCallback($m) {
        if (!in_array($m[1], $this->_entities)) {
            array_push($this->_entities, $m[1]);
        }
    }
}