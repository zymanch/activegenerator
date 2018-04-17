<?php
namespace ActiveGenerator\generator;


class ScriptHelper {

    public $baseClass = 'ActiveRecord\db\ActiveRecord';
    public $queryBaseClass = 'ActiveRecord\db\ActiveQuery';
    public $namespace = 'Model';
    public $path = '';

    /**
     * @param string $tables Example: shared:website,rest_query,script_log;geoip:geo_zone
     */
    public function generate(\PDO $db, $tables) {
        $tables = explode(';',$tables);
        $generator = new Generator($db);
        $generator->baseClass = $this->baseClass;
        $generator->queryBaseClass = $this->queryBaseClass;
        $generator->namespace = $this->namespace;
        $generator->path = $this->path;
        foreach ($tables as $databaseAndTables) {
            $databaseAndTables = explode(':',$databaseAndTables,2);
            $database = $databaseAndTables[0];
            if (isset($databaseAndTables[1])) {
                $tables = array_filter(explode(',', $databaseAndTables[1]));
            } else {
                $tables = $this->_getTables($db, $database);
            }
            $database = new GeneratorDatabase($database);
            foreach ($tables as $table) {
                $database->addTable($table);
            }
            $generator->addDatabase($database);
        }
        $generator->generate();
    }

    protected function _getTables(\PDO $db, $database) {
        $prepare = $db->prepare('show tables from '.$database);
        $prepare->execute();
        return $prepare->fetchAll(\PDO::FETCH_COLUMN);
    }

}