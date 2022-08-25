<?php
    $result = "0";

    session_start();

    if (isset($_SESSION['user_id']))
        $result = "1";

    echo $result;