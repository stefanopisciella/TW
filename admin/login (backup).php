<?php
    require "include/template2.inc.php";
    /*
    if (isset($_SESSION['auth']) && $_SESSION['auth'])
        header('location: profile.php');
    */

    $login = new Template("skins/login.html");

    /*
    if (isset($_GET['location']))
    {
        $array = explode("/", $_GET['location']);
        $_SESSION['previous_page'] = end($array);
    } */

    /*
    if (isset ($_GET['error'])) {

        switch ($_GET['error']) {
            case 1:
                $error = "Compila tutti i campi!";
                break;
            case 2:
                $error = "Username e/o password sbagliati!";
                break;
        }
        session_abort();
        $login->setContent("error", $error);
    }
    else $login->setContent("error", BLANK_T);
    */

    // $main->setContent("dynamic_content", $login->get());

    // la login può essere fatta soltanto con metodo POST
    /*if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['Username'];
        $password = $_POST['Password'];

        if (!isset($username) 
                || $username == '' 
                ||!isset($password) 
                || $password == '') {
            header("Location: login.php?error=1");
        } else {
            if(strcmp($username, "admin") == 0 && strcmp($password, "admin") == 0) {
                header("Location: index.php");
            }  
        }
    }*/

    $login->close();
?>

