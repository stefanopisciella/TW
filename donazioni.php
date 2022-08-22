<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    global $mysqli;

    $head = new Template("skins/frame-public.html");
    $donazioni = new Template("skins/donazioni.html");

    // injection adozioni.html contenuto del frame-public
    $head->setContent("contenuto", $donazioni->get());
    
    $head->close();
?>