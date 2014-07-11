<?php

error_reporting(E_ALL);

ini_set('display_errors', '1');

include 'database.php';
$pdo = new PDO("mysql:host=$db_address;dbname=$db_name", $db_username, $db_password);

session_start();
if (!isset($_POST['sentence']) ) {
    die("weird request, missing sentence");
} 

if (!isset($_POST['csrfToken']) ) {
    die("weird request, missing token");
} 


if (!isset($_POST['corpus'])) {
    die("weird request, missing corpus");
} 

$systems = get_system_list();

foreach ($systems as $system) {
    if (!isset($_POST['var_'.$system])) {
        die("weird request, missing var_".$system);
    }
}

if ($_POST['csrfToken'] !== $_SESSION['csrfToken']) {
    die("weird error, try again please");
}

$sent = $_POST['sentence'];
$corp = $_POST['corpus'];
$user = $_SESSION['user'];

$pdo->beginTransaction();

foreach($systems as $system) {
    $rank = $_POST['var_'.$system];
    $stmt = $pdo->prepare('
        INSERT INTO `hits` (`user`, `sentence`, `corpus`, `system`, `number`)
        VALUES (:user, :sentence, :corpus, :system, :number);
    ');
    $res = $stmt->execute(array("user"=>$user,"sentence"=>$sent, "corpus"=>$corp,
                         "system"=>$system,"number"=>$rank));
    if (!$res) {
        $pdo->rollBack();
        die ("MYSQL failed :/");
    }
}
$pdo->commit();


        header("Location: http://www.czerust.cz/mt/");

?>

