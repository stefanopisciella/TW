<?php

    include "include/template2.inc.php";

    $head = new Template("admin/skins/frame-public-admin.html");
    $item = new Template("admin/skins/adozioni-admin.html");

    $head->setContent("contenuto", $item->get());
    $head->close();
?>