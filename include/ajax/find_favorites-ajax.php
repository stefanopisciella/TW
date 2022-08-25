<?php
    include "../dbms.inc.php";
    global $mysqli;

    $result = "";

    session_start();

    if (isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];

        $res = $mysqli->query(
            "SELECT DISTINCT c.chip FROM utente u 
                    JOIN preferiti p ON p.ID_utente = $id 
                    JOIN cane c ON c.ID = p.ID_cane");

        if ($res) {
            $data = $res->fetch_all(MYSQLI_ASSOC);
            $i = 0;
            $result = array();
            foreach ($data as $row)
                $result[$i++] = $row['chip'];

            $result = json_encode($result);
        }
    }

    echo $result;