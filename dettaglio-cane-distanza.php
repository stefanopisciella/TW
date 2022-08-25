<?php
    require "include/dbms.inc.php";
    require "frame-public.php";
    require "include/utils_dbms.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['importo']) && isset($_POST['cadenza'])) {
            if(!isset($_SESSION['user_id'])) {
                // caso in cui l'utente non è loggato ==>non può effettuare l'adozione a 
                // distanza. Pertanto C viene reindirizzato alla login
                header("Location: login.php");
                exit;   
            } else {
                // caso in cui l'utente è loggato
                $user_id = $_SESSION['user_id']; 
                $id_cane = $_POST['id'];
                $importo = $_POST['importo'];
                $cadenza = $_POST['cadenza']; 
                $actual_date = date("Y/m/d");

                $adozione = [$user_id, $id_cane, $cadenza, "'".$actual_date."'", $importo];

                try {
                    insert_query('adozione_distanza', $adozione);
                    header("Location: operation-success.php?");
                } catch (Exception $e){
                    echo $e;
                }
            }
        
        }
    } else {
        // injection informazioni cane
        $id_cane = $_GET['id'];
        $query_info_cane = "SELECT nome, presentazione, sesso, eta, razza FROM cane WHERE ID = '{$id_cane}';";

        try {
            $oid = $mysqli->query($query_info_cane);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        $info_cane = $oid->fetch_all(MYSQLI_ASSOC);

        $dettaglio_cane->setContent("nome", $info_cane[0]["nome"]);
        $dettaglio_cane->setContent("presentazione", $info_cane[0]["presentazione"]);
        $dettaglio_cane->setContent("sesso", $info_cane[0]["sesso"]);
        // sistemazione stringa età
        $eta = $info_cane[0]['eta'];
        if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
        else $eta = substr($eta, 0, -1)." mesi";
        $dettaglio_cane->setContent("eta", $eta);
        $dettaglio_cane->setContent("razza", $info_cane[0]["razza"]);
    
        $head->setContent("contenuto", $dettaglio_cane->get());
    
        $head->close();
    }
?>