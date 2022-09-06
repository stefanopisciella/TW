<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/php-utils/varie.php";

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
        $articoli->setContent("contenuto", $row['contenuto']);
        $articoli->setContent("data", formatta_data_stringhe($row['data']));
        $articoli->setContent("categoria", $row['categoria']);
        $articoli->setContent("id", $row['ID']);

    }

    $item->setContent("articoli", $articoli->get());

    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>
