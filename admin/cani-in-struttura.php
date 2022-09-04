<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

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
                  WHERE c.adottato=false;" ;

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
            $suffix = substr($row['eta'], -1); 
            $num = substr($row['eta'], 0, 1);  
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
                    $eta = $num . ' anni';
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
    
    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>

