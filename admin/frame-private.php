<?php

    function initialize_frame() {
        
        global $mysqli;

        $user_id = $_SESSION['user_id'];

        $query = "SELECT nome, cognome FROM utente WHERE ID='{$user_id}';";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        $info = $oid->fetch_all(MYSQLI_ASSOC);
        $nome_cognome = $info[0]['nome']." ".$info[0]['cognome'];

        return $nome_cognome;
    }

    function notifiche() {

        global $mysqli;

        $query = "SELECT nome, messaggio FROM richiesta_info ORDER BY `data` DESC LIMIT 3;";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        $res = array();

        while($row = mysqli_fetch_array($oid)) {
     
            $curr = array("nome"=> $row['nome'], "anteprima"=> substr($row['messaggio'], 0, 21)." ...");
            array_push($res, $curr);
    
        }

        return $res;
    }

?>
