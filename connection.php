<?php
function connect()
{
    $db_host = "127.0.0.1"; 
    $db_name = "college_competition";
    $db_uname = "root";
    $db_password = "";
    $db_port = "3306";

    $conn = mysqli_connect(
        $db_host,
        $db_uname,
        $db_password,
        $db_name,
        $db_port
    );

    return $conn;
}
