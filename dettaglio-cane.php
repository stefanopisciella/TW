<?php
    require "include/dbms.inc.php";
    require "frame-public.php";
    require "include/utils_dbms.php";

    $dettaglio_cane = new Template("skins/dettaglio-cane.html");

    $max_char_mex = 1500; 
    
    // injection informazioni cane
    if(isset($_REQUEST['id'])) {
        // la variabile $_REQUEST permette al server di ricervere l'id del cane dal client C 
        // sia quando C visita la pagina con il metodo GET che con il metodo POST
        $id_cane = $_REQUEST['id'];  
        
        $query_info_cane = "SELECT nome, presentazione, sesso, eta, razza, taglia, chip FROM cane WHERE ID = '{$id_cane}';";

        try {
            $oid = $mysqli->query($query_info_cane);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        $info_cane = $oid->fetch_all(MYSQLI_ASSOC); 

        $dettaglio_cane->setContent("nome", $info_cane[0]["nome"]);
        $dettaglio_cane->setContent("presentazione", $info_cane[0]["presentazione"]);
        $dettaglio_cane->setContent("sesso", $info_cane[0]["sesso"]);
        // sistemazione stringa età
        $eta = $info_cane[0]["eta"];
        if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
        else $eta = substr($eta, 0, -1)." mesi";
        $dettaglio_cane->setContent("eta", $eta);
        $dettaglio_cane->setContent("razza", $info_cane[0]["razza"]);
        $dettaglio_cane->setContent("taglia", $info_cane[0]["taglia"]);
    }

    // GESTIONE FORM RICHIESTA INFORMAZIONE
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // caso in cui il client carica la pagina con il metodo GET

        // controlla che l'utente sia loggato
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] >= 1){
            // utente loggato  
            autocompila_textfield();
        }
        
        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $dettaglio_cane->setContent("error", "Non tutti i campi sono stanti compilati");
            $head->setContent("contenuto", $dettaglio_cane->get());
            $head->close();
            exit; 
        }
        
        if(isset($_GET['out_of_limit']) && $_GET['out_of_limit'] == 1){
            $dettaglio_cane->setContent("error", "Il messaggio non può avere più di $max_char_mex caratteri");
            $head->setContent("contenuto", $dettaglio_cane->get());
            $head->close();
            exit;
        } 

        if (isset($_GET['wrong_email']) && $_GET['wrong_email'] == 1){
            $dettaglio_cane->setContent("error", "L'indirizzo email non è valido");
            $head->setContent("contenuto", $dettaglio_cane->get());
            $head->close();
            exit; 
        }

        $head->setContent("contenuto", $dettaglio_cane->get());
        $head->close();
    } else {
        // caso in cui l'utente ha già visionato la pagina e fa "submit" del messaggio con
        // il metodo POST oppure fa la richiesta di affido
        
        // quando il server S riceve una richiesta verso dettaglio-cane.php con il metodo 
        // post da parte del client C, S deve ricevere da C l'ID del cane di cui C sta 
        // visualizzando i dettagli oppure per il quale vuole richiedere l'affido. In questo
        // modo S sarà in grado di reindirizzare C alla pagina di dettaglio-cane relativa al 
        // cane con ID di cui sopra
        
        // controlla se il client ha effettuato la richiesta di affido
        if(isset($_POST['id_cane_affido']) && $_POST['id_cane_affido'] >= 1) {
            // caso in cui il client effettua la richiesta di affido

            if(!isset($_SESSION['user_id'])) {
                // caso in cui l'utente C non è loggato ==>non può effettuare la richiesta di
                // affido. Pertanto C viene reindirizzato alla login
                header("Location: login.php");
                exit; 
            } else {
                $user_id = $_SESSION['user_id']; 
                $id_cane = $_POST['id_cane_affido'];
                // REMOVE
                echo $id_cane;
                $actual_date = date("Y/m/d");

                $affido = [$user_id, $id_cane, "'".$actual_date."'", "NULL"];

                try {
                    insert_query('richiesta_adozione', $affido);
                    header("Location: operation-success.php?");
                } catch (Exception $e){

                }
            }
        } else {
            // caso in cui il client effettua la richiesta di informazione
            if(isset($_POST['id_cane_info']) && $_POST['id_cane_info'] >= 1) {
                // ?
                $param_value = $_POST['id_cane_info'];
                $param_name = 'id=';
                
                $messaggio = $_POST['message'];
                $chip = get_chip_query($param_value);

                // controlla che il messaggio non sia vuoto
                if (!isset($messaggio) || strlen(trim($messaggio)) == 0)
                {
                    // messaggio vuoto
                    header('Location: dettaglio-cane.php?' . $param_name . $param_value . '&empty_fields=1');
                    exit;
                } else {
                    // messaggio non vuoto
                    $messaggio = trim($messaggio);
                    // controlla che il numero di caratteri del messaggio non superi il limite
                    if(strlen($messaggio) > $max_char_mex)
                    {
                        header('Location: dettaglio-cane.php?' . $param_name . $param_value . '&out_of_limit=1');
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
                        header('Location: dettaglio-cane.php?' . $param_name . $param_value . '&empty_fields=1');
                        exit;
                    }
            
                    if (!isset($nome) || strlen(trim($nome)) == 0){
                        header('Location: dettaglio-cane.php?' . $param_name . $param_value . '&empty_fields=1');
                        exit;
                    }

                    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        header('Location: dettaglio-cane.php?' . $param_name . $param_value . '&wrong_email=1');
                        exit;
                    }
                } else {
                    // utente loggato ==> recuperiamo il nome e la email del client dal DB
                    $utente = get_user($_SESSION['user_id']); 
            
                    $user_id = $_SESSION['user_id'];
                    $email = $utente['email'];
                    $nome = $utente['nome'];
                    $cognome = $utente['cognome'];
                    $telefono = $utente['telefono'];
                }
        
                $actual_date = date("Y/m/d");
                $richiesta_info = [$user_id, "'".$nome."'", "'".$cognome."'", "'".$email."'", "'".$actual_date."'", "'".$chip."'", "'".$messaggio."'"];
                try {
                    insert_query('richiesta_info', $richiesta_info);
                    header('Location: dettaglio-cane.php?' . $param_name . $param_value);
                } catch (Exception $e){
                    echo $e;
                }
            }
        }
    }

    function autocompila_textfield(){
        $utente = get_user($_SESSION['user_id']);            
        
        // vengono inseriti nome ed email del client nei relativi textfield
        $GLOBALS['dettaglio_cane']->setContent("nome_utente", $utente['nome']);
        $GLOBALS['dettaglio_cane']->setContent("email", $utente['email']);
        // per rendere non editabili i textfield relativi all'email e al nome utente
        $GLOBALS['dettaglio_cane']->setContent("readonly", "readonly");
    }

    function get_chip_query($id_cane) {
        $query_chip_cane = "SELECT chip FROM cane WHERE ID = '{$id_cane}';";

        try {
            $oid = $GLOBALS['mysqli']->query($query_chip_cane);
        }
        catch (Exception $e) {
            throw new Exception("{$GLOBALS['mysqli']->errno}");
        }

        $query_result = $oid->fetch_all(MYSQLI_ASSOC); 
        $chip = $query_result[0]['chip'];
        return $chip;
    }
?>