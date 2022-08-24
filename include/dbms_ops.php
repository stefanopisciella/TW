<?php

    require "dbms.inc.php";

    /**
     * Funzione per la gestione della query di login.
     * @param String $identifier identificatore, può essere nickname o email
     * @param String $password
     * @return int ID utente in caso di successo, -1 altrimenti o codice errore SQL
     */
    function login_query($identifier, $password) {

        global $mysqli;
        $query = "SELECT ID FROM utente WHERE (nickname = '{$identifier}' OR email = '{$identifier}') AND passwrd = '{$password}';";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            
            return -1;
        }

        $rows = $oid->fetch_all(MYSQLI_ASSOC);
        foreach($rows as $row) {
            return $row["ID"];
        }

        return -1;

    }

    /**
     * Funzione che restituisce, dato l'id di un utente, a quale gruppo appartiene
     */
    function get_group($id_utente) {

        global $mysqli;
        $query = "SELECT ID_gruppo as gruppo FROM utente AS u JOIN user_has_group AS uhg ON u.ID = uhg.ID_utente AND u.ID = {$id_utente};";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("errno: {$mysqli->errno}");
        }

        $rows = $oid->fetch_all(MYSQLI_ASSOC);

        return $rows[0]["gruppo"];
        
    }


    /**
     * Funzione per il controllo degli accessi agli script, in base al gruppo
     * @param String $nome_script
     * @return boolean true se accedibile, false altrimenti
     */
    function user_group_check_script($id_utente, $nome_script) {

        // estrapolo l'id del servizio associato al nome dello script cercato
        global $mysqli;
        $query = "SELECT ID FROM `service` WHERE script = '{$nome_script}';";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("{$mysqli->errno}");
        }

        $rows = $oid->fetch_all(MYSQLI_ASSOC);

        // lancio eccezione se il nome dato non corrisponde a nessuno script nel DB
        if (empty($rows)) throw new Exception("script inesistente");

        $id_servizio = $rows[0]["ID"];

        // dato l'id dell'utente, estraggo il gruppo a cui appartiene
        $id_gruppo = get_group($id_utente);

        // verifico che l'utente che ne fa richiesta ($id_utente) abbia i permessi per lo script ($id_servizio) richiesto
        $query = "SELECT COUNT(ID) AS count FROM ugroup_has_service WHERE ID_servizio='{$id_servizio}' AND ID_gruppo='{$id_gruppo}';";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("{$mysqli->errno}");
        }

        $rows = $oid->fetch_all(MYSQLI_ASSOC);

        $risultato = $rows[0]["count"];

        if ($risultato == 0) return false;
        else return true;

    }

    /**
     * Funzione che restituisce le informazioni di un utente dato il suo ID
     */
    function get_user($id_utente) {
        global $mysqli;
        $query = "SELECT * FROM utente u WHERE u.ID='{$id_utente}';";

        try {
            $oid = $mysqli->query($query);
        }
        catch (Exception $e) {
            throw new Exception("errno: {$mysqli->errno}");
        }

        $rows = $oid->fetch_all(MYSQLI_ASSOC);

        return $rows[0];
    }

    /**
     * Funzione che restituisce i cani in base ai flitri selezionati
     */
    function get_dogs_filtered($arg) {
        global $mysqli;

        // preparo la query in base ai filtri selezionati
        $query_1 = "SELECT DISTINCT cane.ID, nome, eta, sesso, razza, `path` AS img FROM cane JOIN immagine ON cane.ID = ID_cane AND cane.distanza=false AND ";
        $query_2 =  "GROUP BY nome;";

        $filtri = "";

        foreach($arg as $filtro => $val) {
            if ($val != null) {

                // dato che per la razza mi viene passato l'id, devo cercarne il nome per riusicre a fare la query sulla tabella cane
                // quindi estrapolo, dato l'id, il nome della razza richiesta
                if ($filtro == "razza") {
                    try {
                        $res = mysqli_fetch_array($mysqli->query("SELECT nome FROM razza WHERE ID = '{$val}';"));
                        $razza = $res['nome'];
                    }
                    catch (Exception $e) {
                        throw new Exception("{$mysqli->errno}");
                    }
                    $filtri = $filtri."{$filtro}='{$razza}' AND ";
                }

                else if ($filtro == "eta") {
                    
                }

                // concateno il filtro di cui ho verificato che non sia nullo, quindi richiesto
                else $filtri = $filtri."{$filtro}='{$val}' AND ";
            }
        }

        // rimuovo l'ultimo 'AND' dalla striga $filtri
        $filtri = substr($filtri, 0, -5);
    
        // compongo le stringhe a formare la stringha che rappresenta la query
        $query_cani = $query_1.$filtri.$query_2;

        // eseguo la query
        try {
            return $mysqli->query($query_cani);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }
    }
?>