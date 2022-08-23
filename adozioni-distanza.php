<?php
    require "include/dbms.inc.php";
    require "frame-public.php";

    $adozioni_distanza = new Template("skins/adozioni-a-distanza.html");

    $head->setContent("contenuto", $adozioni_distanza->get());
    
    $head->close();
?>