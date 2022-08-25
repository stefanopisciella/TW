<?php
    require "include/dbms.inc.php";
    require "frame-public.php";

    $dettaglio_cane = new Template("skins/dettaglio-cane-a-distanza.html");

    // injection informazioni cane
    $id_cane = $_GET['id'];
    $query_info_cane = "SELECT nome, presentazione, sesso, eta, razza FROM cane WHERE ID = '{$id_cane}';";

    try {
        $oid = $mysqli->query($query_info_cane);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $info_cane = $oid->fetch_all(MYSQLI_ASSOC);

    $dettaglio_cane->setContent("nome", $info_cane[0]["nome"]);
    $dettaglio_cane->setContent("presentazione", $info_cane[0]["presentazione"]);
    $dettaglio_cane->setContent("sesso", $info_cane[0]["sesso"]);
    // sistemazione stringa età
    $eta = $info_cane[0]['eta'];
    if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
    else $eta = substr($eta, 0, -1)." mesi";
    $dettaglio_cane->setContent("eta", $eta);
    $dettaglio_cane->setContent("razza", $info_cane[0]["razza"]);
    
    $head->setContent("contenuto", $dettaglio_cane->get());
    
    $head->close();
?>