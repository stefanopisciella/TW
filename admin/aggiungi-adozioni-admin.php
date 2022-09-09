<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/utils_dbms.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/aggiungi-adozioni";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }

    $max_img_size = 5000000;
    global $mysqli;

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/aggiungi-adozioni-admin.html");

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // caso in cui il client carica la pagina con il metodo GET

        // INIZIO injection opzioni razza
        $opzioni_razza = new Template("skins/opzioni-razza.html");

        $query = "SELECT ID, nome FROM razza";
 
        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }
 
        while($row = mysqli_fetch_array($oid)) {
         
            $opzioni_razza->setContent("nome_razza", $row['nome']);
            $opzioni_razza->setContent("id", $row['ID']);
 
        }
 
        $item->setContent("opzioni_razza", $opzioni_razza->get());
        // FINE injection opzioni razza


        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $item->setContent("error", "Non tutti i campi sono stati compilati");

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

        // INIZIO gestione visualizzazione di messaggi di errore relativi al caricamento delle immagini
        if (isset ($_GET['no_img']) && $_GET['no_img'] == 1) {
            $item->setContent("error", "Deve essere caricata almeno una foto relativa all'adozione");

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

        if (isset ($_GET['img_size_error']) && $_GET['img_size_error'] == 1) {
            $readble_size = $max_img_size/1000000; 
            
            $item->setContent("error", "Ciascuna foto non può avere dimensioni maggiori di " . $readble_size . "MB");

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

        if (isset ($_GET['wrong_format']) && $_GET['wrong_format'] == 1) {
            $item->setContent("error", "Ciascuna foto deve avere formato '.png' oppure '.jpg'");

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

        if (isset ($_GET['img_upload_error']) && $_GET['img_upload_error'] == 1) {
            $item->setContent("error", "Si è verificato un errore durante il caricamento di un'immagine");

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
        // FINE gestione visualizzazione di messaggi di errore relativi al caricamento delle immagini
    } else {
        // caso in cui l'utente ha già visionato la pagina e fa "submit" del messaggio
        $nome = $_POST['nome'];
        $razza = $_POST['razza'];
        $eta = $_POST['eta'];
        $anni_mesi = $_POST['anni_mesi'];
        $taglia = $_POST['taglia'];
        $sesso = $_POST['sesso'];
        $chip = $_POST['chip'];
        $descrizione = $_POST['descrizione'];
        $a_distanza = $_POST['flexRadioDefault'];

        // controlla che il nome non sia vuoto
        if (!isset($nome) || strlen(trim($nome)) == 0) {
            // nome vuoto
            header("Location: aggiungi-adozioni-admin.php?empty_fields=1");
            exit;
        }

        if (!isset($chip) || strlen(trim($chip)) == 0) {
            header("Location: aggiungi-adozioni-admin.php?empty_fields=1");
            exit;
        } else {
            // controlla che il numero di chip non sia una stringa più lunga di 15 caratteri
            if(strlen(trim($chip)) > 15) {
                header("Location: aggiungi-adozioni-admin.php?invalid_chip=1");
                exit;  
            }
        }

        if (!isset($descrizione) || strlen(trim($descrizione)) == 0) {
            header("Location: aggiungi-adozioni-admin.php?empty_fields=1");
            exit;
        }

        // INIZIO controllo validità età
        if (!isset($eta) || strlen(trim($eta)) == 0)
        {
            // età vuota
            header("Location: aggiungi-adozioni-admin.php?empty_fields=1");
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
                header("Location: aggiungi-adozioni-admin.php?wrong_age=1");
                exit;  
            }
        }
        
        if (empty($anni_mesi)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
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
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
            exit;
        }

        if (empty($taglia)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
            exit;
        }

        if (empty($sesso)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
            exit;
        }

        if (!isset($a_distanza)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
            exit;
        }

        $imgs_path = upload_images();

        $cane = ["'".$nome."'", "'".$sesso."'", "'".$eta."'", "'".$razza."'", "'".$taglia."'", "'".$descrizione."'", "'".$chip."'", $a_distanza, 0];
        
        try {
            if(isset($imgs_path)) {
                // caso in cui il client carica almeno un immagine valida ==> aggiunge la nuova adozione ed i path path delle immagini nel DB 
                $id_cane = insert_query('cane', $cane);

                // inserimento nuova razza nel DB
                // check che nel DB non sia già presente quella razza
                // to lower case $razza
                $razza = strtolower($razza);
                if (!check_razza($razza)) {
                    $param_razza = array("'".$razza."'");
                    insert_query('razza', $param_razza);
                }

                for($i=0;$i<sizeof($imgs_path);$i++) {
                    $imgs_path[$i] = 'admin/' . $imgs_path[$i];
                    $immagine = [$id_cane, "'".$imgs_path[$i]."'", 1];
                    insert_query('immagine', $immagine);
                }
                header("Location: aggiungi-adozioni-admin.php?success=1");
            }

        } catch (Exception $e){
            // REMOVE
            echo $e;
        }

    }

    $main->setContent("nome_cognome", initialize_frame());

    $not = new Template("skins/notifiche.html");

    $notifiche = notifiche();

    foreach($notifiche as $notifica) {
        $not->setContent("nome", $notifica['nome']);
        $not->setContent("anteprima", $notifica['anteprima']);
    }

    $main->setContent("notifiche", $not->get());

    $main->setContent("contenuto", $item->get());
    $main->close(); 

    function upload_images() {
        $images_dir = "immagini/";   

        // controlla che almeno un immagine sia stata caricata dal client
        if(!isset($_FILES['image']['tmp_name'][0]) || !is_uploaded_file($_FILES['image']['tmp_name'][0]) || !file_exists($_FILES['image']['tmp_name'][0])) {
            // caso in cui l'utente non ha caricato alcuna immagine per l'adozione
            header("Location: aggiungi-adozioni-admin.php?no_img=1");
            return null;
        }
        
        // controlla quante immagini il client ha caricato
        $imgs = array();
        $imgs = $_FILES['image']['name'];
        $num_img = sizeof( (array) $imgs);
        
        $path_imgs = array();
        for($i=0;$i<$num_img;$i++){
            // scorre le immagini caricate dal client
            
            // fissa il vincolo di dimensioni per il quale non è possibile caricare immagini
            // con dimensione maggiore ai 5MB
            if ($_FILES["image"]["size"][$i] > $GLOBALS['max_img_size']) {
                // caso in cui una delle immagini caricate dal client supera il limite
                // di dimensioni
                header("Location: aggiungi-adozioni-admin.php?img_size_error=1");
                exit;  
            }
                
            // fissa il vincolo per il quale è consentito caricare soltanto immagini con
            // formato .png oppure .jpeg
            $type_img = $_FILES["image"]["type"][$i]; 
            if($type_img == "image/jpeg" || $type_img == "image/jpg" ) {
                // si assegna all'immagine un nome casuale per garantire l'univocità dei 
                // nomi delle immagini (azione necessaria per evitare errori a livello di 
                // filesystem)
                if($type_img == "image/png") {
                    // si assegna all'immagine un nome casuale per garantire l'univocità 
                    // dei nomi delle immagini (azione necessaria per evitare errori a 
                    // livello di filesystem)
                    $path_img = $images_dir . "img_" . random_int(1, 10000) . "." . 'png';
                    array_push($path_imgs, $path_img);
                } else {
                    // si assegna all'immagine un nome casuale per garantire l'univocità 
                    // dei nomi delle immagini (azione necessaria per evitare errori a 
                    // livello di filesystem)
                    $path_img = $images_dir . "img_" . random_int(1, 10000) . "." . 'jpeg';
                    array_push($path_imgs, $path_img); 
                }
            } else {
                // caso in cui una delle immagini ha un formato non consentito
                header("Location: aggiungi-adozioni-admin.php?wrong_format=1");
                exit;  
            }
        }
          
        for($i=0;$i<$num_img;$i++){
            // "tmp_name" è il path dove il server salva temporaneamente il file caricato
            if (!move_uploaded_file($_FILES["image"]["tmp_name"][$i], $path_imgs[$i])) {
                header("Location: aggiungi-adozioni-admin.php?img_upload_error=1");            
            }
        }

        return $path_imgs;
    }

    function check_razza($razza) {
        global $mysqli;

        $query = "SELECT COUNT(nome) AS cont FROM razza WHERE nome='$razza';";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        $trovato = $oid->fetch_assoc()['cont'];

        return $trovato;
    }

?>

