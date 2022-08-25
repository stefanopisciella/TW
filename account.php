<?php

    include "include/template2.inc.php";

    $head = new Template("skins/frame-public.html");
    $item = new Template("skins/account.html");

    $head->setContent("contenuto", $item->get());
    $head->close();
?>