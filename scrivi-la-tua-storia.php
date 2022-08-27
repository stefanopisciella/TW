<?php

require "include/dbms.inc.php";
require "include/php-utils/varie.php";
require "frame-public.php";

$item = new Template("skins/scrivi-la-tua-storia.html");

$head->setContent("contenuto", $item->get());
$head->close();

?>