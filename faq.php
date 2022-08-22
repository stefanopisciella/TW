<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    global $mysqli;

    $head = new Template("skins/frame-public.html");
    $faq = new Template("skins/faq.html");

    // injection categorie (componente outer)
    $query_categorie = "SELECT ID, nome FROM categoria WHERE tipo='faq';";

    try {
        $oid = $mysqli->query($query_categorie);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {

        // devo tenere da parte l'id della categoria di FAQ per collocare opportunamente le domande e le relative risposte
        $nome_cat = $row['nome'];

        // outer injection
        $faq->setContent("id", $row['ID']);
        $faq->setContent("categoria", $row['nome']);

        // inner injection
        // query per ottenere damande e risposte, sulla base della categoria
        $domande_risposte = new Template("skins/domande-risposte.html");

        $query_domande_risposte = "SELECT domanda, risposta FROM faq WHERE categoria ='{$nome_cat}';";

        try {
            $oid2 = $mysqli->query($query_domande_risposte);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        while($row2 = mysqli_fetch_array($oid2)) {
    
            // inner injection
            $domande_risposte->setContent("domanda", $row2['domanda']);
            $domande_risposte->setContent("risposta", $row2['risposta']);
        }

        $faq->setContent("domande_risposte", $domande_risposte->get());
    }


    // injection adozioni.html contenuto del frame-public
    $head->setContent("contenuto", $faq->get());
    
    $head->close();
?>