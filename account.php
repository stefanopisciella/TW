<?php
    require "frame-public.php";

    $nome_script = "account";
    if(!isset($_SESSION['user_id']) ||
    user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        // se il client non è loggato, viene reindirizzato alla home
        header("Location: home.php");
        exit;   
    }

    $user_id = $_SESSION['user_id'];

    $item = new Template("skins/account.html");

    $head->setContent("contenuto", $item->get());
    $head->close();
?>