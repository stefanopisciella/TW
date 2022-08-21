<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    $head = new Template("skins/frame-public.html");
    $adozioni = new Template("skins/adozioni.html");
    $head->setContent("contenuto", $adozioni->get());
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // se il client non seleziona alcuna opzione in una select, invierà una stringa vuota
        // al server
        $sesso = $_POST['sesso'];
        $taglia = $_POST['taglia'];
        $razza = $_POST['razza'];
        $eta = $_POST['eta'];

        // chiamata al metodo che effettua la ricerca nel DB
        
        
        // REMOVE
        echo isset($_POST['sesso']);
        echo $_POST['sesso'] == "";
    }

    $head->close();
?>