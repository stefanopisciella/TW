<?php

    function formatta_data_stringhe($data) {
        // assumendo una stringa che rappresenta una data in formato MySQL (AAAA-MM-GG)
        $res = substr($data, 8, "2")."-".substr($data, 5, "2")."-".substr($data, 0, "4");

        return $res;
    }

?>