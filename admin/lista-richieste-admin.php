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
        $query = "SELECT c.nome, c.chip, u.nome, u.telefono, u.email, r.`data`
                  FROM richiesta_adozione r JOIN utente u JOIN cane c on(r.ID_utente=u.ID AND r.ID_cane=c.ID)
                  WHERE r.documento is null
                  GROUP BY r.ID;";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            throw new Exception("errno: {$mysqli->errno}");
        }

        while($row = mysqli_fetch_array($oid)) {
            $faq_list->setContent("domanda", $row['domanda']);
            $faq_list->setContent("risposta", $row['risposta']);
            $faq_list->setContent("id", $row['ID']);
        }
        // FINE query
    
    
        $main->setContent("contenuto", $item->get());
        $main->close(); 
    }
        
?>