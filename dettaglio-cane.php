<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    $head = new Template("skins/frame-public.html");
    $dettaglio_cane = new Template("skins/dettaglio-cane.html");

    $head->setContent("contenuto", $dettaglio_cane->get());
    
    $head->close();
?>