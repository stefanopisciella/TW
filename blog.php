<?php
    require "include/dbms.inc.php";
    require "include/php-utils/varie.php";
    require "frame-public.php";

    global $mysqli;

    $blog = new Template("skins/blog.html");

    // injection categorie
    $categorie = new Template("skins/categorie.html");

    $query_categorie = "SELECT ID, nome FROM categoria WHERE tipo='articolo';";

    try {
        $oid = $mysqli->query($query_categorie);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    while($row = mysqli_fetch_array($oid)) {
        
        $categorie->setContent("id", $row['ID']);
        $categorie->setContent("nome_categoria", $row['nome']);
    }

    $blog->setContent("categorie", $categorie->get());

    // injection 3 articoli pagina principale del blog
    $articoli_facciata = new Template("skins/articolo-facciata.html");


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $url = $_POST['keyword'];
        header('location: blog.php?keyword='.$url);
        exit;
    }

    // l'utente accede alla pagina con metodo GET
    else {
        if(isset($_GET['keyword'])) {

            $parola_chiave = $_GET['keyword'];
    
            $query_keyword = "SELECT articolo.ID, titolo, contenuto, autore, `data`, categoria, `path` FROM articolo JOIN articolo_tag ON articolo.ID = ID_articolo JOIN tag ON tag.ID = ID_tag WHERE titolo LIKE '%{$parola_chiave}%' OR tag.nome LIKE '%{$parola_chiave}%' GROUP BY articolo.ID;";

            try {
                $oid = $mysqli->query($query_keyword);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }

            if ($oid->num_rows == 0) {
                $not_found = new Template("skins/not-found.html");
                $blog->setContent("articoli-facciata", $not_found->get());
            }

            else {
                $res = $oid->fetch_all(MYSQLI_ASSOC);
    
                // injection nella pagina principale del blog
                foreach($res as $articolo) {
        
                    $articoli_facciata->setContent("id", $articolo["ID"]);
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
            }
        }
    
        // CASO IN CUI VIENE SPECIFICATA UNA CATEGORIA (FILTRO ARTICOLI SU CATEGORIA)
        if (isset($_GET['cat'])) {
    
            $id_categoria = $_GET['cat'];
    
            // estrazione informazioni 3 articoli da presentare
    
            $query = "SELECT ID, titolo, contenuto, autore, `data`, categoria, `path` FROM articolo WHERE ID_categoria='{$id_categoria}';";
    
            try {
                $oid = $mysqli->query($query);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }
    
            $info_articoli = $oid->fetch_all(MYSQLI_ASSOC);
    
            // injection nella pagina principale del blog
            foreach($info_articoli as $articolo) {
    
                $articoli_facciata->setContent("id", $articolo["ID"]);
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
    
        }
    
        // CASO IN CUI NON VIENE SPECIFICATA UNA CATEGORIA
        else {
    
            // estrazione informazioni 3 articoli da presentare
    
            $query = "SELECT ID, titolo, contenuto, autore, `data`, categoria, `path` FROM articolo ORDER BY RAND() LIMIT 3;";
    
            try {
                $oid = $mysqli->query($query);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }
    
            $info_articoli = $oid->fetch_all(MYSQLI_ASSOC);
    
            // injection nella pagina principale del blog
            foreach($info_articoli as $articolo) {
    
                $articoli_facciata->setContent("id", $articolo["ID"]);
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
    
        }
    }

    // RICERCA PER PAROLA CHIAVE
    
    // injection articoli recenti (2)
    $articoli_recenti = new Template("skins/articoli-recenti.html");

    $query = "SELECT ID, titolo, autore, `data`, `path` FROM articolo ORDER BY `data` DESC LIMIT 2;";

    try {
        $oid = $mysqli->query($query);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $articoli_rec = $oid->fetch_all(MYSQLI_ASSOC);

    // injection di ogni articolo recente nello spazio apposito
    foreach($articoli_rec as $articolo) {

        $articoli_recenti->setContent("id", $articolo["ID"]);
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