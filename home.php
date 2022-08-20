<?php

    include "include/template2.inc.php";

    $head = new Template("frame-public.html");
    $item = new Template("home.html");

    $head->setContent("contenuto", $item->get());
    $head->close();
?>