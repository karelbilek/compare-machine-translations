<?php
include 'head.php';

session_start();
if (!isset($_POST['name']) or !$_POST['name']) { 

?>
<div class="col-md-8">

    <h1>Registrace</h1>


<form role="form" action="register.php" method="post">
  <div class="form-group">
    <label for="name">Jméno</label>
    <input type="text" class="form-control" id="name" placeholder="Jméno" name="name">
  </div>
  <div class="form-group">
    <label for="password">Heslo</label>
    <input type="password" class="form-control" id="password" placeholder="Heslo" name="password">
  </div>
   <div class="form-group">
    <label for="passwordtwice">Heslo znovu</label>
    <input type="password" class="form-control" id="passwordtwice" placeholder="Heslo znovu" name="passwordtwice">
  </div>
  <input type="submit" class="btn btn-default" value="Registrace"></input>
</form>


<?php
} 
else {

    print '<div class="col-md-8">';

    if (!isset($_POST['name']) or !isset($_POST['password'])   or !isset($_POST['passwordtwice'])  ) {
        die("weird request");
    } 


    $name = $_POST['name'];
    $pass = $_POST['password'];
    $pass2 = $_POST['passwordtwice'];

    if ($pass != $pass2) {
        $chyba = "Heslo není stejné.";
    } else {

        $pass_enc = password_hash($pass, PASSWORD_DEFAULT);
    
        $stmt = $pdo->prepare('
        INSERT INTO `users` (`username`, `pass`)
        VALUES (:name, :pass)
        ');


        if ($stmt->execute(array('name'=>$name, 'pass'=>$pass_enc))) {
            $chyba = 0;
        } else {
            $chyba = "Uživatel s tímto jménem už existuje.";
        }
    }

    if ($chyba !== 0) {
?>

<div class="alert alert-danger" role="alert">
<b>CHYBA: </b>

<?php
echo $chyba;
?>

</div>
<a href="#" onclick="history.go(-1);return false;">zpět</a>


<?php
    } else {
?>

<div class="alert alert-success" role="alert">
Byl jste úspěšně registrován.

</div>
<a href="http://178.79.146.93/mt/">na hlavní stránku</a>


<?php
    }

}

include 'tail.php';

?>

