<?php

/**
 * This class lets you define your SQL queries in separate logic files 
 * and then comfortably import them in right place in your code. 
 * 
 * Keeps code clean and maintainable.
 *
 * @author jozef_cipa
 *
 */
class SQLQuery{

    /**
     * @var array Array of files in directory
     */
    private $sqlFiles;

    /**
     * @var string Directory path
     */
    private static $dir = './sql';

	/**
	 * @var string Name of part from sql file
	 */
    private $sqlPart;

    private function __construct($sqlPart)
    {
        $this->sqlFiles = glob(self::$dir  . "/*.sql");
        $this->sqlPart = $sqlPart;
    }

    /**
     * Change default sql directory path
     * @param string $dir New path
     */
    public static function setSqlDir($dir){
        self::$dir = $dir;
    }

    /**
     * Return new object and set sql part
     * @param  string $sqlPart Name of part from sql file
     * @return SQLQuery Return new object
     */
    public static function import($sqlPart = '*'){
        
        return new self($sqlPart);
    }

    /**
     * Return whole sql string or its part from given filename
     * @param  string $filename Name of sql file
     * @return Return query string
     */
    public function from($filename)
    {

        $filename .= '.sql';
        
        if (!$this->fileExists($filename))
            throw new Exception("File $filename doesn't exist in directory " . self::$dir);
        
        if ($this->sqlPart == '*')
            return $this->getSQL($filename);
        else
            return $this->getSQLPart($this->sqlPart, $filename);

    }

    /**
     * Check if given filename exists in sql directory
     * @param string $filename Name of sql file
     * @return boolean 
     */
    private function fileExists($filename){
        
        $files = array_map(function($filename){
            return basename($filename);
        }, $this->sqlFiles);
        
        return in_array($filename, $files);
    }

    /**
     * Return content of file
     * @param  string $filename File name 
     * @return string Return content of file
     */
    private function readFile($filename){
        return file_get_contents(self::$dir . '/' . $filename);
    }


    /**
     * Return whole sql string 
     *
     * @param $filename string File name
     * @return string Return whole sql string
     */
    private function getSQL($filename){
        return $this->readFile($filename);
    }

    /**
     * Return sql part from file by $sqlPartName
     * @param  string $sqlPartName Name of sql part
     * @param  string $filename File name
     * @return string Return sql string
     */
    private function getSQLPart($sqlPartName, $filename){

        $sqlFile = $this->getSQL($filename);

        $parts = preg_split("/--@SQLName(.*)\\n/", $sqlFile);

        //remove first empty string
        array_shift($parts);

        //remove spaces from start and end 
        $parts = array_map(function($item){
            return trim($item);
        }, $parts);

        preg_match_all("/--@SQLName (.*)\\n/", $sqlFile, $partsNames);

        $partIndexByName = array_search($sqlPartName, $partsNames[1]);

        $sql = $parts[$partIndexByName];
        
        if(!$sql)
            throw new Exception("SQL part with given name: $sqlPartName doesn't exist in $filename.");
        
        return $sql;
    }
}
