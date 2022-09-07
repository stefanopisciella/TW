<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/index";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/index.html");

    global $mysqli;

    $main->setContent("nome_cognome", initialize_frame());

    $not = new Template("skins/notifiche.html");

    $notifiche = notifiche();

    foreach($notifiche as $notifica) {
        $not->setContent("nome", $notifica['nome']);
        $not->setContent("anteprima", $notifica['anteprima']);
    }

    $main->setContent("notifiche", $not->get());
    
    //query per cani adottati (quelli a distanza li consideriamo adottati?)

    $query_cani_adottati = "SELECT COUNT(*) AS c FROM cane WHERE adottato=1 && distanza=0";

            try {
                $oid = $mysqli->query($query_cani_adottati);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            $cani_adottati =  $oid ->fetch_assoc();

            $item->setContent("cani_adottati", $cani_adottati["c"]);

    // query per cani in struttura

    $query_cani_in_struttura = "SELECT COUNT(*) AS c FROM cane WHERE adottato=0";

            try {
                $oid = $mysqli->query($query_cani_in_struttura);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            $cani_in_struttura =  $oid ->fetch_assoc();

            $item->setContent("cani_in_struttura", $cani_in_struttura["c"]);
    
    // query per utenti

    $query_utenti = "SELECT COUNT(*) AS c FROM user_has_group WHERE ID_gruppo=2";

            try {
                $oid = $mysqli->query($query_utenti);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            $utenti =  $oid ->fetch_assoc();

            $item->setContent("utenti", $utenti["c"]);

    // query per adozioni a distanza

    $query_adozioni_a_distanza = "SELECT COUNT(*) AS c FROM adozione_distanza";

            try {
                $oid = $mysqli->query($query_adozioni_a_distanza);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            $adozioni_a_distanza =  $oid ->fetch_assoc();

            $item->setContent("adozioni_a_distanza", $adozioni_a_distanza["c"]);

    $main->setContent("contenuto", $item->get());
    
    $main->close(); 


?>
