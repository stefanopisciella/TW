<?php

    require "dbms.inc.php";

    function login_query($identifier, $password) {

        global $mysqli;
        $query = "SELECT ID FROM utente WHERE (nickname = '{$identifier}' OR email = '{$identifier}') AND passwrd = '{$password}';";

        $oid = $mysqli->query($query);

        try {
            $mysqli->query($query);
        }
        catch (Exception $e) {
            
            throw new Exception("{$mysqli->errno}");
        }

    }

?>