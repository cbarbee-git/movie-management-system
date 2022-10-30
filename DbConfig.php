<?php
include_once('DotEnv.php');
function getdbconnection(){
    (new APP_ENV\DotEnv(__DIR__ . '/.env'))->load();
    $hostname = getenv('DB_HOST');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $dbname= getenv('DB_NAME');
    try{
        $conn = new PDO("mysql:host=$hostname;dbname=$dbname",$username,$password);
        $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        return $conn;
    }catch(PDOException $e){
        echo "Connection Failed: " . $e->getMessage();
    }
}
