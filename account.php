<?php
    require "frame-public.php";
    require "include/dbms.inc.php";
    require "include/php-utils/varie.php";

    global $mysqli;

    $nome_script = "account";
    if(!isset($_SESSION['user_id']) ||
    user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        // se il client non è loggato, viene reindirizzato alla home
        header("Location: home.php");
        exit;   
    }

    $user_id = $_SESSION['user_id'];

    $item = new Template("skins/account.html");

    // injection dati dell'utente
    $query = "SELECT nome, cognome, email, telefono FROM utente WHERE ID = '{$user_id}';";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $info = $oid->fetch_all(MYSQLI_ASSOC);

    $nome_cognome = $info[0]['nome']." ".$info[0]['cognome'];

    $item->setContent("nome_cognome", $nome_cognome);
    $item->setContent("email", $info[0]['email']);
    $item->setContent("telefono", $info[0]['telefono']);

    // injection adottati
    $cani_adottati = new Template("skins/cani-adottati.html");

    $query_adottati = "SELECT cane.nome, `data`, documento, `path` AS img FROM richiesta_adozione JOIN cane ON ID_cane = cane.ID AND ID_utente = '{$user_id}' JOIN immagine ON immagine.ID_cane = cane.ID GROUP BY cane.nome;";

    try {
        $oid = $mysqli->query($query_adottati);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {

        $cani_adottati->setContent("nome", $row['nome']);
        $cani_adottati->setContent("data_adozione", formatta_data_stringhe($row["data"]));
        $cani_adottati->setContent("img", $row['img']);
    }

    $item->setContent("cani_adottati", $cani_adottati->get());

    // injection preferiti

    $singolo_cane = new Template("skins/singolo-cane.html");

    $query_cani = "SELECT DISTINCT cane.ID, nome, eta, sesso, razza, chip, `path` AS img FROM cane JOIN immagine ON cane.ID = ID_cane JOIN preferiti ON cane.ID = preferiti.ID_cane AND preferiti.ID_utente='{$user_id}' GROUP BY cane.ID;";

    // eseguo la query
    try {
        $oid = $mysqli->query($query_cani);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {

        $singolo_cane->setContent("img", $row['img']);
        $singolo_cane->setContent("id", $row['ID']);
        $singolo_cane->setContent("nome", $row['nome']);
        $singolo_cane->setContent("razza", $row['razza']);
        $singolo_cane->setContent("CHIP", $row['chip']);

        // sistemazione stringa età
        $eta = $row['eta'];
        if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
        else $eta = substr($eta, 0, -1)." mesi";
        $singolo_cane->setContent("eta", $eta);

        $singolo_cane->setContent("sesso", $row['sesso']);
    }

    $item->setContent("preferiti", $singolo_cane->get());

    // injection contenuto
    $head->setContent("contenuto", $item->get());
    $head->close();
?>