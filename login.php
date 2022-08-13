<?php
    require "frame-public.php";
    
    $login = new Template("skins/login.html");
    $frame_public->setContent("contenuto", $login->get());
    $frame_public->close();
    /*
    require "include/template2.inc.php";
    require "include/tags/utility.inc.php";

    session_start();
    
    // una volta loggati correttamente, non è più possibile ritornare alla pagina della login
    // se non in seguito ad un logout
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != 0) {
        header('location: index.php');
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // caso in cui il client ha già visionato la pagina della login e fa "submit" delle credenziali
        $username = $_POST['Username'];
        $password = $_POST['Password'];

        if (!isset($username) 
                || $username == '' 
                || !isset($password) 
                || $password == '') {
             // credenziali non valide
            header("Location: login.php?wrong_credentials=1");
        } else {
            // cripta la password perché nel DB queste ultime non sono salvate in chiaro
            $password = md5(md5(md5(md5(md5($password)))));
            $user_id = login_query($username, $password);
            if($user_id != 0) {
                // credenziali corrette
                $_SESSION['user_id'] = $user_id;
                header("Location: home.php");
            } else {
                // credenziali non corrette
                header("Location: login.php?wrong_credentials=2");
            }
        }
    } else {
        // caso in cui il client carica la pagina con il metodo GET
        if (isset ($_GET['wrong_credentials'])) {
            $param = $_GET['wrong_credentials'];
            // client visualizza errore riguardante le credenziali
            if ($param == 1) {
                $login = new Template("skins/login.html");
                $login->setContent("wrong_credentials", "Username e/o Password non sono stanti compilati");
                $login->close();  
            }  
            if ($param == 2) {
                $login = new Template("skins/login.html");
                $login->setContent("wrong_credentials", "Username e/o Password non corretti");
                $login->close();   
            }
            session_abort();
        } else {
            // caso in cui il client carica la pagina della login, ma non ancora fa ancora il "submit" delle credenziali
            $login = new Template("skins/login.html");
            $login->close();  
        }
    }
    */
?>