<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";

    global $mysqli;

    $head = new Template("skins/frame-public.html");
    $blog = new Template("skins/blog.html");

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
        $articoli_facciata->setContent("data", $articolo["data"]);
        $articoli_facciata->setContent("titolo", $articolo["titolo"]);

        // si modifica la stringa relativa al contenuto da visualizzare, trocandola ai primi X caratteri, facendo seguire tre punti di sospensione
        $anteprima = substr($articolo["contenuto"], 0, 280)." ...";

        $articoli_facciata->setContent("testo", $anteprima);
    }

    $blog->setContent("articoli-facciata", $articoli_facciata->get());

    // injection contenuto blog nel frame public
    $head->setContent("contenuto", $blog->get());
    
    $head->close();
?>