<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";
    require "include/php-utils/varie.php";

    global $mysqli;

    $head = new Template("skins/frame-public.html");
    $blog = new Template("skins/blog.html");

    // injection 3 articoli pagina principale del blog
    $articoli_facciata = new Template("skins/articolo-facciata.html");

    // estrazione informazioni 3 articoli casuali, da presentare

    $query = "SELECT titolo, contenuto, autore, `data`, categoria, `path` FROM articolo JOIN immagine ON articolo.ID = ID_articolo ORDER BY RAND() LIMIT 3;";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $info_articoli = $oid->fetch_all(MYSQLI_ASSOC);

    // injection nella pagina principale del blog
    foreach($info_articoli as $articolo) {
        $articoli_facciata->setContent("img", $articolo["path"]);
        $articoli_facciata->setContent("categoria", $articolo["categoria"]);
        $articoli_facciata->setContent("autore", $articolo["autore"]);

        // formattazione della data in formato italiano
        $articoli_facciata->setContent("data", formatta_data_stringhe($articolo["data"]));

        $articoli_facciata->setContent("titolo", $articolo["titolo"]);

        // si modifica la stringa relativa al contenuto da visualizzare, trocandola ai primi 280 caratteri, facendo seguire tre punti di sospensione
        $anteprima_testo = substr($articolo["contenuto"], 0, 280)." ...";

        $articoli_facciata->setContent("testo", $anteprima_testo);
    }

    $blog->setContent("articoli-facciata", $articoli_facciata->get());

    // injection articoli recenti (2)
    $articoli_recenti = new Template("skins/articoli-recenti.html");

    $query = "SELECT titolo, autore, `data`, `path` FROM articolo JOIN immagine ON articolo.ID = ID_articolo ORDER BY `data` DESC LIMIT 2;";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $articoli_rec = $oid->fetch_all(MYSQLI_ASSOC);

    // injection di ogni articolo recente nello spazio apposito
    foreach($articoli_rec as $articolo) {
        $articoli_recenti->setContent("img", $articolo["path"]);
        $articoli_recenti->setContent("autore", $articolo["autore"]);

        // formattazione della data in formato italiano
        $articoli_recenti->setContent("data", formatta_data_stringhe($articolo["data"]));

        $articoli_recenti->setContent("titolo", $articolo["titolo"]);
    }

    // injection articoli recenti nella pagina principale del blog
    $blog->setContent("articoli_recenti", $articoli_recenti->get());

    // injection contenuto blog nel frame public
    $head->setContent("contenuto", $blog->get());
    
    $head->close();
?>