<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ActiveGenerator\gii\generators\model;

use ActiveGenerator\db\Schema;
use ActiveGenerator\db\TableSchema;
use ActiveGenerator\gii\CodeFile;
use ActiveGenerator\helpers\Inflector;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \ActiveGenerator\gii\Generator
{

    /** @var  \PDO */
    protected $_connect;
    public $baseClass = 'ActiveGenerator\db\ActiveRecord';
    public $generateLabelsFromComments = false;
    public $queryClass;
    public $queryBaseClass = 'ActiveGenerator\db\ActiveQuery';
    public $classNames;

    public $sub;
    public $ns;
    public $prefix = 'C';

    public $path;


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class for the specified database table.';
    }



    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $relations = array_filter($relations);
        $schema = $this->getSchema();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $mainModelClassName = $this->generateMainClassName($tableName);
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = $this->generateQueryClassName($tableName);
            $mainQueryClassName = $this->generateMainQueryClassName($tableName);
            $peerClassName = $this->generatePeerClassName($tableName);
            $tableSchema = $schema->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'mainClassName' => $mainModelClassName,
                'queryClassName' => $queryClassName,
                'mainQueryClassName' => $mainQueryClassName,
                'sub' => $this->sub,
                'ns' => $this->ns,
                'peerClassName'  => $peerClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema, $peerClassName),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                $this->path .'/'.$this->sub. '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            $files[] = new CodeFile(
                $this->path.'/'.$this->sub . '/' . $queryClassName . '.php',
                $this->render('query.php', $params)
            );
            $files[] = new CodeFile(
                $this->path.'/'.$this->sub . '/' . $peerClassName . '.php',
                $this->render('peer.php', $params)
            );
            $originPath = $this->path.'/'.$mainModelClassName.'.php';
            if (!file_exists($originPath)) {
                $file = new CodeFile(
                    $originPath,
                    $this->render('mainModel.php', $params)
                );
                $files[] = $file;
            }
            $originQueryPath = $this->path.'/'.$mainQueryClassName.'.php';
            if (!file_exists($originQueryPath)) {
                $file = new CodeFile(
                    $originQueryPath,
                    $this->render('mainQuery.php', $params)
                );
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \ActiveGenerator\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * Generates validation rules for the specified table.
     * @param \ActiveGenerator\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table, $peerClassName)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $peerClassName.'::'.strtoupper($column->name);
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $peerClassName.'::'.strtoupper($column->name);
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $peerClassName.'::'.strtoupper($column->name);
                    break;
                case Schema::TYPE_FLOAT:
                case 'double': // Schema::TYPE_DOUBLE, which is available since ActiveRecord 2.0.3
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $peerClassName.'::'.strtoupper($column->name);
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $peerClassName.'::'.strtoupper($column->name);
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $peerClassName.'::'.strtoupper($column->name);
                    } else {
                        $types['string'][] = $peerClassName.'::'.strtoupper($column->name);
                    }
            }
        }
        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[[" . implode(", ", $columns) . "], '$type']";
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[[" . implode(", ", $columns) . "], 'string', 'max' => $length]";
        }

        $schema = $this->getSchema();

        // Unique indexes rules
        $uniqueIndexes = $schema->findUniqueIndexes($table);
        foreach ($uniqueIndexes as $uniqueColumns) {
            // Avoid validating auto incremental columns
            if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                $attributesCount = count($uniqueColumns);

                if ($attributesCount === 1) {
                    $rules[] = "[[" . $peerClassName.'::'.strtoupper($uniqueColumns[0]) . "], 'unique']";
                } elseif ($attributesCount > 1) {
                    $uniqueColumns = array_map('strtoupper',$uniqueColumns);
                    $columnsList = implode(", $peerClassName::", $uniqueColumns);
                    $rules[] = "[[$peerClassName::$columnsList], 'unique', 'targetAttribute' => [$peerClassName::$columnsList]]";
                }
            }
        }


        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $schema->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            if (!isset($this->classNames[$refTable])) {
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            $refPeerName = $this->generatePeerClassName($refTable);
            unset($refs[0]);
            $attributes = implode(", $peerClassName::", array_keys($refs));
            $targetAttributes = [];
            foreach ($refs as $key => $value) {
                $targetAttributes[] = "$peerClassName::".strtoupper($key)." => $refPeerName::".strtoupper($value);
            }
            $targetAttributes = implode(', ', $targetAttributes);
            $rules[] = "[[$peerClassName::".strtoupper($attributes)."], 'exist', 'skipOnError' => true, 'targetClass' => $refClassName::className(), 'targetAttribute' => [$targetAttributes]]";
        }

        return $rules;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     * @param \ActiveGenerator\db\TableSchema $table the table being checked
     * @param array $fks obtained from the checkJunctionTable() method
     * @param array $relations
     * @return array modified $relations
     */
    private function generateManyManyRelations($table, $fks, $relations)
    {
        $schema = $this->getSchema();
        $peerName = $this->generatePeerClassName($table->name);
        foreach ($fks as $pair) {
            list($firstKey, $secondKey) = $pair;
            $table0 = $firstKey[0];
            $table1 = $secondKey[0];
            unset($firstKey[0], $secondKey[0]);
            try {
                $className0 = $this->generateClassName($table0);
                $className1 = $this->generateClassName($table1);
            } catch (\Exception $e) {
                continue;
            }
            $peerName0 = $this->generatePeerClassName($table0);
            $peerName1 = $this->generatePeerClassName($table1);
            $table0Schema = $schema->getTableSchema($table0);
            $table1Schema = $schema->getTableSchema($table1);

            // @see https://github.com/yiisoft/yii2-gii/issues/166
            if ($table0Schema === null || $table1Schema === null) {
                continue;
            }

            $link = $this->generateRelationLink(array_flip($secondKey), $peerName1, $peerName);
            $viaLink = $this->generateRelationLink($firstKey, $peerName, $peerName0);
            $relationName = $this->generateRelationName($relations, $table0Schema, key($secondKey), true);
            $relations[$table0Schema->fullName][$relationName] = [
                "return \$this->hasMany($className1::className(), $link)->viaTable('"
                . $table->name . "', $viaLink);",
                $className1,
                true,
            ];

            $link = $this->generateRelationLink(array_flip($firstKey),$peerName0,$peerName);
            $viaLink = $this->generateRelationLink($secondKey, $peerName, $peerName1);
            $relationName = $this->generateRelationName($relations, $table1Schema, key($firstKey), true);
            $relations[$table1Schema->fullName][$relationName] = [
                "return \$this->hasMany($className0::className(), $link)->viaTable('"
                . $table->name . "', $viaLink);",
                $className0,
                true,
            ];
        }

        return $relations;
    }

    /**
     * @return string[] all db schema names or an array with a single empty string
     * @since 2.0.5
     */
    protected function getSchemaNames()
    {
        $result = [];
        foreach ($this->classNames as $tableName => $class) {
            $parts = explode('.',$tableName,2);
            $result[] = $parts[0];
        }
        return array_unique($result);
    }

    protected function getSchema() {
        $schema = new \ActiveGenerator\db\mysql\Schema();
        $schema->db = $this->getDbConnection();
        return $schema;
    }

    /**
     * @return array the generated relation declarations
     */
    protected function generateRelations() {
        $relations = [];
        $schemaNames = $this->getSchemaNames();
        $schema = $this->getSchema();
        foreach ($schemaNames as $schemaName) {
            foreach ($schema->getTableSchemas($schemaName) as $table) {
                if (!isset($this->classNames[$table->fullName])) {
                    continue;
                }
                $className = $this->generateMainClassName($table->fullName);
                $peerName = $this->generatePeerClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    if (!isset($this->classNames[$refTable])) {
                        continue;
                    }
                    $refTableSchema = $schema->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateMainClassName($refTable);
                    $refPeerName = $this->generatePeerClassName($refTable);
                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs), $refPeerName, $peerName);
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relations[$table->fullName][$relationName] = [
                        "return \$this->hasOne(\\$this->ns\\$refClassName::className(), $link);",
                        $refClassName,
                        false,
                    ];

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs, $peerName, $refPeerName);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][$relationName] = [
                        "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "(\\$this->ns\\$className::className(), $link);",
                        $className,
                        $hasMany,
                    ];
                }

                if (($junctionFks = $this->checkJunctionTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }
        return $relations;
    }

    /**
     * Determines if relation is of has many type
     *
     * @param TableSchema $table
     * @param array $fks
     * @return bool
     * @since 2.0.5
     */
    protected function isHasManyRelation($table, $fks)
    {
        $uniqueKeys = [$table->primaryKey];
        $uniqueKeys = array_merge($uniqueKeys, $this->getSchema()->findUniqueIndexes($table));
        foreach ($uniqueKeys as $uniqueKey) {
            if (count(array_diff(array_merge($uniqueKey, $fks), array_intersect($uniqueKey, $fks))) === 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generates the link parameter to be used in generating the relation declaration.
     * @param array $refs reference constraint
     * @return string the generated link parameter.
     */
    protected function generateRelationLink($refs, $peerName, $refPeerName)
    {
        $pairs = [];
        foreach ($refs as $a => $b) {
            $pairs[] = $peerName.'::'.strtoupper($a).' => '.$refPeerName.'::'.strtoupper($b);
        }

        return '[' . implode(', ', $pairs) . ']';
    }

    /**
     * Checks if the given table is a junction table, that is it has at least one pair of unique foreign keys.
     * @param \ActiveGenerator\db\TableSchema the table being checked
     * @return array|bool all unique foreign key pairs if the table is a junction table,
     * or false if the table is not a junction table.
     */
    protected function checkJunctionTable($table)
    {
        if (count($table->foreignKeys) < 2) {
            return false;
        }
        $uniqueKeys = [$table->primaryKey];
        $uniqueKeys = array_merge($uniqueKeys, $this->getSchema()->findUniqueIndexes($table));
        $result = [];
        // find all foreign key pairs that have all columns in an unique constraint
        $foreignKeys = array_values($table->foreignKeys);
        for ($i = 0; $i < count($foreignKeys); $i++) {
            $firstColumns = $foreignKeys[$i];
            unset($firstColumns[0]);

            for ($j = $i + 1; $j < count($foreignKeys); $j++) {
                $secondColumns = $foreignKeys[$j];
                unset($secondColumns[0]);

                $fks = array_merge(array_keys($firstColumns), array_keys($secondColumns));
                foreach ($uniqueKeys as $uniqueKey) {
                    if (count(array_diff(array_merge($uniqueKey, $fks), array_intersect($uniqueKey, $fks))) === 0) {
                        // save the foreign key pair
                        $result[] = [$foreignKeys[$i], $foreignKeys[$j]];
                        break;
                    }
                }
            }
        }
        return empty($result) ? false : $result;
    }

    /**
     * Generate a relation name for the specified table and a base name.
     * @param array $relations the relations being generated currently.
     * @param \ActiveGenerator\db\TableSchema $table the table schema
     * @param string $key a base name that the relation name may be generated from
     * @param bool $multiple whether this is a has-many relation
     * @return string the relation name
     */
    protected function generateRelationName($relations, $table, $key, $multiple)
    {
        if (!empty($key) && strcasecmp($key, 'id')) {
            if (substr_compare($key, 'id', -2, 2, true) === 0) {
                $key = rtrim(substr($key, 0, -2), '_');
            } elseif (substr_compare($key, 'id', 0, 2, true) === 0) {
                $key = ltrim(substr($key, 2, strlen($key)), '_');
            }
        }
        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        $i = 0;
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName . ($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName . ($i++);
        }

        return $name;
    }




    protected $tableNames;

    /**
     * @return array the table names that match the pattern specified by [[tableName]].
     */
    protected function getTableNames()
    {
        return array_keys($this->classNames);
    }

    /**
     * Generates a class name from the specified table name.
     * @param string $tableName the table name (which may contain schema prefix)
     * @param bool $useSchemaName should schema name be included in the class name, if present
     * @return string the generated class name
     */
    protected function generateMainClassName($tableName)
    {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }
        throw new \Exception('Table not needed:'.$tableName);
    }

    protected function generateClassName($tableName)
    {
        if (isset($this->classNames[$tableName])) {
            return $this->prefix.$this->classNames[$tableName];
        }
        throw new \Exception('Table not needed:'.$tableName);
    }

    /**
     * Generates a query class name from the specified model class name.
     * @param $tableName
     * @return string generated class name
     */
    protected function generateQueryClassName($tableName)
    {
        return $this->generateClassName($tableName).'Query';
    }

    protected function generateMainQueryClassName($tableName) {
        return $this->generateMainClassName($tableName).'Query';
    }

    protected function generatePeerClassName($tableName)
    {
        if (strpos($tableName,'.')) {
            $tableName = explode('.',$tableName);
            $tableName = $tableName[1];
        }
        return $this->prefix.implode('', array_map('ucfirst', explode('_', $tableName.'_peer')));
    }

    /**
     * @return \PDO Connection the DB connection as specified by [[db]].
     */
    public function getDbConnection()
    {
        return $this->_connect;
    }

    public function setDbConnection($db) {
        $this->_connect = $db;
    }

    /**
     * Checks if any of the specified columns is auto incremental.
     * @param \ActiveGenerator\db\TableSchema $table the table schema
     * @param array $columns columns to check for autoIncrement property
     * @return bool whether any of the specified columns is auto incremental.
     */
    protected function isColumnAutoIncremental($table, $columns)
    {
        foreach ($columns as $column) {
            if (isset($table->columns[$column]) && $table->columns[$column]->autoIncrement) {
                return true;
            }
        }

        return false;
    }
}
