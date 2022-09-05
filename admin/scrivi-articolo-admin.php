<?php
    require "include/template2.inc.php"; 
    require 'include/utils_dbms.php';
    require "include/dbms_ops.php";

    $main = new Template("skins/frame-private.html");
    $item = new Template("skins/scrivi-articolo-admin.html");

            // INIZIO injection categorie articolo
            $categorie_articolo = new Template("skins/categorie_blog.html");

            $query = "SELECT nome FROM categoria WHERE tipo='articolo';";
     
            try {
                $oid = $mysqli->query($query);
            }
            catch (Exception $e) {
                throw new Exception("{$mysqli->errno}");
            }
     
            while($row = mysqli_fetch_array($oid)) {
             
                $categorie_articolo->setContent("nome_categoria", $row['nome']);
     
            }
        
            $item->setContent("categorie_articolo", $categorie_articolo->get());
            // FINE injection categorie articolo

    $main->setContent("contenuto", $item->get());
    $main->close(); 
?>

