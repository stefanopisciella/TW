<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    session_start();
    $nome_script = "admin/donazioni";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;  
    }
    
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/donazioni.html");
    
    $tab_ado = new Template("skins/tabella-adozioni.html");
    $tab_don = new Template("skins/tabella-donazioni.html");

    global $mysqli;

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        // INIZIO injection delle informazioni delle adozioni a distanza
        $query = "SELECT a.importo as a_i, a.cadenza as a_c, c.nome as c_n, c.chip as c_c, u.nome as u_n, u.cognome as u_c, u.email as u_e
                  FROM adozione_distanza a join utente u join cane c on(a.ID_utente=u.ID and a.ID_cane=c.ID);"; 

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            throw new Exception("errno: {$mysqli->errno}");
        }

        while($row = mysqli_fetch_array($oid)) {
            $tab_ado->setContent("importo", $row['a_i']);
            
            // formattazione della cadenza
            $cadenza = $row['a_c'];
            if($cadenza == 1) {
                $cadenza .= " mese"; 
            } else {
                $cadenza .= " mesi"; 
            }

            $tab_ado->setContent("cadenza", $cadenza);
            $tab_ado->setContent("nome-c", $row['c_n']);
            $tab_ado->setContent("chip", $row['c_c']);
            $tab_ado->setContent("nome-u", $row['u_n']);
            $tab_ado->setContent("cognome-u", $row['u_c']);
            $tab_ado->setContent("email", $row['u_e']);
        }
        $item->setContent("tab-ado", $tab_ado->get());
        // FINE injection delle informazioni delle adozioni a distanza

        // INIZIO injection delle informazioni delle donazioni
        $query = "SELECT importo, email, `data` as d FROM donazione;"; 

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            // REMOVE
            echo $e;
            throw new Exception("errno: {$mysqli->errno}");
        }

        while($row = mysqli_fetch_array($oid)) {
            $tab_don->setContent("importo", $row['importo']);
            $tab_don->setContent("email", $row['email']);
            $tab_don->setContent("data", $row['d']);
        }
        $item->setContent("tab-don", $tab_don->get());
        // FINE injection delle informazioni delle donazioni
    }
    
    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>

