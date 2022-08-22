<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    global $mysqli;

    $head = new Template("skins/frame-public.html");
    $faq = new Template("skins/faq.html");

    // injection adozioni.html contenuto del frame-public
    $head->setContent("contenuto", $faq->get());
    
    $head->close();
?>