<?php
class Database{
    private static $connectionStatus;

    private	function __construct() {/* ... @return Singleton */}
    private function __clone(){/* ... @return Singleton */ }
    private function __wakeup(){/* ... @return Singleton */ }

    private static function errorDataBase(mysqli $object){
        var_dump($object);
        echo "Request failed! Error: {$object->error}. Error number: <strong>{$object->errno}</strong>";
        exit();
    }

    public static function checkConnect(){
        return !(isset(self::$connectionStatus)) ? self::$connectionStatus = new Database : self::$connectionStatus;
    }

    public function connectDatabase(){
        $mysqli = new mysqli(HOST, USERNAME, PASSWORD, DATABASE);
        if ($mysqli->connect_errno)
            self::errorDataBase($mysqli);
        return $mysqli;
    }

    public function truncateTable(mysqli $db, $table){
        if(($query = $db->query("TRUNCATE TABLE {$table}")) === FALSE)
            self::errorDataBase($db);
    }

    private static function queryParameters(array $table_param)
    {
        $param = '';
        $count = count($table_param);
        $i = 0;

        foreach ($table_param as $value => $item) {
            $i++;

            $item = htmlentities($item, ENT_QUOTES);

            if($count > 0 && $i !== $count)
                $param .= "$value = '$item' , ";
            else
                $param .= "$value = '$item'";
        }

        return $param;
    }

    public function selectData(mysqli $db, $table, $requestData){

        if(($query = $db->query("SELECT {$requestData} FROM {$table}")) === FALSE)
            self::errorDataBase($db);
        return $query;
    }

    public function insertData(mysqli $db, $table, array $requestData){
        $data = self::queryParameters($requestData);

        if(($query = $db->query("INSERT INTO {$table} SET {$data}")) === FALSE )
            self::errorDataBase($db);
    }

    public function updateData(mysqli $db, $table, $searchKey, array $requestData){
        $data = self::queryParameters($requestData);

        if(($query = $db->query("UPDATE {$table} SET {$data} WHERE GAME_ID = '{$searchKey}'")) === FALSE )
            self::errorDataBase($db);
    }

    public function leftJoin(mysqli $db, $table_1, $table_2, $requestData, $select = '*') {
        if(($query = $db->query("SELECT {$table_2}.$select FROM {$table_2} 
                                 LEFT JOIN {$table_1} ON ({$table_2}.$requestData = {$table_1}.$requestData)
                                 WHERE {$table_1}.$requestData IS NULL")) === FALSE )
            self::errorDataBase($db);

        return $query;
    }
}