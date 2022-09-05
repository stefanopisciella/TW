<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    session_start();
    $nome_script = "admin/dettaglio-adozione";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }
    
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/dettaglio-adozione-admin.html");
    $slides = new Template("skins/slide-cane.html"); 

    global $mysqli;

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        
        if(isset($_GET['id'])) {
            $id_cane = $_GET['id']; 

            // inizio query
            $query_info_cane = "SELECT * FROM cane WHERE ID = '{$id_cane}';";

            try {
                $oid = $mysqli->query($query_info_cane);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            $info_cane = $oid->fetch_all(MYSQLI_ASSOC);
            // fine query
      
            $item->setContent("nome", $info_cane[0]["nome"]);
        
            $item->setContent("sesso", $info_cane[0]["sesso"]);
      
            // sistemazione stringa età
            $eta = $info_cane[0]['eta'];
            if (substr($eta, -1) == 'a') 
                $eta = substr($eta, 0, -1)." anni";
            else 
                $eta = substr($eta, 0, -1)." mesi";

            $item->setContent("eta", $eta);
            $item->setContent("razza", $info_cane[0]["razza"]);
            $item->setContent("chip", $info_cane[0]["chip"]);
            $item->setContent("taglia", $info_cane[0]["taglia"]);

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

            if(isset($_SESSION['previous_page']) && $_SESSION['previous_page'] == 'storico-adottati') {
                // caso in cui il client arriva in questa pagina dalla schermata 'storico-adottati' ==> bisogna far comparire il pulsante per scaricare il certificato di adozione
                $item->setContent("download-file", '<a href="#" class="btn btn-primary" style="max-width: 45%">Documento di adozione</a>');

            } else {
                // caso in cui il client arriva in questa pagina dalla schermata 'lista-richieste' ==> bisogna far comparire il pulsante che permette l'upload del certificato di adozione
                $item->setContent("pick-file", '<input type="file" name="certificate" class="form-control" style="width: 56%;" id="inputGroupFile04"  aria-describedby="inputGroupFileAddon04" aria-label="Upload" accept=".doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                                <button type="submit" class="btn btn-primary me-1 mb-1">Carica</button>');

            }

            // visualizzzione di messaggi di errore relativi al caricamento delle immagini
            if(isset($_GET['cert_size_error']) && $_GET['cert_size_error'] == 1){
            
                $item->setContent("error", "Il certificato non può avere dimensioni maggiori 5 MB");
                $main->setContent("contenuto", $item->get());
                $main->close();
                exit;
            } 

            if (isset ($_GET['cert_upload_error']) && $_GET['cert_upload_error'] == 1) {
                $item->setContent("error", "Non è stato possibile caricare il certificato");
                $main->setContent("contenuto", $item->get());
                $main->close();
                exit; 
            }

            if (isset ($_GET['wrong_format']) && $_GET['wrong_format'] == 1) {
                $item->setContent("error", "Il certificato deve essere in formato '.pdf'");
                $main->setContent("contenuto", $item->get());
                $main->close();
                exit; 
            }

            if (isset ($_GET['no_cert']) && $_GET['no_cert'] == 1) {
                $item->setContent("error", "Non è stato caricato alcun certificato");
                $main->setContent("contenuto", $item->get());
                $main->close();
                exit; 
            }
        
        } else {
            exit;
        }








    } else {
        // caso in cui l'admin ha fatto la submit del certificato di adozione

        // REMOVE
        echo "NEIN";
        if(isset($_POST['id'])) {
            // "$id" contiene l'id del cane per il quale si vuole fare l'upload del certificato
            $id = $_POST['id'];
            // REMOVE
            echo "NEIN";
            $cert_path = upload_certificate($id); 
        }




    }
    
    
    
    
    $main->setContent("contenuto", $item->get());
    $main->close(); 

    function upload_certificate($param_value) {
        // REMOVE
        echo "OK";
        
        $param_name = 'id=';
        
        // controlla se il client ha caricato o meno il certificato
        if(isset($_FILES['certificate']['tmp_name']) && is_uploaded_file($_FILES['certificate']['tmp_name']) && file_exists($_FILES['certificate']['tmp_name'])) {
            // caso in cui l'utente ha caricato il certificato

            // fissa il vincolo di dimensioni per il quale non è possibile caricare un certifiicato con dimensione maggiore ai 5MB
            if ($_FILES["certificate"]["size"] > 5000000) {
                header('Location: dettaglio-adozione-admin.php?' . $param_name . $param_value . '&' . 'cert_size_error=1');
                exit;
            }
        } else {
            // caso in cui l'utente non ha caricato il certificato
            header('Location: dettaglio-adozione-admin.php?' . $param_name . $param_value . '&' . 'no_cert=1');
            return null;
        }
  
        // fissa il vincolo per il quale è consentito caricare soltanto certificati con formato .pdf
        $imageFileType = $_FILES["certificate"]["type"];
        // REMOVE
        echo "CAZZO" . $imageFileType;

        if($imageFileType == ".pdf") {
            $extension = "pdf";
        } else {
            header('Location: dettaglio-adozione-admin.php?' . $param_name . $param_value . '&' . 'wrong_format=1');
            exit;  
        }
  
        $cert_dir = "certificati_adozione/";   
        // si assegna al certificato un nome casuale per garantire l'univocità dei nomi dei certificati (azione necessaria per evitare errori a livello di filesystem)
        $path_cert = $cert_dir . "cert_" . random_int(1, 10000) . "." . $extension;
  
        // "tmp_name" è il path dove il server salva temporaneamente il file caricato
        if (!move_uploaded_file($_FILES["certificate"]["tmp_name"], $path_cert)) {
            header('Location: dettaglio-adozione-admin.php?' . $param_name . $param_value . '&' . 'cert_upload_error=1');
        }

        return $path_cert;
    }
?>

