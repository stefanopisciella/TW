<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/utils_dbms.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/dettaglio-articolo";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (isset($_GET['art'])) {
            $main = new Template("skins/frame-private.html");
            $item = new Template("skins/dettaglio-articolo.html");

            $modifica = isset($_GET['mod']);

            // injection informazioni articolo selezionato
            $id_articolo = $_GET['art'];

            $query = "SELECT titolo, contenuto, categoria, `path` AS img FROM articolo WHERE ID='{$id_articolo}';";

            try {
                $oid = $mysqli->query($query);
            } catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            $info_articolo = $oid->fetch_all(MYSQLI_ASSOC);

            $contenuto = $info_articolo[0]['contenuto'];

            $item->setContent("titolo", $info_articolo[0]['titolo']);
            $item->setContent("contenuto", $contenuto);
            $item->setContent("categoria", $info_articolo[0]['categoria']);
            $item->setContent("img", $info_articolo[0]['img']);

            $query = "SELECT nome FROM tag JOIN articolo_tag ON tag.ID=articolo_tag.ID_tag AND ID_articolo='{$id_articolo}'";

            try {
                $oid = $mysqli->query($query);
            } catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            //$tags = $oid->fetch_all(MYSQLI_ASSOC);

            // costruisco la stringa con i tags separati da virgola
            $tags = "";

            while($row = mysqli_fetch_array($oid)) {
                $tags = $tags.$row['nome'].",";
            }

            $tags = substr($tags, 0, -1);

            $item->setContent("tags", $tags);
    
            $main->setContent("nome_cognome", initialize_frame());

            $not = new Template("skins/notifiche.html");

            $notifiche = notifiche();

            foreach($notifiche as $notifica) {
                $not->setContent("nome", $notifica['nome']);
                $not->setContent("anteprima", $notifica['anteprima']);
            }

            $main->setContent("notifiche", $not->get());
            
            // injection dell'id dell'articolo al pulsante di eliminazione dell'articolo
            $item->setContent("id-articolo", $_GET['art']);

            $main->setContent("contenuto", $item->get());
            $main->close(); 
        }

        // INIZIO gestione eliminazione di un'articolo
        if(isset($_GET['delete_art']) && is_numeric($_GET['delete_art']) && $_GET['delete_art'] > 0 ){
            // caso in cui l'admin preme sul pulsante elimina 
            
            $id_art = (int) $_GET['delete_art'];

            try {
                delete_query('articolo', $id_art);
                header("Location: blog-admin.php?success=1");
                exit;
            } catch (Exception $e){
                echo $e;
            }
        }
        // FINE gestione eliminazione di un'articolo
    }
?>
