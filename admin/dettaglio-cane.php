<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    session_start();
    $nome_script = "admin/dettaglio-cane";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        echo "Unauthorized";
        exit;   
    }
   
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/dettaglio-cane.html"); 
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $main = new Template("skins/frame-private.html");
        $item = new Template("skins/dettaglio-cane.html");
    
        $main->setContent("contenuto", $item->get());
        $main->close();


    }

    $main->setContent("contenuto", $item->get());
    $main->close();

?>
