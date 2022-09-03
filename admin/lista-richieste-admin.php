<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";

    session_start();
    $nome_script = "admin/lista-richieste";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        echo "Unauthorized";
        exit;   
    }
    
    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/lista-richieste-admin.html");
    $request_tab = new Template("skins/tabella-richieste-adozioni.html"); 

    global $mysqli;

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        // INIZIO query
        $query = "SELECT c.nome as c_n, c.chip as c_c, u.nome as u_n, u.cognome as u_c, u.telefono as u_t, u.email as u_e, r.`data` as r_d
                  FROM richiesta_adozione r JOIN utente u JOIN cane c on(r.ID_utente=u.ID AND r.ID_cane=c.ID)
                  WHERE r.documento is null
                  GROUP BY r.ID;";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            echo $e;
            throw new Exception("errno: {$mysqli->errno}");
        }

        while($row = mysqli_fetch_array($oid)) {
            $request_tab->setContent("nome_cane", $row['c_n']);
            $request_tab->setContent("chip", $row['c_c']);
            $request_tab->setContent("nome_richiedente", $row['u_n']);
            $request_tab->setContent("cognome_richiedente", $row['u_c']);
            $request_tab->setContent("telefono", $row['u_t']);
            $request_tab->setContent("email", $row['u_e']);
            $request_tab->setContent("data", $row['r_d']);
        }
        // FINE query
        $item->setContent("richieste", $request_tab->get());
    
        $main->setContent("contenuto", $item->get());
        $main->close(); 
    }
        
?>