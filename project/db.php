<?php
// Update account info accordingly
$dsn = "mysql:host=localhost;port=3306;dbname=project;charset=utf8mb4" ;
$user = "std" ;
$pass = "" ;

try {
    $db = new PDO($dsn, $user, $pass) ;
} catch( PDOException $ex) {
    echo "<p> Connection Error".$ex->getMessage()."<p>";
    exit ;
}