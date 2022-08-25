<?php
    require "include/dbms.inc.php";
    //require "include/dbms_ops.php";
    require "frame-public.php";

    global $mysqli;

    $adozioni = new Template("skins/adozioni.html");

    // injection opzioni razze nel relativo filtro di ricerca
    $opzioni_razza = new Template("skins/opzioni-razza.html");

    $query = "SELECT ID, nome FROM razza";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {
        
        $opzioni_razza->setContent("nome_razza", $row['nome']);
        $opzioni_razza->setContent("id", $row['ID']);

    }

    $adozioni->setContent("opzioni_razza", $opzioni_razza->get());

    // injection cani da visualizzare per pagina
    
    // devo estarre le informazioni sui cani, considerando anche i filtri

    // prendo dall'html i filtri selezionati
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $sesso = $_POST['sesso'];
        $taglia = $_POST['taglia'];
        $razza = $_POST['razza'];
        $eta = $_POST['eta'];

        $url = "adozioni.php?";

        $informazioni_query_param = array("sesso"=>$sesso, "taglia"=>$taglia, "razza"=>$razza, "eta"=>$eta);

        foreach ($informazioni_query_param as $curr => $val) {
            global $url;
            if ($val != null) {
                $url = $url."{$curr}={$val}&";
            }
        }

        // elimino '&' alla fine della stringa url
        $url = substr($url, 0, -1);
        echo $url;

        // reindirizzamento e aggiustamento parametri di query
        // adozioni.php?s=.$sesso.&
        header('location: '.$url);
        exit;
    }

    // no filtri
    else {

        // verifico cosa sia settato nella query string
        $sesso = null;
        $taglia = null;
        $razza = null;
        $eta = null;

        if(isset($_GET['sesso'])) {global $sesso; $sesso = $_GET['sesso'];}
        if(isset($_GET['taglia'])) {global $taglia; $taglia = $_GET['taglia'];}
        if(isset($_GET['razza'])) {global $razza; $razza = $_GET['razza'];}
        if(isset($_GET['eta'])) {global $eta; $eta = $_GET['eta'];}

        $arg = array("sesso"=>$sesso, "taglia"=>$taglia, "razza"=>$razza, "eta"=>$eta);

        $no_filtri = $sesso == null && $taglia == null && $razza == null && $eta == null;
        
        // caso senza filtri
        if ($no_filtri) {
            // preparo la query nel caso più semplice, cioè quello in cui non ci sono filtri
            $query_cani = "SELECT DISTINCT cane.ID, nome, eta, sesso, razza, chip, `path` AS img FROM cane JOIN immagine ON cane.ID = ID_cane AND cane.distanza=false AND cane.adottato = FALSE GROUP BY nome;";

            // eseguo la query
            try {
                $oid2 = $mysqli->query($query_cani);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }
        }

        // caso con i filtri (query parametrica)
        else {
            $oid2 = get_dogs_filtered($arg);
        }

        // injection numero pagine: ceiling(num cani / num cani per pagina) (parte intera superiore)
        $num_cani = $oid2->num_rows;
        // si fa visualizzare 9 cani per pagina
        $num_pagine = ceil($num_cani/6);

        $page_shifter = new Template("skins/page-shifter.html");

        // injection numero pagine
        for ($i = 1; $i <= $num_pagine; ) {

            // se non ci sono filtri devo inserire nell'HTML dinamico solo l'informazione relativa al numero di pagina
            if ($no_filtri) {
                $page_shifter->setContent("page_no", $i++);
                $page_shifter->setContent("filtri", "");
            }
            // se invece ci sono i filtri, devo inserire anche l'informazione relativa
            else {
                $page_shifter->setContent("page_no", $i++);
                global $arg;
                $url_filtri = "&";
                foreach ($arg as $curr => $val) {
                    if ($val != null) {
                        $url_filtri = $url_filtri."{$curr}={$val}&";
                    }
                }
                $url_filtri = substr($url_filtri, 0, -1);
                $page_shifter->setContent("filtri", $url_filtri);
            }
            
        }

        $adozioni->setContent("page_shifter", $page_shifter->get());

        // injection cani per pagina corrente
        $singolo_cane = new Template("skins/singolo-cane.html");
        // tengo memorizzati i cani (divisi per pagina) in un array: posizione 1 -> pagina 1, ..., posizione n -> pagina n

        $cani_paginati = array();

        // riempio l'array
        for ($i = 0; $i < $num_pagine; $i++) {
            $pag_corrente = array();
            for ($j = 0; $j < 6; $j++) {
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
                $singolo_cane->setContent("CHIP", $cane['chip']);

                // sistemazione stringa età
                $eta = $cane['eta'];
                if (substr($eta, -1) == 'a') $eta = substr($eta, 0, -1)." anni";
                else $eta = substr($eta, 0, -1)." mesi";
                $singolo_cane->setContent("eta", $eta);

                $singolo_cane->setContent("sesso", $cane['sesso']);
            }

            $adozioni->setContent("singolo_cane", $singolo_cane->get());
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

            $adozioni->setContent("singolo_cane", $singolo_cane->get());
        }

        // injection adozioni.html contenuto del frame-public
        $head->setContent("contenuto", $adozioni->get());
        
        $head->close();

    }

    
    
?>