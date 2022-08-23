<?php

    require "include/dbms.inc.php";
    require "include/php-utils/varie.php";
    require "frame-public.php";

    $home = new Template("skins/home.html");

    global $mysqli;

    // inserimento 6 cani da adottare (random)
    // inserimento 6 cani da adottare

    // query per ottenere 6 cani da mostrare in home (DATI E 1 IMMAGINE)
    $query_cani = "SELECT DISTINCT cane.ID, nome, eta, sesso, razza, `path` AS img FROM cane JOIN immagine ON cane.ID = ID_cane GROUP BY nome ORDER BY RAND() LIMIT 6;";

    try {
        $oid = $mysqli->query($query_cani);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    // preparo lo snippet di HTML che contiene i 6 cani
    $cani_home = new Template("skins/cani-home.html");

    // injection dati cani in cani_home.html
    while($row = mysqli_fetch_array($oid)) {
        $cani_home->setContent("id", $row['ID']);
        $cani_home->setContent("nome", $row['nome']);
        $cani_home->setContent("eta", $row['eta']);
        $cani_home->setContent("sesso", $row['sesso']);
        $cani_home->setContent("razza", $row['razza']);
        $cani_home->setContent("img", $row['img']);
    }

    // injection snippet cani_home in home.html
    $home->setContent("cani_home", $cani_home->get());
    //$home->close();

    // injection articoli nella categoria NEWS più recenti home
    $query_articoli = "SELECT ID, titolo, autore, `data`, `path` AS img FROM articolo WHERE categoria='news' ORDER BY `data` DESC LIMIT 3;";
    try {
        $oid = $mysqli->query($query_articoli);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $articoli_home = new Template("skins/articoli-home.html");

    while($row = mysqli_fetch_array($oid)) {
        $articoli_home->setContent("id", $row['ID']);
        $articoli_home->setContent("titolo", $row['titolo']);
        $articoli_home->setContent("autore", $row['autore']);
        // formattazione della data in formato italiano
        $articoli_home->setContent("data", formatta_data_stringhe($row["data"]));
        $articoli_home->setContent("img", $row['img']);
    }

    $home->setContent("articoli_home", $articoli_home->get());

    // injection snippet home in frame-public
    $head->setContent("contenuto", $home->get());
    $head->close();
?>