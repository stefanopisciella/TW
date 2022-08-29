<?php
    include "../dbms.inc.php";
    global $mysqli;

    $result = "err";

    session_start();

    if (isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];

        if (isset($_POST['setpref']) && $_POST['setpref'] == "add")
        {
            $id_doggo = isset($_POST['IDD']) ? intval($_POST['IDD']) : null;

            if ($id_doggo != null) {
                $oid = $mysqli->query("SELECT c.ID FROM cane c WHERE c.chip = $id_doggo");
                if ($oid)
                {
                    $oid = $oid->fetch_assoc();
                    $id_doggo = $oid['ID'];

                    $stmt = $mysqli->prepare("INSERT INTO `preferiti`(`ID_utente`, `ID_cane`)
                                                VALUES(?, ?)");
                    $stmt->bind_param("ii", $id, $id_doggo);
                    if ($stmt->execute())
                        $result = "added";
                }
            }
        }
        else if (isset($_POST['setpref']) && $_POST['setpref'] == "rem")
        {
            $id_doggo = isset($_POST['IDD']) ? intval($_POST['IDD']) : null;

            if ($id_doggo != null)
            {
                $oid = $mysqli->query("SELECT c.ID FROM cane c WHERE c.chip = $id_doggo");

                if ($oid)
                {
                    $oid = $oid->fetch_assoc();
                    $id_doggo = $oid['ID'];

                    if ($id_doggo != null) {
                        if ($mysqli->query("DELETE FROM `preferiti`  WHERE `preferiti`.`ID_utente` = $id AND `preferiti`.`ID_cane` = $id_doggo"))
                            $result = "erased";
                    }
                }
            }
        }
    }

    echo $result;
