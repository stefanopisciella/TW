<?php
    require "include/template2.inc.php";
    require 'include/utils_dbms.php';
    require "include/dbms_ops.php";

    session_start();
    $nome_script = "admin/faq";
    if(!isset($_SESSION['user_id']) ||
       user_group_check_script($_SESSION['user_id'], $nome_script) == false) 
    {
        header("Location: error.php");
        exit;   
    }

    $main = new Template("skins/frame-private.html");
    $page = new Template("skins/faq.html");
    $faq_list = new Template("skins/faq-list.html");


    $max_char_domanda = 75;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // caso in cui l'admin ha già visionato la pagina e fa "submit" della faq
        $domanda = $_POST['domanda'];
        $risposta = $_POST['risposta'];
        $categoria = (string) $_POST['categoria'];

        // controlla che la domanda e la risposta non siano vuote
        if (!isset($domanda) || 
            !isset($risposta) ||
            !isset($categoria) ||
            strlen(trim($domanda)) == 0 ||
            strlen(trim($risposta)) == 0 ||
            strlen(trim($categoria)) == 0)
        {
            // faq non valida
            header("Location: faq.php?empty_faq=1");
            exit;
        } else {
            $t_domanda = trim($domanda);
            $t_risposta = trim($risposta);

            // controlla il numero di caratteri della domanda
            if(strlen($t_domanda) > $max_char_domanda)
            {
                header("Location: faq.php?out_of_limit=1");
                exit;
            }

            $faq = ["'".$t_domanda."'", "'".$t_risposta."'", "'".$categoria."'"];

            try {
                insert_query('faq', $faq);
                header("Location: faq.php?success=1");
            } catch (Exception $e){
                
            }
        }
    } else {
        // caso in cui il client carica la pagina con il metodo GET
        
        // INIZIO injection delle categorie delle faq nella select
        $categorie_faq = new Template("skins/categorie-faq.html");

        $query = "SELECT nome FROM categoria WHERE tipo='faq';";
 
        try {
             $oid = $mysqli->query($query);
        } catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }
 
        while($row = mysqli_fetch_array($oid)) {
            $categorie_faq->setContent("nome_categoria", $row['nome']);
        }
 
        $page->setContent("categorie-faq", $categorie_faq->get());
        // FINE injection delle categorie

        // INIZIO injection delle faq
        
        // query che restituisce tutte le FAQ presenti nel sistema
        $query_domande_risposte = "SELECT ID, domanda, risposta FROM faq;";

        try {
            $oid2 = $mysqli->query($query_domande_risposte);
        }
        catch (Exception $e) {
            throw new Exception("{$mysqli->errno}");
        }

        while($row = mysqli_fetch_array($oid2)) {
    
            $faq_list->setContent("domanda", $row['domanda']);
            $faq_list->setContent("risposta", $row['risposta']);
            $faq_list->setContent("id", $row['ID']);
        }
        $page->setContent("faq_list", $faq_list->get());
        // FINE injection delle faq

        // INIZIO gestione eliminazione di una FAQ
        if(isset($_GET['delete_faq']) && is_numeric($_GET['delete_faq']) && $_GET['delete_faq'] > 0 ){
            $id_faq = (int) $_GET['delete_faq'];

            try {
                delete_query('faq', $id_faq);
                header("Location: faq.php");
            } catch (Exception $e){
            
            }
        }
        // FINE gestione eliminazione di una FAQ

        //  INIZIO gestione dei messaggi di errore in caso di compilazione non corretta della form
        if(isset($_GET['empty_faq']) && $_GET['empty_faq'] == 1){
            $page->setContent("faq_error", "Tutti i campi devono essere compilati");
        } 

        if(isset($_GET['out_of_limit']) && $_GET['out_of_limit'] == 1){
            $page->setContent("faq_error", "La domanda non può avere più di $max_char_domanda caratteri");
        } 
        // FINE gestione dei messaggi di errore in caso di compilazione non corretta della form

        $main->setContent("contenuto", $page->get());
        $main->close();
    } 
?>