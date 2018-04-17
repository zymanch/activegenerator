<?php

namespace ActiveGenerator\generator;

class Generator {

    /** @var  GeneratorDatabase[] */
    protected $_databases;

    /** @var  \PDO */
    protected $_db;

    public $baseClass = 'ActiveRecord\db\ActiveRecord';
    public $queryBaseClass = 'ActiveRecord\db\ActiveQuery';
    public $namespace = 'Model';
    public $prefix = 'C';
    public $path = '';
    public $sub = 'Base';

    public function __construct(\PDO $db) {
        $this->_db = $db;
    }

    public function addDatabase(GeneratorDatabase $database) {
        $this->_databases[] = $database;
    }

    public function generate() {
        $absolutePath = realpath($this->path);
        if (!$this->path || !$absolutePath) {
            throw new \Exception('Folder not found:'.$this->path);
        }
        $namespace = trim($this->namespace,'\\');
        $absolutePath = rtrim($absolutePath,'/');
        @exec('rm -fR ' . $absolutePath . '/Base');
        $generator = $this->_getGenerator($namespace, $absolutePath);
        $files = $generator->generate();
        /** @var \ActiveGenerator\gii\CodeFile $file */
        foreach ($files as $file) {
            $success = $file->save();
            if ($success !== true) {
                printf("File %s: %s\n",$file->path, $success);
            } else {
                printf("File %s: ok\n",$file->path);
            }
        }
        @exec(sprintf('git add %s',$absolutePath.'/'));
    }

    /**
     * @param GeneratorDatabase $database
     * @param $namespace
     * @param $path
     * @return \ActiveGenerator\gii\generators\model\Generator
     */
    protected function _getGenerator($namespace, $path) {
        $generator = new \ActiveGenerator\gii\generators\model\Generator();
        $generator->setDbConnection($this->_db);
        $generator->ns = $namespace;
        $generator->path = $path;
        $generator->sub  = $this->sub;
        $generator->prefix  = $this->prefix;
        $generator->baseClass = $this->baseClass;
        $generator->queryBaseClass = $this->queryBaseClass;
        foreach ($this->_databases as $currentDatabase) {
            foreach ($currentDatabase->getTables() as $tableName) {
                $relationTableName = $currentDatabase->getDatabase().'.'.$tableName;
                $generator->classNames[$relationTableName] = $this->_tableToClass($tableName);
            }
        }
        return $generator;
    }

    protected function _tableToClass($table) {
        return implode('', array_map('ucfirst', explode('_', $table)));
    }

    protected function _getOriginFile($namespace, $class, $extends) {
        return '<?'."php\n\nnamespace $namespace;\n\n".
            "class $class extends \\$namespace\\Base\\$extends {\n\n}";
    }

}


