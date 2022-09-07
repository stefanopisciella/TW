<?php
    require "include/template2.inc.php"; 
    require 'include/utils_dbms.php';
    require "include/dbms_ops.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/scrivi-articolo";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }
    
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/scrivi-articolo-admin.html");

            // INIZIO injection categorie articolo
            $categorie_articolo = new Template("skins/categorie-blog.html");

            $query = "SELECT nome FROM categoria WHERE tipo='articolo';";
     
            try {
                $oid = $mysqli->query($query);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }
     
            while($row = mysqli_fetch_array($oid)) {
             
                $categorie_articolo->setContent("nome_categoria", $row['nome']);
     
            }

            // query inserimento articolo
        
            $item->setContent("categorie_articolo", $categorie_articolo->get());
            // FINE injection categorie articolo

    
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
?>

