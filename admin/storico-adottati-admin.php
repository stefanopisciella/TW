<?php
    require "include/template2.inc.php"; 
    require "include/dbms_ops.php";
    require "include/utils_dbms.php";
    require "frame-private.php";

    session_start();
    $nome_script = "admin/storico-adottati";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }

    $_SESSION['previous_page'] = 'storico-adottati';

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/storico-adottati-admin.html");
    $tab_adottati = new Template("skins/tabella-adottati.html"); 

    global $mysqli;

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        // INIZIO injection delle adozioni nella tabella
        $query = "SELECT c.nome as c_n, c.chip as c_c, u.nome as u_n, u.cognome as u_c, u.telefono as u_t, u.email as u_e, r.`data` as r_d, r.ID as r_i
                  FROM richiesta_adozione r join utente u join cane c on(r.ID_utente=u.ID and r.ID_cane=c.ID) 
                  WHERE c.adottato is true;";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            // REMOVE
            // echo $e;
            throw new Exception("errno: {$mysqli->errno}");
        }

        while($row = mysqli_fetch_array($oid)) {
            $tab_adottati->setContent("nome-c", $row['c_n']);
            $tab_adottati->setContent("chip", $row['c_c']);
            $tab_adottati->setContent("nome-u", $row['u_n']);
            $tab_adottati->setContent("cognome", $row['u_c']);
            $tab_adottati->setContent("telefono", $row['u_t']);
            $tab_adottati->setContent("email", $row['u_e']);
            $tab_adottati->setContent("data", $row['r_d']);
            $tab_adottati->setContent("id-adozione", $row['r_i']);
        }
        $item->setContent("adozioni", $tab_adottati->get());
        // FINE injection delle adozioni nella tabella
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