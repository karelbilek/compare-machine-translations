 <div class="col-md-3">

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Uživatel</h3>
  </div>
  <div class="panel-body">

<?php
session_start();

if (!isset($_SESSION['csrfToken'])) {
    $randomtoken = md5(uniqid(rand(), true));
    $_SESSION['csrfToken']=$randomtoken;
}

if (!isset($_SESSION['user']) or !$_SESSION['user']) { 
?>

<form class="form-horizontal" role="form" action="login.php" method="post" >
  <div class="form-group">
    <label for="name" class="col-sm-3 control-label">Jméno</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="name" placeholder="Jméno" name="name">
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-sm-3 control-label">Heslo</label>
    <div class="col-sm-9">
        <input type="password" class="form-control" id="password" placeholder="Heslo" name="password">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-9 col-sm-offset-3">
        <input type="submit" class="btn btn-primary" value="Přihlásit"></input>
        <br><br><a class="" href="register.php">Registrace</a>
     </div>
   </div>
</form>


  </div>
</div> <!-- /panel -->




<?php
} else { 

function get_user_info($id) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT * FROM users
        WHERE id = :id
    ');
    $stmt->execute(array('id'=>$id));

    $res=array();
    $row = $stmt->fetch();
    $res['username']= $row['username'];

    $stmt2 = $pdo->prepare('
        SELECT COUNT(DISTINCT `sentence`) AS done FROM `hits` WHERE `user` = :user
    ');    
    $stmt2->execute(array('user'=>$id));
    
    $row=$stmt2->fetch();
    
    $res['done']=$row['done'];
     
    return $res;
}

$uinfo=get_user_info($_SESSION['user']);

?>

<div class="row">
  <div class="col-sm-5"><b>Jméno</b></div>
  <div class="col-sm-7"><?php print htmlspecialchars($uinfo['username'])?></div>
</div>
<br>
<div class="row">
  <div class="col-sm-5"><b>Ohodnoceno</b></div>
  <div class="col-sm-7"><?php print $uinfo['done']?></div>
</div>

<br>
<div class="row">
  <div class="col-sm-5"></div>
  <div class="col-sm-7">
<a href="http://www.czerust.cz/mt/logout.php">Odhlásit</a></div>
</div>



  </div>
</div> <!-- /panel -->

<div class="alert alert-info" role="alert">
Srovnejte překlady od nejlepšího po nejhorší. Je povolené, aby dvě věty měly stejné číslo; přesto je lepší vnímat hodnocení spíš jako řazení, než jako známkování.<br><br>

Není nutné nad hodnocením tolik přemýšlet, nejde o "test"; dejte spíše na první dojem.<br><br>
Referenční překlad je pouze orientační a nemusí být vždy 100% správný.
</div>

<p>Případné komentáře pište na <a href="mailto:kb@karelbilek.com">můj e-mail</a>.</p>

<?php } ?>

 
 
</div> <!-- /col-md-3 -->

