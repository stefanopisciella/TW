<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    $head = new Template("skins/frame-public.html");
    $blog = new Template("skins/blog.html");

    $head->setContent("contenuto", $blog->get());
    
    $head->close();
?>