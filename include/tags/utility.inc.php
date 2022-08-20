<?php

    class utility extends taglibrary {

        function dummy() {}

        function notify($name, $data, $pars) {

            switch($data) {

                case "00":
                    $msg = "La transazione è andata a buon fine";
                    $class = "alert alert-success";
                    break;
                case "10":
                    $msg = "Attenzione: Si è verificato un errore";
                    $class = "alert alert-danger";
                    break;
                case "11":
                    $msg = "Attenzione: l'aggioramento non è andato a buon fine!";
                    $class = "alert alert-danger";
                    break;
                default:
                    $msg = "";
                    $class = "hidden_notification";
                    break;

            }

            $result ="<div class=\"{$class}\"><button class=\"close\" data-dismiss=\"alert\"></button>{$msg}. </div>";

            return $result;

        }

        function show($name, $data, $pars) {
            require "include/dbms.inc.php";
            
            $main = new Template("skins/slider-home.html");

            // query per selezionare foto SLIDER HOME
            $oid = $mysqli->query("SELECT `path` FROM immagine where ID_cane is null and ID_articolo is null;");

            if (!$oid) {
                echo "Error {$mysqli->errno}: {$mysqli->error}"; exit;
            } 

            $data = $oid->fetch_all(MYSQLI_ASSOC);

            // query per selezionare titolo e sottotitolo
            // lunghezza titolo 50 caratteri, sottotitolo 200
            $oid = $mysqli->query("SELECT titolo, sottotitolo FROM slider_home;");

            if (!$oid) {
                echo "Error {$mysqli->errno}: {$mysqli->error}"; exit;
            } 

            $titoli = $oid->fetch_all(MYSQLI_ASSOC);

            // inserimento titoli, sottotitoli e immagini alle slides
            // supponendo che il numero di immagini per lo slider = numero titoli/sottotitoli
            for ($i = 0; $i < sizeof($data); $i++) {
                $main->setContent("path", $data[$i]['path']);
                $main->setContent("titolo", $titoli[$i]['titolo']);
                $main->setContent("sottotitolo", $titoli[$i]['sottotitolo']);
            }
            
            return $main->get();
        }

        function report($name, $data, $pars) {

            global $mysqli;

            $report = new Template("skins/webarch/dtml/report.html");
            $report->setContent("name", $pars['name']);

            $oid = $mysqli->query("SELECT {$pars['fields']} FROM {$pars['table']}");
            if (!$oid) {
                // error
            }
            do {
                $data = $oid->fetch_assoc();
                if ($data) {
                    foreach($data as $key => $value) {
                        $report->setContent($key, $value);
                    }
                }

            } while ($data);

            return $report->get();
        }
    }
?>