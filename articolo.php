<?php
    require "include/template2.inc.php";
    require "include/dbms.inc.php";
    require "include/php-utils/varie.php";

    global $mysqli;

    $head = new Template("skins/frame-public.html");
    $articolo = new Template("skins/articolo.html");

    // injection informazioni articolo selezionato

    // estrazione informazioni da DB
    $id_articolo = $_GET['art'];

    $query_info = "SELECT titolo, contenuto, autore, `data`, categoria, `path` FROM articolo WHERE ID='{$id_articolo}';";

    try {
        $oid = $mysqli->query($query_info);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $info_articolo = $oid->fetch_all(MYSQLI_ASSOC);

    $articolo->setContent("img", $info_articolo[0]['path']);
    $articolo->setContent("categoria", $info_articolo[0]["categoria"]);
    $articolo->setContent("autore", $info_articolo[0]["autore"]);
    // formattazione della data in formato italiano
    $articolo->setContent("data", formatta_data_stringhe($info_articolo[0]["data"]));
    $articolo->setContent("titolo", $info_articolo[0]["titolo"]);
    $articolo->setContent("contenuto", $info_articolo[0]["contenuto"]);

    // injection dei tags
    $query_tags = "SELECT nome FROM tag JOIN articolo_tag ON tag.ID = ID_tag AND ID_articolo='{$id_articolo}';";

    try {
        $oid = $mysqli->query($query_tags);
    }
    catch (Exception $e) {
        throw new Exception("{$mysqli->errno}");
    }

    $tags = new Template("skins/tags-articolo.html");

    while($row = mysqli_fetch_array($oid)) {
        $tags->setContent("tag", $row['nome']);
    }

    $articolo->setContent("tags_articolo", $tags->get());

    // injection contenuto blog nel frame public
    $head->setContent("contenuto", $articolo->get());
    
    $head->close();
?>