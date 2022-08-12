<?php
    require "include/template2.inc.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // caso in cui il client ha già visionato la pagina della login e fa "submit" delle credenziali
        $username = $_POST['Username'];
        $password = $_POST['Password'];

        if (!isset($username) 
                || $username == '' 
                || !isset($password) 
                || $password == '') {
             // credenziali non valide
            header("Location: index.php?wrong_credentials=1");
        } else {
            if(strcmp($username, "admin") == 0 && strcmp($password, "admin") == 0) {
                // credenziali corrette
                header("Location: home.php");
            } else {
                // credenziali non corrette
                header("Location: index.php?wrong_credentials=2");
            }
        }
    } else {
        // caso in cui il client carica la pagina con il metodo GET
        if (isset ($_GET['wrong_credentials'])) {
            $param = $_GET['wrong_credentials'];
            // client visualizza errore riguardante le credenziali
            if ($param == 1) {
                $login = new Template("skins/index.html");
                $login->setContent("wrong_credentials", "Username e/o Password non sono stanti compilati");
                $login->close();  
            }  
            if ($param == 2) {
                $login = new Template("skins/index.html");
                $login->setContent("wrong_credentials", "Username e/o Password non corretti");
                $login->close();   
            }
        } else {
            // caso in cui il client carica la pagina della login, ma non ancora fa ancora il "submit" delle credenziali
            $login = new Template("skins/index.html");
            $login->close();  
        }
    }
?>