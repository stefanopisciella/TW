<?php
    require "include/dbms.inc.php";
    require "frame-public.php";

    // per il redirect allo script "adozioni-distanza" una volta effettuato il login
    $_SESSION['previous_page'] = 'adozioni-distanza';

    $adozioni_distanza = new Template("skins/adozioni-a-distanza.html");

    $query_cani = "SELECT DISTINCT cane.ID, nome, eta, sesso, razza, `path` AS img FROM cane JOIN immagine ON cane.ID = ID_cane AND cane.distanza=true GROUP BY nome;";

    // eseguo la query
    try {
        $oid2 = $mysqli->query($query_cani);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    // injection numero pagine: ceiling(num cani / num cani per pagina) (parte intera superiore)
    $num_cani = $oid2->num_rows;
    // si fa visualizzare 9 cani per pagina
    $num_pagine = ceil($num_cani/3);

    $page_shifter = new Template("skins/page-shifter-distanza.html");

    // injection numero pagine
    for ($i = 1; $i <= $num_pagine; ) {
        $page_shifter->setContent("page_no", $i++);
    }

    $adozioni_distanza->setContent("page_shifter", $page_shifter->get());

    // injection cani per pagina corrente
    $singolo_cane = new Template("skins/singolo-cane-distanza.html");
    // tengo memorizzati i cani (divisi per pagina) in un array: posizione 1 -> pagina 1, ..., posizione n -> pagina n

    $cani_paginati = array();

    // riempio l'array
    for ($i = 0; $i < $num_pagine; $i++) {
        $pag_corrente = array();
        for ($j = 0; $j < 3; $j++) {
            $cane_corrente = $oid2->fetch_assoc();
            if ($cane_corrente != null) {
                array_push($pag_corrente, $cane_corrente);
            }
        }
        array_push($cani_paginati, $pag_corrente);
    }

    // se la $_GET['page'] NON è settata, mostro i cani della prima pagina, altrimenti quelli della pagina selezionata
    if (! isset($_GET['page'])) {
        // mostra cani prima pagina
        $cani_prima_pagina = $cani_paginati[0];

        foreach($cani_prima_pagina as $cane) {
            $singolo_cane->setContent("img", $cane['img']);
            $singolo_cane->setContent("id", $cane['ID']);
            $singolo_cane->setContent("nome", $cane['nome']);
            $singolo_cane->setContent("razza", $cane['razza']);

            // sistemazione stringa età
            $eta = $cane['eta'];
            if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
            else $eta = substr($eta, 0, -1)." mesi";
            $singolo_cane->setContent("eta", $eta);

            $singolo_cane->setContent("sesso", $cane['sesso']);
        }

        $adozioni_distanza->setContent("singolo_cane_distanza", $singolo_cane->get());
    }

    // mostra cani pagina selezionata
    else {
        
        $cani_pagina_sel = $cani_paginati[$_GET['page']-1];

        foreach($cani_pagina_sel as $cane) {
            $singolo_cane->setContent("img", $cane['img']);
            $singolo_cane->setContent("id", $cane['ID']);
            $singolo_cane->setContent("nome", $cane['nome']);
            $singolo_cane->setContent("razza", $cane['razza']);

            // sistemazione stringa età
                $eta = $cane['eta'];
                if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
                else $eta = substr($eta, 0, -1)." mesi";
                $singolo_cane->setContent("eta", $eta);
                
            $singolo_cane->setContent("sesso", $cane['sesso']);
        }

        $adozioni_distanza->setContent("singolo_cane_distanza", $singolo_cane->get());
    }

    $head->setContent("contenuto", $adozioni_distanza->get());
    
    $head->close();
?>