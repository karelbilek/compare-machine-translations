<?php

error_reporting(E_ALL);

ini_set('display_errors', '1');

include 'database.php';
$pdo = new PDO("mysql:host=$db_address;dbname=$db_name", $db_username, $db_password);

session_start();

if (!isset($_POST['name']) or !isset($_POST['password'])) {
    die("weird request");
} 

$name = $_POST['name'];
$pass = $_POST['password'];


    $stmt = $pdo->prepare('
    SELECT * FROM users
    WHERE username = :name
    ');
    $stmt->execute(array('name'=>$name));

    $res = $stmt->fetch();
    if (!$res) {
        $chyba= "Uzivatel neexistuje.";
    }
    else {
        $pass_hash = $res['pass'];     
        if (password_verify($pass, $pass_hash)) {
            $_SESSION['user']=$res['id'];
        header("Location: http://www.czerust.cz/mt/");
            
        } else {

            $chyba= "špatné heslo";
        }
    }

?>

<?php

include 'head.php';


?>

<div class="col-md-8">

<div class="alert alert-danger" role="alert">
<b>CHYBA: </b>
<?php
echo $chyba;
?>
</div>
<a href="http://www.czerust.cz/mt/">zpět</a>

<?php

include 'tail.php';
?>

