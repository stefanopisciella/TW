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
            header("Location: aggiungi-adozioni-admin.php?empty_fields=1");
            exit;
        }

        // controllo correttezza età

        if (!isset($taglia)){
            header("Location: aggiungi-adozioni-admin.php?empty_fields=1");
            exit;
        }

        if (!isset($sesso)){
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

        if (!isset($a_distanza)){
            header("Location: aggiungi-adozioni-admin.php?empty_fields=1");
            exit;
        }

        $cane = ["'".$nome."'", "'".$sesso."'", $eta, "'".$razza."'", "'".$taglia."'", "'".$descrizione."'", "'".$chip."'", $a_distanza, "NULL"];
        
        try {
            insert_query('cane', $cane);
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

    function upload_image() {
        // fissa il vincolo di dimensioni per il quale non è possibile caricare immagini con
        // dimensione maggiore ai 5MB
        if(isset($_FILES["image"]["tmp_name"])) {
            if ($_FILES["image"]["size"] > $GLOBALS['max_img_size']) {
            header("Location: aggiungi-adozioni.admin.php?img_size_error=1");
            exit;
        } else {
                // caso in cui l'utente non ha caricato alcuna immagine per l'articolo
                return "img/blog/default_img.jpg"; // è il path dell'immagine di default
            }
        }
  
        // fissa il vincolo per il quale è consentito caricare soltanto immagini con formato .png oppure .jpeg
        $imageFileType = $_FILES["image"]["type"];
        if($imageFileType == "image/jpeg") {
            $imageFileType = "jpeg";
        } else {
            if($imageFileType == "image/png") {
                $imageFileType = "png";
            }
            // estensione file non consentito ==> eccezione
        }
  
        $images_dir = "immagini/";   
        // si assegna all'immagine un nome casuale per garantire l'univocità dei nomi delle
        //  immagini (azione necessaria per evitare errori a livello di filesystem)
        $path_image = $images_dir . "img_" . random_int(1, 10000) . "." . $imageFileType;
  
        // "tmp_name" è il path dove il server salva temporaneamente il file caricato
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $path_image)) {
            header("Location: aggiungi-adozioni-admin.php?img_upload_error=1");            
        }

        return $path_image;
    }
?>

