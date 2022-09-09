<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/cani-in-struttura";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }
    
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/cani-in-struttura.html");
    $tab = new Template("skins/tabella_cani.html");

    global $mysqli;

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        // INIZIO injection delle informazioni dei cani presenti in struttura
        $query = "SELECT *
                  FROM cane c
                  WHERE c.adottato=false or c.adottato is null;" ;

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            // REMOVE
            echo $e;
            throw new Exception("errno: {$mysqli->errno}");
        }

        while($row = mysqli_fetch_array($oid)) {
            $tab->setContent("id", $row['ID']);
            $tab->setContent("nome", $row['nome']);
            $tab->setContent("razza", $row['razza']);
            
            // INIZIO formattazione dell'età
            if (strlen($row['eta']) == 3) {
                // caso in cui il cane ha un età a due cifre
                $num = substr($row['eta'], 0, 2);
            } else {
                // caso in cui il cane ha un età con un'unica cifra
                $num = substr($row['eta'], 0, 1);
            }
            
            $suffix = substr($row['eta'], -1, 1); 
            if($suffix == 'a') {
                if($num == 1) {
                    $eta = $num . ' anno';
                } else {
                    $eta = $num . ' anni';
                }
            } else {
                if($num == 1) {
                    $eta = $num . ' mese';
                } else {
                    $eta = $num . ' mesi';
                } 
            }
            $tab->setContent("eta", $eta);
            // FINE formattazione dell'età
            
            $tab->setContent("chip", $row['chip']);
            
            if($row['distanza'] == true) {
                $tab->setContent("distanza", "A distanza");
            } else {
                $tab->setContent("distanza", "Normale");
            }
    
        }
        $item->setContent("tabella-cani", $tab->get());
        // FINE injection delle informazioni dei cani presenti in struttura
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
?>

