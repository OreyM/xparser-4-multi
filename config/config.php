<?php
//PHP error settings
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('max_execution_time', '320000');

//DATA to connect to Local DataBase
define(HOST, 'localhost');
define(DATABASE, 'parser');
define(USERNAME, 'root');
define(PASSWORD, '');

//URL DEFINE
define(GAME_URL, 'https://www.microsoft.com');
//define(TOP_PAID, '/store/top-paid/games/xbox');
//define(BEST_RATED, '/store/best-rated/games/xbox');
//define(NEW_GAMES, '/store/new/games/xbox');
//define(TOP_FREE, '/store/top-free/games/xbox');
define(EXCHANGE_RATES_URL, 'http://ru.fxexchangerate.com/currency-exchange-rates.html');

//function autoloadClasses($className) {
//    $classPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $className . '.php';
//    if (file_exists($classPath)) {
//        require_once $classPath;
//        return true;
//    }
//    else{
//        echo "<p>The called Class - <strong>{$className}</strong> - does not exist!</p>";
//    }
//    return false;
//}

//spl_autoload_register('autoloadClasses');