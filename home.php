<?php

    require "include/dbms.inc.php";
    require "include/php-utils/varie.php";
    require "frame-public.php";

    // per il redirect allo script "home" una volta effettuato il login
    $_SESSION['previous_page'] = 'home';

    $home = new Template("skins/home.html");

    global $mysqli;

    // inserimento 6 cani da adottare (random)
    // inserimento 6 cani da adottare

    // query per ottenere 6 cani da mostrare in home (DATI E 1 IMMAGINE)
    $query_cani = "SELECT DISTINCT cane.ID, nome, eta, sesso, razza, chip, `path` AS img FROM cane JOIN immagine ON cane.ID = ID_cane AND distanza=false AND adottato=false GROUP BY nome ORDER BY RAND() LIMIT 6;";

    try {
        $oid = $mysqli->query($query_cani);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    // preparo lo snippet di HTML che contiene i 6 cani
    //$cani_home = new Template("skins/cani-home.html");
    $cani_home = new Template("skins/singolo-cane.html");

    // injection dati cani in cani_home.html
    while($row = mysqli_fetch_array($oid)) {
        $cani_home->setContent("id", $row['ID']);
        $cani_home->setContent("nome", $row['nome']);

        // sistemazione stringa età in home
        $eta = $row['eta'];

        if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
        else $eta = substr($eta, 0, -1)." mesi";
        $cani_home->setContent("eta", $eta);

        $cani_home->setContent("sesso", $row['sesso']);
        $cani_home->setContent("razza", $row['razza']);
        $cani_home->setContent("CHIP", $row['chip']);
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

    // injection sezione faq
    // per ogni categoria, faccio visualizzare una coppia domanda-risposta presa a caso

    // injection categorie
    $query_categorie = "SELECT ID, nome FROM categoria WHERE tipo='faq';";

    try {
        $oid = $mysqli->query($query_categorie);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {

        // devo tenere da parte l'id della categoria di FAQ per collocare opportunamente le domande e le relative risposte
        $nome_cat = $row['nome'];

        // outer injection
        $home->setContent("id", $row['ID']);
        $home->setContent("categoria", $row['nome']);
        
        $domande_risposte = new Template("skins/domande-risposte.html");

        $query_domande_risposte = "SELECT domanda, risposta FROM faq WHERE categoria ='{$nome_cat}' ORDER BY RAND() LIMIT 1;";

        try {
            $oid2 = $mysqli->query($query_domande_risposte);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        while($row2 = mysqli_fetch_array($oid2)) {
    
            // inner injection
            $domande_risposte->setContent("domanda", $row2['domanda']);
            $domande_risposte->setContent("risposta", $row2['risposta']);
        }

        $home->setContent("domande_risposte", $domande_risposte->get());
    }



    // injection snippet home in frame-public
    $head->setContent("contenuto", $home->get());
    $head->close();
?>