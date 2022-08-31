<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/listaprova.html");

    $opzioni_razza = new Template("skins/opzioni-razza.html");

    $query = "SELECT ID, nome FROM razza";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {
        
        $opzioni_razza->setContent("nome_razza", $row['nome']);
        $opzioni_razza->setContent("id", $row['ID']);

    }

    $item->setContent("opzioni_razza", $opzioni_razza->get());


    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>