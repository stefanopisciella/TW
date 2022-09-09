<?php
    require "include/dbms.inc.php";
    require "include/php-utils/varie.php";
    require "include/utils_dbms.php";
    require "include/dbms_ops.php";
    require "include/template2.inc.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/scrivi-articolo";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }

    $max_char_title = 100; 
    $max_img_size = 5000000;
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // caso in cui il client carica la pagina con il metodo GET
        $item = new Template("skins/scrivi-articolo-admin.html");
        $head = new Template("skins/frame-private.html");

        // INIZIO injection categorie articolo
        $categorie_articolo = new Template("skins/categorie-blog.html");

        $query = "SELECT nome, ID FROM categoria WHERE tipo='articolo' AND ID!=2;";
     
        try {
            $oid = $mysqli->query($query);
        } catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }
     
        while($row = mysqli_fetch_array($oid)) {
            $categorie_articolo->setContent("nome_categoria", $row['nome']);
            $categorie_articolo->setContent("ID_categoria", $row['ID']);
        }

        $item->setContent("categorie_articolo", $categorie_articolo->get());
        // FINE injection categorie articolo
    
        if (isset ($_GET['empty_fields']) && $_GET['empty_fields'] == 1) {
            $item->setContent("error", "Non tutti i campi sono stanti compilati");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $head->setContent("notifiche", $not->get());

            $head->setContent("contenuto", $item->get());
            $head->setContent("nome_cognome", initialize_frame());
            $head->close();
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

            $head->setContent("notifiche", $not->get());

            $head->setContent("contenuto", $item->get());
            $head->setContent("nome_cognome", initialize_frame());
            $head->close();
            exit; 
        }

        if(isset($_GET['title_out_of_limit']) && $_GET['title_out_of_limit'] == 1){
            $item->setContent("error", "Il titolo non può avere più di $max_char_title caratteri");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $head->setContent("notifiche", $not->get());

            $head->setContent("contenuto", $item->get());
            $head->setContent("nome_cognome", initialize_frame());
            $head->close();
            exit;
        } 

        // visualizzzione di messaggi di errore relativi al caricamento delle immagini
        if(isset($_GET['img_size_error']) && $_GET['img_size_error'] == 1){
            $readble_size = $max_img_size/1000000;
            
            $item->setContent("error", "L'immagine non può avere dimensioni maggiori di $readble_size" . "MB");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $head->setContent("notifiche", $not->get());

            $head->setContent("contenuto", $item->get());
            $head->setContent("nome_cognome", initialize_frame());
            $head->close();
            exit;
        } 

        if (isset ($_GET['img_upload_error']) && $_GET['img_upload_error'] == 1) {
            $item->setContent("error", "Non è stato possibile caricare l'immagine");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $head->setContent("notifiche", $not->get());

            $head->setContent("contenuto", $item->get());
            $head->setContent("nome_cognome", initialize_frame());
            $head->close();
            exit; 
        }

        if (isset ($_GET['wrong_format']) && $_GET['wrong_format'] == 1) {
            $item->setContent("error", "La foto deve avere formato '.png' oppure '.jpg'");

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $head->setContent("notifiche", $not->get());

            $head->setContent("contenuto", $item->get());
            $head->setContent("nome_cognome", initialize_frame());

            $head->close();
            exit; 
        }

        $head->setContent("contenuto", $item->get());

        $head->setContent("nome_cognome", initialize_frame());

        $not = new Template("skins/notifiche.html");

        $notifiche = notifiche();

        foreach($notifiche as $notifica) {
            $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
        }

        $head->setContent("notifiche", $not->get());

        $head->close();
    } else {
        // caso in cui l'utente ha già visionato la pagina e fa "submit" dell'articolo ed
        // eventualmente anche dell'immagine dell'articolo

        $contenuto = $_POST['testo'];
        // $id_utente contiene l'ID dell'admin
        $id_utente = $_SESSION['user_id'];
        $tags = $_POST['tags'];
        // $autore contiene il nickname dell'admin
        $autore = get_user($id_utente)['nickname'];
        $actual_date = date("Y/m/d");
        $id_categoria = $_POST['categoria'];
        $titolo = $_POST['titolo'];

        // controlla che il testo dell'articolo non sia vuoto
        if (!isset($contenuto) || strlen(trim($contenuto)) == 0) {
            // testo vuoto
            header("Location: scrivi-articolo-admin.php?empty_fields=1");
            exit;
        }

        // controlla che il titolo dell'articolo non sia vuoto
        if (!isset($titolo) || strlen(trim($titolo)) == 0)
        {
            // titolo vuoto
            header("Location: scrivi-articolo-admin.php?empty_fields=1");
            exit;
        } else {
            // titolo non vuoto
            $titolo = trim($titolo);
            $titolo = addslashes($titolo);
            // controlla che il numero di caratteri del titolo non superi il limite
            if(strlen($titolo) > $max_char_title)
            {
                header("Location: scrivi-articolo-admin.php?title_out_of_limit=1");
                exit;
            }
        }

        // controlla che la categoria sia stata selezionata
        if (!isset($id_categoria) || (isset($id_categoria) && empty($id_categoria))) {
            // categoria non selezionata
            header("Location: scrivi-articolo-admin.php?empty_select=1");
            exit;
        }

        $path_img = upload_image();

        // INIZIO query per estrarre il nome della categoria dato il suo ID
        $query = "SELECT c.nome as c_n FROM categoria c WHERE c.ID={$id_categoria};";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("errno: {$mysqli->errno}");
        }

        $rows = $oid->fetch_all(MYSQLI_ASSOC);

        $categoria = $rows[0]["c_n"];
        // FINE query per estrarre il nome della categoria dato il suo ID
        $contenuto = addslashes($contenuto);

        $articolo = [$id_utente, $id_categoria, "'".$titolo."'", "'".$contenuto."'", "'".$autore."'", "'".$actual_date."'", "'".$categoria."'", "'".$path_img."'"];
        
        try {
            $id_articolo = insert_query('articolo', $articolo);

            // INIZIO inserimento dei tag dell'articolo nel DB
            $tags_array = split_tags($tags); // split_tags ritorna un array di tags
            
            for($i=0;$i<count($tags_array);$i++) {
                $temp = addslashes($tags_array[$i]);
                $tag = ["'".$temp."'"];
                $id_tag = insert_query("tag", $tag);
                
                // associa il tag al presente articolo
                $articolo_tag = [$id_articolo, $id_tag];
                insert_query('articolo_tag', $articolo_tag);
            }
            // FINE inserimento dei tag dell'articolo nel DB
            
            header('Location: dettaglio-articolo.php?art=' . $id_articolo);
        } catch (Exception $e){
            echo $e;
        }
    }

    function upload_image() {
        // controlla se il client ha caricato o meno un'immagine
        if(isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name']) && file_exists($_FILES['image']['tmp_name'])) {
            // caso in cui l'utente ha caricato un immagine per l'articolo

            // fissa il vincolo di dimensioni per il quale non è possibile caricare immagini con
            // dimensione maggiore ai 5MB
            if ($_FILES["image"]["size"] > $GLOBALS['max_img_size']) {
                header("Location: scrivi-la-tua-storia.php?img_size_error=1");
                exit;
            }
        } else {
            // caso in cui l'utente non ha caricato alcuna immagine per l'articolo
            return "img/blog/default_img.jpg"; // è il path dell'immagine di default
        }
  
        // fissa il vincolo per il quale è consentito caricare soltanto immagini con formato 
        // .png oppure .jpeg
        $imageFileType = $_FILES["image"]["type"];
        if($imageFileType == "image/jpeg") {
            $extension = "jpeg";
        } else {
            if($imageFileType == "image/png") {
                $extension = "png";
            } else {
                // caso in cui una delle immagini ha un formato non consentito
                header("Location: scrivi-la-tua-storia.php?wrong_format=1");
                exit;  
            }
        }
  
        $images_dir = "immagini/";   
        // si assegna all'immagine un nome casuale per garantire l'univocità dei nomi delle
        //  immagini (azione necessaria per evitare errori a livello di filesystem)
        $path_image = $images_dir . "img_" . random_int(1, 10000) . "." . $extension;
  
        // "tmp_name" è il path dove il server salva temporaneamente il file caricato
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $path_image)) {
            header("Location: scrivi-la-tua-storia.php?img_upload_error=1");            
        }

        return $path_image;
    }

    function split_tags($tags) {
        // controllare che $tags non sia vuoto
        $tags .= ","; 
        $tags_array = array();
        $b = 0; //indice del primo carattere del tag leftmost contenuto in $tags
    
        for($i=0;$i<strlen($tags);$i++) {
            $single_char = substr($tags, $i, 1);
        
            if(strcmp($single_char, ",") == 0) {
                // caso in cui viene individuato un tag
            
                $tag = substr($tags, $b, $i - $b);
                // $b contiene l'indice del primo carattere del tag attuale 
                $b = $i + 1;
                // tolgo gli eventuali spazi presenti a DX e a SX del tag
                $tag = trim($tag);
                
                if(strlen($tag) > 0) {
                    // caso in cui il tag T non è vuoto ==> t è un tag valido 
                    array_push($tags_array, $tag);
                }
            }
        }

        return $tags_array;
    }
?>