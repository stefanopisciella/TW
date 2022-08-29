<?php
    require "include/utils_dbms.php";
    require "frame-public.php";

    // per il redirect allo script "donazioni" una volta effettuato il login
    $_SESSION['previous_page'] = 'donazioni';

    $donazioni = new Template("skins/donazioni.html");
  
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // caso in cui il client carica la pagina con il metodo GET

        // controlla che l'utente sia loggato
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] >= 1){
            // utente loggato  
            autocompila_textfield();
        }
        
        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $donazioni->setContent("error", "Non tutti i campi sono stanti compilati");
            $head->setContent("contenuto", $donazioni->get());
            $head->close();
            exit; 
        }
    
        if (isset($_GET['wrong_amount']) && $_GET['wrong_amount'] == 1){
            $donazioni->setContent("error", "L'importo della donazione deve essere un numero intero");
            $head->setContent("contenuto", $donazioni->get());
            $head->close();
            exit; 
        }

        if (isset($_GET['wrong_email']) && $_GET['wrong_email'] == 1){
            $donazioni->setContent("error", "L'indirizzo email non è valido");
            $head->setContent("contenuto", $donazioni->get());
            $head->close();
            exit; 
        }

        $head->setContent("contenuto", $donazioni->get());
        $head->close();
    } else {
        // caso in cui l'utente ha già visionato la pagina e fa "submit" del messaggio
        
        if (!isset($_SESSION['user_id'])){
            // utente non loggato ==> bisogna recuperare dalla form l'email del
            // client. Di conseguenza è anche necessario controllare la validità dell'email 
            // inserita
            $email = $_POST['email'];

            if (!isset($email) || strlen(trim($email)) == 0){
                header("Location: donazioni.php?empty_fields=1");
                exit;
            }
            
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: donazioni.php?wrong_email=1");
                exit;
            }
        } else {
            // utente loggato ==> recuperiamo l'email del client dal DB
            $utente = get_user($_SESSION['user_id']); 
            $email = $utente['email'];
        }
        
        $importo = $_POST['importo'];

        // controlla che l'importo non sia vuoto
        if (!isset($importo) || strlen(trim($importo)) == 0)
        {
            // importo vuoto
            header("Location: donazioni.php?empty_fields=1");
            exit;
        } else {
            // importo non vuoto
            $importo = trim($importo);
            
            // controlla che l'importo sia numerico, intero e non negativo
            if(is_numeric($importo) && (int) $importo > 0) {
                // importo valido
                $importo = (int) $importo;
            } else {
                // importo non valido
                header("Location: donazioni.php?wrong_amount=1");
                exit;  
            }
        }
        
        $actual_date = date("Y/m/d");
        $donazione = [$importo, "'".$email."'", "'".$actual_date."'"];
        try {
            insert_query('donazione', $donazione);
            header("Location: donazioni.php?success=1");
        } catch (Exception $e){
        
        }
    }
    
    function autocompila_textfield(){
        $utente = get_user($_SESSION['user_id']);
        $email = $utente['email'];            
        
        // viene inserita l'email del client nel relativo textfield
        $GLOBALS['donazioni']->setContent("email", $email);
        // per rendere non editabile il textfield relativo all'email
        $GLOBALS['donazioni']->setContent("readonly", "readonly");
    }
?>

