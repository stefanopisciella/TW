<?php
    session_start();

    header('location: index.php');    

    if(isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
        session_destroy();
    }
?>