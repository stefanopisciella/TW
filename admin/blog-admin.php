<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/php-utils/varie.php";
    require "frame-private.php";

    session_start();

    global $mysqli;

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/blog-admin.html");

    // injection articoli
    $articoli = new Template("skins/articolo-admin.html");

    $query = "SELECT ID, titolo, contenuto, `data`, categoria, `path` AS img FROM articolo;";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {
     
        $articoli->setContent("img", $row['img']);
        $articoli->setContent("titolo", $row['titolo']);

        $anteprima_testo = substr($row['contenuto'], 0, 280)." ...";
        $articoli->setContent("testo", $anteprima_testo);

        $articoli->setContent("data", formatta_data_stringhe($row['data']));
        $articoli->setContent("categoria", $row['categoria']);
        $articoli->setContent("id", $row['ID']);

    }

    $item->setContent("articoli", $articoli->get());

    $main->setContent("nome_cognome", initialize_frame());

    $not = new Template("skins/notifiche.html");

    $notifiche = notifiche();

    foreach($notifiche as $notifica) {
        $not->setContent("nome", $notifica['nome']);
        $not->setContent("anteprima", $notifica['anteprima']);
    }

    $main->setContent("notifiche", $not->get());

    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>
