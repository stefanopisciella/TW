<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/dbms.inc.php";
    require "include/utils_dbms.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/dettaglio-cane";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }
   
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/dettaglio-cane.html"); 
    $slides = new Template("skins/slide-cane.html"); 


    global $mysqli;

    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        
        if(isset($_GET['id'])) {
            $id_cane = $_GET['id']; 

            // INIZIO injection opzioni razza
            $opzioni_razza = new Template("skins/opzioni-razza.html");

            $query = "SELECT ID, nome FROM razza";
 
            try {
                $oid = $mysqli->query($query);
            }
            catch (Exception $e) {
                // REMOVE
                echo $e;
                throw new Exception("{$mysqli->errno}");
            }
 
            while($row = mysqli_fetch_array($oid)) {
                $opzioni_razza->setContent("nome_razza", $row['nome']);
                $opzioni_razza->setContent("id", $row['ID']);
            }

            $item->setContent("opzioni_razza", $opzioni_razza->get());
            // FINE injection opzioni razza
            
            // inizio query
            $query_info_cane = "SELECT * FROM cane WHERE ID = '{$id_cane}';";

            try {
                $oid = $mysqli->query($query_info_cane);
            }
            catch (Exception $e) {
                // REMOVE
                echo $e;
                throw new Exception("{$mysqli->errno}");
            }

            $info_cane = $oid->fetch_all(MYSQLI_ASSOC);
            // fine query
            
            $item->setContent("nome", $info_cane[0]["nome"]);
            $item->setContent("descrizione", $info_cane[0]["presentazione"]);
            $item->setContent("sesso", $info_cane[0]["sesso"]);
            $item->setContent("razza", $info_cane[0]["razza"]);
            
            // INIZIO sistemazione stringa età
            if (strlen($info_cane[0]['eta']) == 3) {
                // caso in cui il cane ha un età a due cifre
                $eta = substr($info_cane[0]['eta'], 0, 2);
            } else {
                // caso in cui il cane ha un età con un'unica cifra
                $eta = substr($info_cane[0]['eta'], 0, 1);
            }
            $item->setContent("eta", $eta); 

            $anni_mesi = substr($info_cane[0]['eta'], -1, 1); 
            if(strcmp($anni_mesi, 'a') == 0) {
                // caso in cui l'età è espressa in anni
                $item->setContent("anni_mesi", "anni"); 
            } else {
                // caso in cui l'età è espressa in mesi
                $item->setContent("anni_mesi", "mesi"); 
            }
            // FINE sistemazione stringa età

            // sistemazione tipologia di adozione
            if($info_cane[0]["distanza"] == true) {
                $item->setContent("distanza", "A distanza");
            } else {
                $item->setContent("distanza", "Non a distanza");
            } 
            
            $item->setContent("chip", $info_cane[0]["chip"]);
            $item->setContent("taglia", $info_cane[0]["taglia"]);
            $item->setContent("descrizione", $info_cane[0]["presentazione"]);

            // caricamento delle immagini del cane
            
            // query per trovare le immagini del cane in questione
            $oid = $mysqli->query("SELECT `path` as img FROM immagine WHERE ID_cane = '{$id_cane}';");

            if (!$oid) {
                echo "Error {$mysqli->errno}: {$mysqli->error}"; exit;
            }

            while($row = mysqli_fetch_array($oid)) {
                $slides->setContent("path", $row['img']);
            }

            $item->setContent("slides", $slides->get());

            $main->setContent("nome_cognome", initialize_frame());

            $not = new Template("skins/notifiche.html");
    
            $notifiche = notifiche();
        
            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }
        
            $main->setContent("notifiche", $not->get());

        } else {
            exit;
        }

        
        // INIZIO gestione visualizzazione di messaggi di errore
        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $item->setContent("error", "Non tutti i campi sono stanti compilati");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $main->setContent("notifiche", $not->get());

            $main->setContent("nome_cognome", initialize_frame());
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        if (isset ($_GET['empty_select']) && $_GET['empty_select'] == 1) {
            $item->setContent("error", "Non tutte le opzioni sono state selezionate");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $main->setContent("notifiche", $not->get());

            $main->setContent("nome_cognome", initialize_frame());
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        if (isset ($_GET['invalid_chip']) && $_GET['invalid_chip'] == 1) {
            $item->setContent("error", "Il numero chip può avere al massimo 15 cifre");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $main->setContent("notifiche", $not->get());

            $main->setContent("nome_cognome", initialize_frame());
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        if (isset ($_GET['wrong_age']) && $_GET['wrong_age'] == 1) {
            $item->setContent("error", "L'età inerita non è valida");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $main->setContent("notifiche", $not->get());

            $main->setContent("nome_cognome", initialize_frame());
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }
        // FINE gestione visualizzazione di messaggi di errore

        $main->setContent("contenuto", $item->get());
        $main->close();

    } else {
        // caso in cui l'admin fa la submit delle modifiche (premendo il pulsante "Salva") apportate ai dettagli del cane

        // $_POST['id'] contiene l'id del cane
        if(!isset($_POST['id'])) {
            exit;
        }

        $id_cane = $_POST['id'];
        $nome = $_POST['nome'];
        $razza = $_POST['razza'];
        $eta = $_POST['eta'];
        $anni_mesi = $_POST['anni_mesi'];
        $taglia = $_POST['taglia'];
        $sesso = $_POST['sesso'];
        $chip = $_POST['chip'];
        $descrizione = $_POST['descrizione'];

        $param_name = 'id=';

        // controlla che il nome non sia vuoto
        if (!isset($nome) || strlen(trim($nome)) == 0) {
            // nome vuoto
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_fields=1');
            exit;
        }

        if (!isset($chip) || strlen(trim($chip)) == 0) {
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_fields=1');
            exit;
        } else {
            // controlla che il numero di chip non sia una stringa più lunga di 15 caratteri
            if(strlen(trim($chip)) > 15) {
                header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&invalid_chip=1');
                exit;  
            }
        }

        if (!isset($descrizione) || strlen(trim($descrizione)) == 0) {
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_fields=1');
            exit;
        }

        // INIZIO controllo validità età
        if (!isset($eta) || strlen(trim($eta)) == 0)
        {
            // età vuota
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_fields=1');
            exit;
        } else {
            // età non vuota
            $eta = trim($eta);
              
            // controlla che l'età sia numerica, intera e non negativa
            if(is_numeric($eta) && (int) $eta > 0) {
                // età valida
                $eta = (int) $eta;
            } else {
                // età non valida
                header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&wrong_age=1');
                exit;  
            }
        }
        
        if (empty($anni_mesi)){
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_select=1');
            exit;
        } else {
            if($anni_mesi == 'a') {
                // caso in cui si vuole esprimere l'eta in anni
                $eta = (string) $eta . 'a';
            } else {
                // caso in cui si vuole esprimere l'eta in mesi
                $eta = (string) $eta . 'm';
            }
        }
        // FINE controllo validità età

        if (empty($razza)){
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_select=1');
            exit;
        }

        if (empty($taglia)){
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_select=1');
            exit;
        }

        if (empty($sesso)){
            header('Location: dettaglio-cane.php?' . $param_name . $id_cane . '&empty_select=1');
            exit;
        }
       
        try {
            $query = "UPDATE cane SET nome='{$nome}', sesso='{$sesso}', eta='{$eta}', razza='{$razza}', taglia='{$taglia}', presentazione='{$descrizione}', chip='{$chip}' WHERE ID = {$id_cane};";
            $mysqli->query($query);
            
            header("Location: cani-in-struttura.php?success=1");
         }
         catch (Exception $e) {
            echo $e;
            throw new Exception("{$mysqli->errno}");
        }
    }
?>
