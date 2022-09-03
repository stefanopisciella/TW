<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    session_start();
    $nome_script = "admin/index";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        echo "Unauthorized";
        exit;   
    }

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/index.html");

    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>
