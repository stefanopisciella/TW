<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/utils_dbms.php";

    session_start();
    $nome_script = "admin/aggiungi-adozioni-admin";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        echo "Unauthorized";
        exit;   
    }

    $max_img_size = 5000000;
    global $mysqli;

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/aggiungi-adozioni-admin.html");

    // injection opzioni razze nel relativo filtro di ricerca
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
 
    $main->setContent("opzioni_razza", $opzioni_razza->get());
    
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // caso in cui il client carica la pagina con il metodo GET

        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $item->setContent("error", "Non tutti i campi sono stanti compilati");
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        if (isset ($_GET['empty_select']) && $_GET['empty_select'] == 1) {
            $item->setContent("error", "Non tutte le opzioni sono state selezionate");
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        if (isset ($_GET['invalid_chip']) && $_GET['invalid_chip'] == 1) {
            $item->setContent("error", "Il numero chip può avere al massimo 15 cifre");
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        // visualizzzione di messaggi di errore relativi al caricamento delle immagini
        if (isset ($_GET['no_img']) && $_GET['no_img'] == 1) {
            $item->setContent("error", "Deve essere caricata almeno una foto relativa all'adozione");
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        // visualizzzione di messaggi di errore relativi al caricamento delle immagini
        if (isset ($_GET['img_size_error']) && $_GET['img_size_error'] == 1) {
            $readble_size = $max_img_size/1000000; 
            
            $item->setContent("error", "Ciascuna foto non può avere dimensioni maggiori di " . $readble_size . "MB");
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        // visualizzzione di messaggi di errore relativi al caricamento delle immagini
        if (isset ($_GET['wrong_format']) && $_GET['wrong_format'] == 1) {
            $item->setContent("error", "Ciascuna foto deve avere formato '.png' oppure '.jpg'");
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

        // visualizzzione di messaggi di errore relativi al caricamento delle immagini
        if (isset ($_GET['img_upload_error']) && $_GET['img_upload_error'] == 1) {
            $item->setContent("error", "Si è verificato un errore durante il caricamento dell'immagine/immagini");
            $main->setContent("contenuto", $item->get());
            $main->close();
            exit; 
        }

    } else {
        // caso in cui l'utente ha già visionato la pagina e fa "submit" del messaggio
        $nome = $_POST['nome'];
        $razza = $_POST['razza'];
        $eta = $_POST['eta'];
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

        if (!isset($razza)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
            exit;
        }

        // controllo correttezza età

        if (!isset($taglia)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
            exit;
        }

        if (!isset($sesso)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
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

        if (!isset($a_distanza)){
            header("Location: aggiungi-adozioni-admin.php?empty_select=1");
            exit;
        }

        $imgs_path = upload_images();

        $cane = ["'".$nome."'", "'".$sesso."'", $eta, "'".$razza."'", "'".$taglia."'", "'".$descrizione."'", "'".$chip."'", $a_distanza, "NULL"];
        
        try {
            $id_cane = insert_query('cane', $cane);
            header("Location: aggiungi-adozioni-admin.php?success=1");
            // REMOVE
            echo "query ereguita";
        } catch (Exception $e){
            // REMOVE
            echo $e;
        }

    }

    $main->setContent("contenuto", $item->get());
    $main->close(); 

    function upload_images() {
        $images_dir = "immagini/";   

        if(!isset($_FILES["image"]["tmp_name"])) {
            // caso in cui l'utente non ha caricato alcuna immagine per l'articolo
            return null;
            header("Location: aggiungi-adozioni-admin.php?no_img=1");
            exit; 
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
                $path_img = $images_dir . "img_" . random_int(1, 10000) . "." . 'jpeg';
                array_push($path_imgs, $path_img);
            } else {
                if($type_img == "image/png") {
                    // si assegna all'immagine un nome casuale per garantire l'univocità 
                    // dei nomi delle immagini (azione necessaria per evitare errori a 
                    // livello di filesystem)
                    $path_img = $images_dir . "img_" . random_int(1, 10000) . "." . 'png';
                    array_push($path_imgs, $path_img);
                } else {
                    // caso in cui una delle immagini ha un formato non consentito
                    header("Location: aggiungi-adozioni-admin.php?wrong_format=1");
                    exit;  
                }
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

?>

