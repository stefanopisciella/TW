<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    $head = new Template("skins/frame-public.html");
    $adozioni_distanza = new Template("skins/adozioni-a-distanza.html");

    $head->setContent("contenuto", $adozioni_distanza->get());
    
    $head->close();
?>