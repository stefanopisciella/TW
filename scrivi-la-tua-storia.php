<?php
require "include/dbms.inc.php";
require "include/php-utils/varie.php";
require "frame-public.php";
require "include/utils_dbms.php";

    if (!isset($_SESSION['user_id'])){
        // per il redirect allo script "scrivi-la-tua-storia" una volta effettuato il login
        $_SESSION['previous_page'] = 'scrivi-la-tua-storia';
        // se il client non è loggato, viene reindirizzato alla login
        header("Location: login.php");
        exit;  
    }

    $max_char_title = 100; 
    $max_img_size = 5000000;
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // caso in cui il client carica la pagina con il metodo GET
        $item = new Template("skins/scrivi-la-tua-storia.html");
    
        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $item->setContent("error", "Non tutti i campi sono stanti compilati");
            $head->setContent("contenuto", $item->get());
            $head->close();
            exit; 
        }

        if(isset($_GET['title_out_of_limit']) && $_GET['title_out_of_limit'] == 1){
            $item->setContent("error", "Il titolo non può avere più di $max_char_title caratteri");
            $head->setContent("contenuto", $item->get());
            $head->close();
            exit;
        } 

        if(isset($_GET['img_size_error']) && $_GET['img_size_error'] == 1){
            $readble_size = $max_img_size/1000000;
            
            $item->setContent("error", "L'immagine non può avere dimensioni maggiori di $readble_size" . "MB");
            $head->setContent("contenuto", $item->get());
            $head->close();
            exit;
        } 

        if (isset ($_GET['img_upload_error']) && $_GET['img_upload_error'] == 1) {
            $item->setContent("error", "Non è stato possibile caricare l'immagine");
            $head->setContent("contenuto", $item->get());
            $head->close();
            exit; 
        }

        $head->setContent("contenuto", $item->get());
        $head->close();
    } else {
        // caso in cui l'utente ha già visionato la pagina e fa "submit" dell'articolo e dell'
        // immagine dell'articolo

        $contenuto = $_POST['testo'];
        $id_utente = $_SESSION['user_id'];
        $autore = get_user($id_utente)['nickname'];
        $actual_date = date("Y/m/d");
        $id_categoria = 2;
        $categoria = "Le Vostre Storie";
        $titolo = $_POST['titolo'];

        // controlla che il testo dell'articolo non sia vuoto
        if (!isset($contenuto) || strlen(trim($contenuto)) == 0) {
            // testo vuoto
            header("Location: scrivi-la-tua-storia.php?empty_fields=1");
            exit;
        }

        // controlla che il titolo dell'articolo non sia vuoto
        if (!isset($titolo) || strlen(trim($titolo)) == 0)
        {
            // titolo vuoto
            header("Location: scrivi-la-tua-storia.php?empty_fields=1");
            exit;
        } else {
            // titolo non vuoto
            $titolo = trim($titolo);
            // controlla che il numero di caratteri del titolo non superi il limite
            if(strlen($titolo) > $max_char_title)
            {
                header("Location: scrivi-la-tua-storia.php?title_out_of_limit=1");
                exit;
            }
        }

        $path_img = upload_image();
        
        $articolo = [$id_utente, $id_categoria, "'".$titolo."'", "'".$contenuto."'", "'".$autore."'", "'".$actual_date."'", "'".$categoria."'", "'".$path_img."'"];
        
        try {
            $id_articolo = insert_query('articolo', $articolo);
            header('Location: articolo.php?art=' . $id_articolo);
        } catch (Exception $e){
            
        }
    }

    function upload_image() {
        // fissa il vincolo di dimensioni per il quale non è possibile caricare immagini con
        // dimensione maggiore ai 5MB
        if(isset($_FILES["image"]["tmp_name"])) {
            if ($_FILES["image"]["size"] > $GLOBALS['max_img_size']) {
            header("Location: scrivi-la-tua-storia.php?img_size_error=1");
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
            header("Location: scrivi-la-tua-storia.php?img_upload_error=1");            
        }

        return $path_image;
    }
?>