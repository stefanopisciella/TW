<?php
    require "include/template2.inc.php";

    session_start();
    if ((isset($_SESSION['admin']) && $_SESSION['admin'] == false) ||
        !isset($_SESSION['user_id'])) {
        // caso in cui il client non è loggato oppure è loggato ma non risulta essere l'admin
        echo "Unauthorized";
    } else {
        $main = new Template("skins/index.html");
        $main->close();   
    }  
?>