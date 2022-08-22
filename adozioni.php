<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    global $mysqli;

    $head = new Template("skins/frame-public.html");
    $adozioni = new Template("skins/adozioni.html");

    // injection opzioni razze nel relativo filtro di ricerca
    $opzioni_razza = new Template("skins/opzioni-razza.html");

    $query = "SELECT nome FROM razza";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {
        
        $opzioni_razza->setContent("nome_razza", $row['nome']);
    }

    $adozioni->setContent("opzioni_razza", $opzioni_razza->get());

    // injection cani da visualizzare per pagina
    
    // devo estarre le informazioni sui cani, considerando anche i filtri

    // prendo dall'html i filtri selezionati
    $sesso = $_POST['sesso'];

    $taglia = $_POST['taglia'];

    $razza = $_POST['razza'];

    $eta = $_POST['eta'];

    // injection adozioni.html contenuto del frame-public
    $head->setContent("contenuto", $adozioni->get());
    
    $head->close();
?>