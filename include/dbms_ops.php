<?php

    require "dbms.inc.php";

    function login_query($identifier, $password) {

        global $mysqli;
        $query = "SELECT ID FROM utente WHERE (nickname = '{$identifier}' OR email = '{$identifier}') AND passwrd = '{$password}';";

        $oid = $mysqli->query($query);

        if($oid) {
            echo "OK - login";
            return $oid;
        }
        else {
            echo "ERRORE!!";
        }

    }

?>