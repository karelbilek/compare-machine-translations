<?php

include 'head.php';
include 'logbox.php';


?>

<div class="col-md-8">

<?php
if (!isset($_SESSION['user']) or !$_SESSION['user']) { 
?>

<h1 style="border-bottom: 1px solid #20496D;padding-bottom: 20px;margin-top: 0px;">Srovnávání česko-ruského strojového překladu</h1>

<p>Umíte česky a rusky? Pomozte zlepšit strojový překlad! (A mně dopsat diplomovou práci.)</p>

<p>Na obrazovce se Vám objeví zdrojová věta (česky), její správný překlad (rusky) a několik strojových překladů (většinou trochu až hodně špatných). Vašim úkolem je seřadit je od nejlepšího po nejhorší. Hodnocení vypadá přibližně takto:</p>

<img src="http://i.imgur.com/MeL8wG1l.png">

<p>Pro start klikněte na <a href="http://www.czerust.cz/mt/register.php">registrace</a> a registrujte se s libovolným jménem.</p>

<p>Autorem projektu je <a href="http://karelbilek.com">Karel Bílek</a>; o projektu více <a href="http://www.czerust.cz/mt/about.html">zde</a>. Hodnotící systém má zdrojový kód <a href="https://github.com/runn1ng/compare-machine-translations">tu</a>; inspirováno systémem <a href="https://github.com/cfedermann/Appraise">Appraise</a>.</p>

<p>Případné komentáře pište na <a href="mailto:kb@karelbilek.com">můj e-mail</a>.</p>

<?php
} else {

function get_random_system_list() {
    $list = get_system_list();
    shuffle($list);
    return $list;
}

function get_corpus_list() {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT `id` FROM corpora
    ');
    $stmt->execute();

    $res=array();
    while ($row = $stmt->fetch()) {
        $res[]=$row['id'];
    }
    
    return $res;
}

function get_random_corpus() {
    $list = get_corpus_list();
    $i=array_rand($list);
    return $list[$i];
}


function get_random_unfilled_sentence($user, $corpus) {
    global $pdo;
    $stmt = $pdo->prepare('
        select `id` from sentences
        where corpus = :corpus
        and id
        not in
        (
                SELECT sentence
                FROM hits
                WHERE user = :user
                AND corpus= :corpus
                GROUP BY sentence
        )
        order by rand()
        limit 1
    ');
    $stmt->execute(array("user"=>$user, "corpus"=>$corpus));
 
    $row = $stmt->fetch();
   
    if (!$row) {
        die ("Vět jste označili tolik, že už došly. Jděte radši dělat něco jiného.");
    }

    $stmt2 = $pdo->prepare('
        select * from sentences
        where id = :id
        and corpus=:corpus');
    $stmt2->execute(array("id"=>$row['id'], "corpus"=>$corpus));
    $res = $stmt2->fetch();
    return $res;
}

function get_random_variants($sentence, $corpus) {
    global $pdo;

    $res = array();
    $systems = get_random_system_list();
    foreach($systems as $system) {
        $stmt = $pdo->prepare('
            select `variant` from variants
            where sentence = :sentence
            and corpus=:corpus
            and system = :system');    
        $stmt->execute(array("sentence"=>$sentence['id'], "corpus"=>$corpus, "system"=>$system));
        $row = $stmt->fetch();
        $res[]=array("system"=>$system, "variant"=>$row['variant']);
    }
    return $res;
}

$corpus = get_random_corpus();
$sent = get_random_unfilled_sentence($_SESSION['user'], $corpus);
#var_dump($sent);
$variants =get_random_variants($sent, $corpus);
#var_dump($variants);
#print $sent['target'];

?>

<style>


.ref {
padding: 20px;
/*margin: 20px 0;*/
border: 1px solid #eee;
border-left-width: 5px;
border-radius: 3px;
border-left-color:#696969;
}
.ref h4{
margin-top: 0;
margin-bottom: 5px;
color:#696969;
}



.zdroj {
padding: 20px;
/*margin: 20px 0;*/
border: 1px solid #eee;
border-left-width: 5px;
border-radius: 3px;
border-left-color:#69AA58;
}
.zdroj h4{
margin-top: 0;
margin-bottom: 5px;
color:#69AA58;
}

#refhide {
    display:none;
}

#refshow {
    margin-top:8px;
}

.veta {
margin-bottom:8px;
}

</style>

<div class="row veta">
    <div class="col-md-6">


        <div class="zdroj">
        <h4>Zdroj</h4>
        <?php
        print htmlspecialchars($sent['source']);
        ?>
        </div>

    </div>

    <div class="col-md-6">


        <div class="ref">
        <h4>Reference</h4>
        <div id="refhide">
        <?php
        print htmlspecialchars($sent['target']);
        ?>
        </div>
        <button class="btn btn-default" id="refshow">Zobrazit</a>
        </div>

    </div>
    <script>
    $('#refshow').click(function() {
        $('#refshow').hide(50);
        $('#refhide').show(50);

    });
    </script>

</div>
<div class="row">

<?php
    $variant_count = count($variants);
?>

<style>
.mytable>tbody>tr>td {
    border-top:0px;
}

.boxes {
    border-bottom:1px solid rgb(190, 188, 188);
}

.even {
    background-color: #f9f9f9;
}

</style>
<form action="annot.php" method="post">
<table class="table mytable">
    
  <?php
    $i=-1;
    foreach($variants as $variant) {
    $i++;
    if ($i%2==0) {
        $class="even";
    } else {
        $class="odd";
    }
  ?>
   <tr class="<?php echo $class?>">
      <td></td>
      <td colspan="<?php print $variant_count;?>">
         <?php print htmlspecialchars($variant['variant']); ?>
      </td>
      <td></td>
   </tr>
   <tr class="boxes <?php echo $class?> ">
      <td><span class="label label-success">Nejlepší</span></td>
      <?php
        for ($j=1; $j<=$variant_count; $j++) {
            ?>
            <td>
            <label>
                <input type="radio" name="var_<?php print $variant['system']?>" value="<?php print $j?>">
                <span class="label label-default"><?php print $j?></span>
            </label>
            </td>
            <?php
        }
      ?>
        <td><span class="label label-danger">Nejhorší</span></td>
   </tr>
   <?php
   }
   ?>

</table>

<input type="hidden" name="sentence" value="<?php print $sent['id']?>">
<input type="hidden" name="corpus" value="<?php print $corpus?>">
<input type="hidden" name="csrfToken" value="<?php print $_SESSION['csrfToken']?>">

        <input type="submit" class="btn btn-primary" value="Odeslat" id="odbutton"></input>

        <script>
           $('#odbutton').click(function(event){
                var vse = true;
                for (i=1; i<=<?php echo $variant_count; ?>; i++) {
                    var v = $('input:radio[name=var_'+i+']:checked').val();
                    if (typeof v === "undefined") {
                        vse=false;
                    }
                }
                if (!vse) {
                    alert("Prosím, oznámkujte všechny věty.");
                    event.preventDefault();
                }
           });

        </script>

</form>

</div>

<?php } 


include 'tail.php';

?>

