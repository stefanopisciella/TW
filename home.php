<?php

    include "include/template2.inc.php";
    require "include/dbms.inc.php";

    $head = new Template("skins/frame-public.html");
    $home = new Template("skins/home.html");

    global $mysqli;

    // inserimento 6 cani da adottare

    // query per ottenere 6 cani da mostrare in home (DATI E 1 IMMAGINE)
    $query_cani = "SELECT DISTINCT nome, eta, sesso, razza, `path` AS img FROM cane JOIN immagine ON cane.ID = ID_cane GROUP BY nome ORDER BY RAND() LIMIT 6;";

    try {
        $oid = $mysqli->query($query_cani);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $cani_info = $oid->fetch_all(MYSQLI_ASSOC);

    // preparo lo snippet di HTML che contiene i 6 cani
    $cani_home = new Template("skins/cani-home.html");

    // injection dati cani in cani_home.html
    for ($i = 0; $i < 6; $i++) {
        $cani_home->setContent("nome".($i+1), $cani_info[$i]['nome']);
        $cani_home->setContent("eta".($i+1), $cani_info[$i]['eta']);
        $cani_home->setContent("sesso".($i+1), $cani_info[$i]['sesso']);
        $cani_home->setContent("razza".($i+1), $cani_info[$i]['razza']);
        $cani_home->setContent("img".($i+1), $cani_info[$i]['img']);
    }

    // injection snippet cani_home in home.html
    $home->setContent("cani_home", $cani_home->get());

    // injection snippet home in frame-public
    $head->setContent("contenuto", $home->get());
    $head->close();
?>