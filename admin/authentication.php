<?php

require "include/template2.inc.php";

// la login può essere fatta soltanto con metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['Username'];
    $password = $_POST['Password'];

    if (!isset($username) 
            || $username == '' 
            || !isset($password) 
            || $password == '') {
        // credenziali non valide
        header("Location: login.php");
    } else {
        if(strcmp($username, "admin") == 0 && strcmp($password, "admin") == 0) {
            // credenziali scorrette
            header("Location: index.php");
        } else {
            // credenziali non corrette
            header("Location: login.php");
        }
        
    }
} else {
    $login = new Template("skins/login.html");
    $login->close();
}
?>