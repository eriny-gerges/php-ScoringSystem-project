<?php

declare(strict_types=1);
const DB_HOST = '127.0.0.1';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'college_competition';
const DB_PORT = 3306;

function get_db(): mysqli
{
    static $conn = null;

    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            $conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            http_response_code(500);
            exit('A database error occurred. Please try again later.');
        }
    }

    return $conn;
}
