<?php
/**
 * Created by PhpStorm.
 * User: ZyManch
 * Date: 25.04.2017
 * Time: 17:17
 */
include __DIR__.'/vendor/autoload.php';

class Cache  implements ArrayAccess {

    protected $_data = [];
    protected $_path;

    public function __construct() {
        $this->_path = __DIR__.'/cache.dat';
        if (file_exists($this->_path)) {
            $this->_data = unserialize(file_get_contents($this->_path));
        }
    }

    public function __destruct() {
        file_put_contents($this->_path,serialize($this->_data));
    }

    public function offsetExists($offset) {
        return isset($this->_data[$offset]);
    }

    public function offsetGet($offset) {
        return $this->_data[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->_data[$offset]);
    }
}

$db = new PDO('mysql:host=sanddb.gtflixtv.com;dbname=shared', 'develop','develop@box');

$tables = 'gtf:video,video_file,file_object_type,file_object_type_family';
$tables = explode(';',$tables);
$generator = new \ActiveGenerator\generator\Generator($db);
foreach ($tables as $databaseAndTables) {
    $databaseAndTables = explode(':',$databaseAndTables,2);
    $database = $databaseAndTables[0];
    if (isset($databaseAndTables[1])) {
        $tables = array_filter(explode(',', $databaseAndTables[1]));
    } else {
        $tables = _getTables($db, $database);
    }
    $database = new \ActiveGenerator\generator\GeneratorDatabase($database);
    foreach ($tables as $table) {
        $database->addTable($table);
    }
    $generator->addDatabase($database);
}
$generator->generate(__DIR__.'/src/Model');

function _getTables(\PDO $db, $database) {
    $prepare = $db->prepare('show tables from '.$database);
    $prepare->execute();
    return $prepare->fetchAll(\PDO::FETCH_COLUMN);
}