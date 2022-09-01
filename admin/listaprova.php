<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/faq.html");

    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>
