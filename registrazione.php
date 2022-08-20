<?php
    require "frame-public.php";
    require 'include/php-utils/utils_dbms.php';
     
    /*
    session_start();
    // unset dei dati di sessione
    $_SESSION = array();
    // si distrugge la sessione per evitare che siano attive più di una sessione 
    // associata ad un singolo client, cosa che può succedere se il client decide
    // di registrarsi da loggato
    session_destroy(); */
    
    /*
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        echo $id;
    } else {
        echo "porcodio";
    } */
   
    // non è possibile effettuare la registrazione se non si effettua prima il logout
    session_start();
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] >= 1) {
        header('location: index.php');
    } 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $conferma_password = $_POST['conferma_password'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];

        if (!isset($nome) || $nome == '' 
            || !isset($cognome) || $cognome == ''
            || !isset($username) || $username == ''
            || !isset($password) || $password == ''
            || !isset($conferma_password) || $conferma_password == ''
            || !isset($email) || $email == ''
            || !isset($telefono) || $telefono == '') {
            
            // caso in cui non tutti i campi della form sono stanti compilati
            header("Location: registrazione.php?empty_fields=1");
        } else {
            $url_params = array('password' => null,
                                'phone' => null,
                                'email' => null
            );
            
            if(strcmp($password, $conferma_password) != 0) {
                // le password inserite non matchano
                $url_params["password"] = "wrong_password=1"; 
            }

            // controllo validità telefono
            if(!is_numeric($telefono)) {
                $url_params["phone"] = "wrong_phone=1"; 

            }
        
            // controllo validità email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $url_params["email"] = "wrong_email=1"; 
            }

            $errore = false;
            $url_error = "";
            foreach ($url_params as $url_param) {
                if(isset($url_param)) {
                    $url_error .= $url_param . "&";
                    $errore = true;
                }
            }
            
            $url_error = substr($url_error, 0, -1);

            if($errore == false) {
                $password = md5(md5(md5(md5(md5($password)))));
                $utente = ["'".$username."'", "'".$nome."'", "'".$cognome."'", "'".$password."'", "'".$email."'", "'".$telefono."'"];

                try {
                    insert_query('utente', $utente);
                    header("Location: login.php?");
                } catch (Exception $e){
                    // caso in cui viene violato uno o più vincoli di univocità (abbiamo questi vincoli per quanto riguarda l' inserimento
                    //dell'email e del nickname)
                    header("Location: registrazione.php?not_unique=1");

                    // controllo univocità email

                    // controllo univocità nickname
                    
                    // REMOVE
                    // echo $e;
                }
            } else {
                // caso in cui si è verificato almeno un errore nella compilazione del form
                header("Location: registrazione.php?" . $url_error);
            }
        }
    } else {
        // caso in cui il client carica la pagina con il metodo GET
        $signup = new Template("skins/registrazione.html");
        
        if (isset ($_GET['empty_fields'])) {
            $param = $_GET['empty_fields'];
            if ($param == 1) {
                $signup->setContent("empty_fields", "Non tutti i campi sono stanti compilati <br>");
                $frame_public->setContent("contenuto", $signup->get());
                $frame_public->close();
                exit;
            }  
        }

        if (isset ($_GET['wrong_password'])) {
            $param = $_GET['wrong_password'];
            if ($param == 1) {
                $signup->setContent("wrong_password", "Le credenziali inserite non coincidono <br>");
            }  
        }

        if (isset ($_GET['wrong_phone'])) {
            $param = $_GET['wrong_phone'];
            if ($param == 1) {
                $signup->setContent("wrong_phone", "Il numero di telefono inserito non è valido <br>");
            }  
        }
        
        if (isset ($_GET['wrong_email'])) {
            $param = $_GET['wrong_email'];
            if ($param == 1) {
                $signup->setContent("wrong_email", "L'indirizzo email non è valido <br>");
            }  
        }

        if (isset ($_GET['not_unique'])) {
            $param = $_GET['not_unique'];
            if ($param == 1) {
                $signup->setContent("not_unique", "Il nickname e/o l'email sono già stati utilizzati <br>");
            }  
        }

        $frame_public->setContent("contenuto", $signup->get());
        $frame_public->close();
    }
?>