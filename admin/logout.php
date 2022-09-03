<?php
    session_start();
    // unset dei dati di sessione prima della distruzione della sessione
    $_SESSION = array();
    session_destroy(); 

    header("Location: index.php?");
?>