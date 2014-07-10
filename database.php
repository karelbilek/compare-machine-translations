<?php

$db_address="localhost";
$db_name="testy";
$db_username="root";
$db_password="nenapisu";


function get_system_list() {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT `id` FROM systems
    ');
    $stmt->execute();

    $res=array();
    while ($row = $stmt->fetch()) {
        $res[]=$row['id'];
    }
    
    return $res;
}


?>
