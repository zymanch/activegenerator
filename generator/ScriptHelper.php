<?php
namespace ActiveGenerator\generator;


class ScriptHelper {

    /**
     * @param string $tables Example: shared:website,rest_query,script_log;geoip:geo_zone
     */
    public function generate(\PDO $db, $dir, $tables) {
        $tables = explode(';',$tables);
        $generator = new Generator($db);
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
        $generator->generate('Model',$dir);
    }

    protected function _getTables(\PDO $db, $database) {
        $prepare = $db->prepare('show tables from '.$database);
        $prepare->execute();
        return $prepare->fetchAll(\PDO::FETCH_COLUMN);
    }

}