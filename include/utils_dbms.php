<?php

    require "dbms.inc.php";

    /**
     * Funzione per formattare nomi di colonna e valori per query (inserimento, modifica), secondo la sintassi SQL
     * @param String[] $valori array di valori (ordinati secondo l'ordine specificato nella tabella SQL) da formattare
     */
    function formatta_valori($valori) {
        
        $len = count($valori);

        // stringa risultato
        $res = "";

        for ($i = 0; $i < $len; $i++) {
            if ($i < $len-1) {
                $res = $res.$valori[$i].",";
            }

            // se il corrente è l'ultimo elemento dell'array, allora non devono appendere la virgola alla fine
            if ($i == $len-1) {
                $res = $res.$valori[$i];
            }
        }

        return $res;
        
    }

    /**
     * Funzione che, dato il nome di una tabella, ne restituisce i nomi delle colonne
     * @param String $nome_tabella
     */
    function colonne($nome_tabella) {

        // query per ottenere nome colonne della tabella specificata
        global $mysqli;
        $oid = $mysqli->query("SELECT `COLUMN_NAME` 
                            FROM `INFORMATION_SCHEMA`.`COLUMNS` 
                            WHERE `TABLE_SCHEMA`='petco' 
                            AND `TABLE_NAME`='{$nome_tabella}';");
        if ($oid) {
            // formatto le colonne in modo tale che possano essere usate direttamente in query
            $colonne = "";
            $len = $oid->num_rows;

            // inizializzo un array vuoto che conterrà i valori da formattare
            $array = [];

            // chiamata per consumare la prima riga, quella dell'ID (non c'è bisogno di fare un inserimento dell'ID)
            $oid->fetch_row();

            // popolo l'array 
            for ($i = 1; $i < $len; $i++) {
                $curr = $oid->fetch_row();
                foreach($curr as $sub_curr) {

                    array_push($array, $sub_curr);

                }
            }

            // ritorno il risultato della formattazione dei valori nell'array
            return formatta_valori($array);
        }

        return null;

    }

    /**
     * Funzione per la costruzione e l'invio di query di inserimento parametrizzate
     * @param String $nome_tabella
     * @param String[] $valori valori da inserire nella tabella
     * @return int 0 per successo, codice errore altrimenti
     */

    function insert_query($nome_tabella, $valori) {

        // dato il nome della tabella, cerco i nomi delle sue colonne, ottenendoli formattati e pronti per la query di inserimento
        $colonne = colonne($nome_tabella);

        // formatto i valori
        $_valori = formatta_valori($valori);
    
        // crea la stringa che rappresenta la query, con nome della tabella e valori passati
        $query = "INSERT INTO {$nome_tabella} ({$colonne}) VALUES ({$_valori});";
        // REMOVE
        // echo $query;

        // mando la query
        global $mysqli;

        try {
            $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("{$mysqli->errno}");
        }
    }

    /**
     * Funzione per la costruzione e l'invio di query di update
     * @param String $nome_tabella
     * @param String[] $colonne colonne da modificare nella tabella specificata
     * @param String[] $valori valori da inserire per aggiornare quelli esistenti
     * @param int $id_condizione ID per la condizione del WHERE
     */
    function update_query($nome_tabella, $colonne, $valori, $id_condizione) {

        // NOTA: si assume che le query di update abbiamo sempre e solo una condizione, dato che in tutti i casi di interesse, abbiamo sempre WHERE ID = <id>

        // creo un nuovo array in cui ogni elemento è una nuova stringa del tipo <nome_colonna_i> = 'valore_per_colonna_i'
        $colonne_valori = [];
        $len = count($colonne);

        // NOTA: si assume che, negli array, in valori[i] ci sia il valore da modificare nella colonna i
        for($i = 0; $i < $len; $i++) {
            $curr = $colonne[$i]."='".$valori[$i]."'";
            array_push($colonne_valori, $curr);
        }

        // formatto (aggiungendo le virgole) e ottengo la stringa da concatenare immediatamente dopo il SET nella query
        $updates = formatta_valori($colonne_valori);
        
        // creo la stringa per la query
        $query = "UPDATE {$nome_tabella} SET {$updates} WHERE ID = {$id_condizione};";

        // mando la query
        global $mysqli;

        try {
            $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("{$mysqli->errno}");
        }

    }

    /**
     * Funzione per la costruzione e l'invio di query di delete
     * @param String $nome_tabella
     * @param int $id_condizione ID per la condizione del WHERE
     */
    function delete_query($nome_tabella, $id_condizione) {

        $query = "DELETE FROM {$nome_tabella} WHERE ID = {$id_condizione}";

        // mando la query
        global $mysqli;
        $oid = $mysqli->query($query);

        try {
            $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("{$mysqli->errno}");
        }

    }

?>