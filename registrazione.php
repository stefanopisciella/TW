<?php
 require "frame-public.php";
    
 $registrazione = new Template("skins/registrazione.html");
 $frame_public->setContent("contenuto", $registrazione->get());
 $frame_public->close();
?>