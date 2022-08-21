<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    $head = new Template("skins/frame-public.html");
    $adozioni = new Template("skins/adozioni.html");

    $head->setContent("contenuto", $adozioni->get());
    
    $head->close();
?>