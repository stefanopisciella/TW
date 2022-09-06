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
