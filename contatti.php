<?php
    require "include/utils_dbms.php";
    require "frame-public.php";

    $contatti = new Template("skins/contatti.html");
  
    $max_char_mex = 1500; 
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // caso in cui il client carica la pagina con il metodo GET

        // controlla che l'utente sia loggato
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] >= 1){
            // utente loggato  
            autocompila_textfield();
        }
        
        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $contatti->setContent("error", "Non tutti i campi sono stanti compilati");
            $head->setContent("contenuto", $contatti->get());
            $head->close();
            exit; 
        }
        
        if(isset($_GET['out_of_limit']) && $_GET['out_of_limit'] == 1){
            $contatti->setContent("error", "Il messaggio non può avere più di $max_char_mex caratteri");
            $head->setContent("contenuto", $contatti->get());
            $head->close();
            exit;
        } 

        if (isset($_GET['wrong_email']) && $_GET['wrong_email'] == 1){
            $contatti->setContent("error", "L'indirizzo email non è valido");
            $head->setContent("contenuto", $contatti->get());
            $head->close();
            exit; 
        }

        $head->setContent("contenuto", $contatti->get());
        $head->close();
    } else {
        // caso in cui l'utente ha già visionato la pagina e fa "submit" del messaggio
        $messaggio = $_POST['message'];

        // controlla che il messaggio non sia vuoto
        if (!isset($messaggio) || strlen(trim($messaggio)) == 0)
        {
            // messaggio vuoto
            header("Location: contatti.php?empty_fields=1");
            exit;
        } else {
            // messaggio non vuoto
            $messaggio = trim($messaggio);
            // controlla che il numero di caratteri del messaggio non superi il limite
            if(strlen($messaggio) > $max_char_mex)
            {
                header("Location: contatti.php?out_of_limit=1");
                exit;
            }
        }

        if (!isset($_SESSION['user_id'])){
            // utente non loggato ==> bisogna recuperare dalla form il nome e la email del
            // client. Di conseguenza è anche necessario controllare la validità dell'email 
            // inserita e che il campo del nome non sia nullo 
            $email = $_POST['email'];
            $nome = $_POST['name'];
            $user_id = "NULL";

            if (!isset($email) || strlen(trim($email)) == 0){
                header("Location: contatti.php?empty_fields=1");
                exit;
            }
            
            if (!isset($nome) || strlen(trim($nome)) == 0){
                header("Location: contatti.php?empty_fields=1");
                exit;
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: contatti.php?wrong_email=1");
                exit;
            }
        } else {
            // utente loggato ==> recuperiamo il nome e la email del client dal DB
            $utente = get_user($_SESSION['user_id']); 
            
            $user_id = $_SESSION['user_id'];
            $email = $utente['email'];
            $nome = $utente['nome'];
        }
        
        $actual_date = date("Y/m/d");
        $richiesta_info = [$user_id, "'".$nome."'", "'".$email."'", "'".$actual_date."'", "NULL", "'".$messaggio."'"];
        try {
            insert_query('richiesta_info', $richiesta_info);
            header("Location: contatti.php?");
        } catch (Exception $e){
        
        }
    }
    
    function autocompila_textfield(){
        $utente = get_user($_SESSION['user_id']);            
        
        // vengono inseriti nome ed email del client nei relativi textfield
        $GLOBALS['contatti']->setContent("nome", $utente['nome']);
        $GLOBALS['contatti']->setContent("email", $utente['email']);
        // per rendere non editabili i textfield relativi all'email e al nome utente
        $GLOBALS['contatti']->setContent("readonly", "readonly");
    }
?>