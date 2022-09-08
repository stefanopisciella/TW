<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/php-utils/varie.php";
    require "include/utils_dbms.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/informazioni";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }

    global $mysqli;

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/informazioni.html");

    // injection informazioni
    $informazioni = new Template("skins/messaggio.html");

    $query = "SELECT nome, email, `data`, messaggio FROM richiesta_info WHERE chip IS NULL ORDER BY `data` DESC;";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {
         
        $informazioni->setContent("data", formatta_data_stringhe($row['data']));
        $informazioni->setContent("nome", $row['nome']);
        $informazioni->setContent("mail", $row['email']);
        $informazioni->setContent("contenuto", $row['messaggio']);

    }

    $item->setContent("messaggi", $informazioni->get());

    //-----------------------------------------------------------------------------------------------------------

    // injection richieste cani
    $info_cani = new Template("skins/info-cane.html");

    $query = "SELECT richiesta_info.nome, email, `data`, messaggio, richiesta_info.chip, cane.nome AS nomeC FROM richiesta_info JOIN cane ON cane.chip = richiesta_info.chip ORDER BY `data` DESC;";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {
         
        $info_cani->setContent("data", formatta_data_stringhe($row['data']));
        $info_cani->setContent("nome", $row['nome']);
        $info_cani->setContent("mail", $row['email']);
        $info_cani->setContent("contenuto", $row['messaggio']);
        $info_cani->setContent("nome_cane", $row['nomeC']);
        $info_cani->setContent("chip", $row['chip']);

    }

    $item->setContent("messaggi_cani", $info_cani->get());

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

