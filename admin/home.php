<?php
    require "include/template2.inc.php";

    session_start();
    if(isset($_SESSION['logged']) && $_SESSION['logged'] != true) {
        header('location: index.php');
    } else {
        $main = new Template("skins/home.html");
        $main->close();    
    }
?>